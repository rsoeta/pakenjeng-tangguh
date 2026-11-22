<?php

namespace App\Controllers\Auth;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;

use App\Models\Dtks\AuthModel;
use App\Models\WilayahModel;
use App\Models\Dtks\UsersLoginModel;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use App\Controllers\BaseController;

use App\Libraries\GoogleOAuthService;
use App\Libraries\MailgunService;

class Auth extends BaseController
{
    protected $AuthModel;
    protected $WilayahModel;
    protected $UsersLoginModel;
    protected GoogleOAuthService $googleOAuth;

    public function __construct()
    {
        $this->AuthModel = new AuthModel();
        $this->WilayahModel = new WilayahModel();
        $this->UsersLoginModel = new UsersLoginModel();
        $this->googleOAuth = new GoogleOAuthService();
    }

    public function login()
    {
        $data = [];

        if ($this->request->getPost()) {

            $rules = [
                'email' => 'required|min_length[6]|max_length[50]|valid_email',
                'password' => 'required|min_length[6]|max_length[255]|validateUser[email,password]',
            ];

            $errors = [
                'password' => [
                    'validateUser' => "User atau Password tidak sesuai",
                ],
            ];

            if (!$this->validate($rules, $errors)) {
                session()->setFlashdata('message', [
                    'type' => 'error',
                    'text' => 'User atau Password tidak sesuai!'
                ]);
                return view('dtks/auth/login', [
                    "validation" => $this->validator,
                    "title" => 'Login',
                    'googleOAuthEnabled' => $this->googleOAuth->isConfigured(),
                ]);
            }

            $model = new AuthModel();
            $user = $model->where('email', $this->request->getVar('email'))->first();

            // === 🧩 Validasi Cloudflare Turnstile ===
            $isDev = (ENVIRONMENT === 'development');
            $captchaVerified = false;

            if ($isDev) {
                // 🔹 Skip Turnstile di local/dev
                $captchaVerified = true;
            } else {
                $turnstileResponse = $this->request->getPost('cf-turnstile-response');

                if (!empty($turnstileResponse)) {
                    $secretKey = '0x4AAAAAADhc2-aRqsdZlCENE-iIH2rie7k'; // Ganti dengan secret key milikmu
                    $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $verifyUrl);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, [
                        'secret'   => $secretKey,
                        'response' => $turnstileResponse,
                        'remoteip' => $this->request->getIPAddress()
                    ]);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($curl);
                    curl_close($curl);

                    $responseData = json_decode($response);
                    if (isset($responseData->success) && $responseData->success === true) {
                        $captchaVerified = true;
                    }
                }
            }

            // === 🧩 Jika Turnstile valid (atau dilewati di dev) ===
            if ($captchaVerified) {
                return $this->completeUserLogin($user);
            } else {
                // === Turnstile gagal diverifikasi ===
                session()->setFlashdata('message', [
                    'type' => 'error',
                    'text' => 'Verifikasi keamanan gagal. Silakan coba lagi.'
                ]);
                return redirect()->to('/login')->withInput();
            }
        }

        $data = [
            'title' => 'Sign In',
            'googleOAuthEnabled' => $this->googleOAuth->isConfigured(),
        ];
        return view('dtks/auth/login', $data);
    }

    public function googleRedirect()
    {
        if (!$this->googleOAuth->isConfigured()) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => ENVIRONMENT === 'development'
                    ? 'Login Google belum dikonfigurasi. Lengkapi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file .env.'
                    : 'Login Google belum dikonfigurasi oleh Admin.',
            ]);

            return redirect()->to('/login');
        }

        $state = $this->googleOAuth->generateState();
        $pkce  = $this->googleOAuth->generatePkcePair();

        session()->set([
            'google_oauth_state'         => $state,
            'google_oauth_code_verifier' => $pkce['verifier'],
            'google_oauth_created_at'    => time(),
        ]);

        return redirect()->to($this->googleOAuth->buildAuthorizationUrl($state, $pkce['challenge']));
    }

    public function googleCallback()
    {
        if (!$this->googleOAuth->isConfigured()) {
            $this->clearGoogleOAuthSession();
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Login Google belum dikonfigurasi oleh Admin.',
            ]);

            return redirect()->to('/login');
        }

        $error = $this->request->getGet('error');
        if (is_string($error) && $error !== '') {
            $this->clearGoogleOAuthSession();
            $sanitizedError = preg_replace('/[^a-zA-Z0-9_.-]/', '', $error);
            log_message('warning', 'Google OAuth callback error: ' . $sanitizedError);

            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => $error === 'access_denied'
                    ? 'Login dengan Google dibatalkan.'
                    : 'Autentikasi Google gagal. Silakan coba lagi.',
            ]);

            return redirect()->to('/login');
        }

        $code          = (string) ($this->request->getGet('code') ?? '');
        $returnedState = (string) ($this->request->getGet('state') ?? '');

        $expectedState = (string) (session()->get('google_oauth_state') ?? '');
        $codeVerifier  = (string) (session()->get('google_oauth_code_verifier') ?? '');
        $createdAt     = (int) (session()->get('google_oauth_created_at') ?? 0);

        $this->clearGoogleOAuthSession();

        if (
            $code === ''
            || $returnedState === ''
            || $expectedState === ''
            || $codeVerifier === ''
            || $createdAt <= 0
            || (time() - $createdAt) > 600
            || !hash_equals($expectedState, $returnedState)
        ) {
            log_message('warning', 'Google OAuth callback rejected due to invalid or expired state/PKCE.');
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Sesi login Google tidak valid atau telah kedaluwarsa. Silakan coba lagi.',
            ]);

            return redirect()->to('/login');
        }

        try {
            $googleUser = $this->googleOAuth->authenticateCallback($code, $codeVerifier);
        } catch (\Throwable $e) {
            log_message('error', 'Google OAuth callback verification error: ' . $e->getMessage());
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Gagal memverifikasi akun Google. Silakan coba lagi.',
            ]);

            return redirect()->to('/login');
        }

        $email = strtolower(trim((string) ($googleUser['email'] ?? '')));
        if ($email === '' || ($googleUser['email_verified'] ?? false) !== true) {
            log_message('warning', 'Google OAuth rejected unverified or empty email.');
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Email akun Google Anda belum terverifikasi.',
            ]);

            return redirect()->to('/login');
        }

        $user = $this->AuthModel->findByWhitelistedEmail($email);
        if (!$user) {
            log_message('warning', 'Google OAuth rejected non-whitelisted email: ' . $email);
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => "Email {$email} tidak terdaftar dalam whitelist akun SINDEN. Silakan hubungi Admin.",
            ]);

            return redirect()->to('/login');
        }

        return $this->completeUserLogin($user);
    }

    private function clearGoogleOAuthSession(): void
    {
        session()->remove([
            'google_oauth_state',
            'google_oauth_code_verifier',
            'google_oauth_created_at',
        ]);
    }

    private function completeUserLogin(array $user)
    {
        if ((int) ($user['status'] ?? 0) !== 1) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'User Non-Aktif, Silakan hubungi Admin!',
            ]);

            return redirect()->to('/login');
        }

        if (empty($user['role_id'])) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Akun belum memiliki role akses. Silakan hubungi Admin!',
            ]);

            return redirect()->to('/login');
        }

        $redirectUrl = session()->get('redirectUrl');

        session()->regenerate(true);

        // 🚀 1. BUAT TOKEN ACAK SEBAGAI KUNCI SESI
        $sessionToken = bin2hex(random_bytes(32));

        // Buat session login user bawaan
        $this->setUserSession($user);

        // 🚀 2. INJEKSI TOKEN KE DALAM SESSION AKTIF BROWSER INI
        session()->set('session_token', $sessionToken);

        $UsersLogin = new UsersLoginModel();

        // 🚀 3. TAMBAHKAN TOKEN DAN IP ADDRESS KE DATABASE
        $data_login = [
            'dul_du_id'         => $user['id'],
            'dul_last_activity' => date('Y-m-d H:i:s'),
            'dul_ip_address'    => $this->request->getIPAddress(),
            'dul_token'         => $sessionToken,
        ];

        $last_login_record = $UsersLogin->where('dul_du_id', $user['id'])->first();

        if (!empty($last_login_record)) {
            $pkName = $UsersLogin->primaryKey;
            $data_login[$pkName] = $last_login_record[$pkName];
        }

        $UsersLogin->save($data_login);

        // Cek redirect URL sebelumnya secara aman
        if (is_string($redirectUrl) && $redirectUrl !== '') {
            session()->remove('redirectUrl');
            $trimmedRedirect = trim($redirectUrl);
            $baseUrl         = rtrim(base_url(), '/') . '/';

            if (str_starts_with($trimmedRedirect, $baseUrl)) {
                return redirect()->to($trimmedRedirect);
            }

            if (
                !str_contains($trimmedRedirect, '://')
                && !str_starts_with($trimmedRedirect, '//')
                && !str_starts_with($trimmedRedirect, '\\')
                && !preg_match('/^[a-z][a-z0-9+.-]*:/i', $trimmedRedirect)
            ) {
                return redirect()->to(base_url(ltrim($trimmedRedirect, '/')));
            }
        }

        return redirect()->to('/dashboard');
    }

    public function register()
    {
        $this->WilayahModel = new WilayahModel();

        $kode_kab = Profil_Admin()['kode_kab'];
        $kode_kec = Profil_Admin()['kode_kec'];

        $data = [
            'title' => 'Registration',
            'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', $kode_kec)->findAll(),
            'datarw' => $this->WilayahModel->getDataRW()->getResultArray(),
        ];

        if ($this->request->getPost()) {
            $rules = [
                'fullname' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required|min_length[3]|max_length[25]',
                    'errors' => [
                        'required' => '{field} harus diisi',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]|is_unique[dtks_users.nik]',
                    'errors' => [
                        'required' => '{field} harus diisi',
                        'numeric' => '{field} harus berupa angka',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                        'is_unique' => '{field} sudah terdaftar',
                    ]
                ],
                'nope' => [
                    'label' => 'No. HP',
                    'rules' => 'required|numeric|min_length[11]|max_length[20]|is_unique[dtks_users.nope]',
                    'errors' => [
                        'required' => '{field} harus diisi',
                        'numeric' => '{field} harus berupa angka',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                        'is_unique' => '{field} sudah terdaftar',
                    ]
                ],
                'kelurahan' => [
                    'label' => 'Nama Desa',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi',
                    ]
                ],
                'email' => [
                    'label' => 'Email',
                    'rules' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[dtks_users.email]',
                    'errors' => [
                        'required' => '{field} harus diisi',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                        'valid_email' => '{field} tidak valid',
                        'is_unique' => '{field} sudah terdaftar',

                    ]
                ],
                'password' => [
                    'label' => 'Password',
                    'rules' => 'required|min_length[6]|max_length[255]',
                    'errors' => [
                        'required' => '{field} harus diisi',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'password_confirm' => [
                    'label' => 'Ulangi Password',
                    'rules' => 'matches[password]',
                    'errors' => [
                        'matches' => '{field} tidak sama'
                    ]
                ],
            ];

            if (!$this->validate($rules)) {
                return view('dtks/auth/register', [
                    "validation" => $this->validator,
                    'title' => 'Register',
                    'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', $kode_kec)->findAll(),
                    'datarw' => $this->WilayahModel->getDataRW()->getResultArray(),

                ]);
            } else {
                // === 🧩 Validasi Cloudflare Turnstile ===
                $isDev = (ENVIRONMENT === 'development');
                $captchaVerified = false;

                if ($isDev) {
                    $captchaVerified = true;
                } else {
                    $turnstileResponse = $this->request->getPost('cf-turnstile-response');
                    if (!empty($turnstileResponse)) {
                        $secretKey = '0x4AAAAAADhc2-aRqsdZlCENE-iIH2rie7k'; // Ganti dengan secret key milikmu
                        $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $verifyUrl);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, [
                            'secret'   => $secretKey,
                            'response' => $turnstileResponse,
                            'remoteip' => $this->request->getIPAddress()
                        ]);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($curl);
                        curl_close($curl);

                        $responseData = json_decode($response);
                        if (isset($responseData->success) && $responseData->success === true) {
                            $captchaVerified = true;
                        }
                    }
                }

                if (!$captchaVerified) {
                    session()->setFlashdata('message', [
                        'type' => 'error',
                        'text' => 'Verifikasi keamanan gagal. Silakan coba lagi.'
                    ]);
                    return redirect()->back()->withInput();
                }

                // === Simpan user ke database ===
                $model = new AuthModel();

                $kode_kab = substr($this->request->getVar('kelurahan'), 0, 5);
                $kode_kec = substr($this->request->getVar('kelurahan'), 0, 8);

                $newData = [
                    'nik' => $this->request->getVar('nik'),
                    'fullname' => $this->request->getVar('fullname'),
                    'email' => $this->request->getVar('email'),
                    'kode_desa' => $this->request->getVar('kelurahan'),
                    'kode_kec' => $kode_kec,
                    'kode_kab' => $kode_kab,
                    'status' => 0,
                    'level' => $this->request->getVar('no_rw'),
                    'nope' => $this->request->getVar('nope'),
                    'role_id' => 99, // Role default untuk pendaftar biasa
                    'password' => $this->request->getVar('password'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $model->save($newData);
                $session = session();
                $session->setFlashdata('message', [
                    'type' => 'success',
                    'text' => 'Registrasi Berhasil, silahkan hubungi Admin untuk aktivasi'
                ]);
                return redirect()->to('/login');
            }
        }

        return view('dtks/auth/register', $data);
    }

    public function requestReset()
    {
        helper('opdtks_helper');

        $email = $this->request->getPost('email');
        $nik   = $this->request->getPost('nik');

        // Validasi input
        if (empty($email) || empty($nik)) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Email dan NIK wajib diisi.'
            ]);
            return redirect()->back()->withInput();
        }

        // === 🧩 Validasi Cloudflare Turnstile ===
        $isDev = (ENVIRONMENT === 'development');
        $captchaVerified = false;

        if ($isDev) {
            $captchaVerified = true;
        } else {
            $turnstileResponse = $this->request->getPost('cf-turnstile-response');
            if (!empty($turnstileResponse)) {
                $secretKey = '0x4AAAAAADhc2-aRqsdZlCENE-iIH2rie7k'; // Ganti dengan secret key milikmu
                $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $verifyUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, [
                    'secret'   => $secretKey,
                    'response' => $turnstileResponse,
                    'remoteip' => $this->request->getIPAddress()
                ]);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
                curl_close($curl);

                $responseData = json_decode($response);
                if (isset($responseData->success) && $responseData->success === true) {
                    $captchaVerified = true;
                }
            }
        }

        if (!$captchaVerified) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Verifikasi keamanan gagal. Silakan coba lagi.'
            ]);
            return redirect()->back()->withInput();
        }

        $user = $this->AuthModel
            ->where('email', $email)
            ->where('nik', $nik)
            ->first();

        if (!$user) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Data tidak ditemukan.'
            ]);
            return redirect()->back()->withInput();
        }

        try {
            // Generate token dan simpan
            $token = bin2hex(random_bytes(32));
            $this->AuthModel->update($user['id'], [
                'reset_token'  => $token,
                'reset_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour'))
            ]);

            // Siapkan email
            $resetLink = base_url("reset-password?token={$token}");
            $subject   = 'Reset Password Anda';
            $message   = "
            <p>Halo, <strong>{$user['fullname']}</strong>.</p>
            <p>Kami menerima permintaan untuk mereset password akun Anda.</p>
            <p>Silakan klik tautan berikut untuk melanjutkan proses:</p>
            <p><a href='{$resetLink}' style='background:#28a745;color:white;padding:8px 12px;border-radius:4px;text-decoration:none;'>Reset Password</a></p>
            <p>Link ini berlaku selama 1 jam.</p>
            <hr>
            <p>Abaikan pesan ini jika Anda tidak merasa meminta reset password.</p>
        ";

            // Load config email
            $emailService = \Config\Services::email();
            $config = config('Email');
            $emailService->initialize((array)$config);

            $emailService->setTo($user['email']);
            $emailService->setFrom($config->fromEmail, $config->fromName);
            $emailService->setSubject($subject);
            $emailService->setMessage($message);

            // Kirim email
            if ($emailService->send()) {
                session()->setFlashdata('message', [
                    'type' => 'success',
                    'text' => 'Email reset password telah dikirim ke alamat Anda.'
                ]);
                return redirect()->to(base_url('login'));
            }

            // Jika gagal kirim
            $error = $emailService->printDebugger(['headers']);
            log_message('error', 'Email gagal dikirim: ' . $error);

            // Mode dev: tampilkan isi email
            if (ENVIRONMENT === 'development') {
                echo "<h3>Debug Email (Mode Dev)</h3>";
                echo "<pre>{$error}</pre>";
                echo "<hr><h4>Isi Email:</h4>{$message}";
                exit;
            }

            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan dalam pengiriman email. Silakan hubungi admin.'
            ]);
            return redirect()->back();
        } catch (\Throwable $e) {
            log_message('error', 'requestReset() error: ' . $e->getMessage());
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan internal. Coba lagi nanti.'
            ]);
            return redirect()->back();
        }
    }

    public function redirectToExternalLink($externalLink = null)
    {
        if ($externalLink) {
            // Store the external link in the session
            session()->set('redirectUrl', $externalLink);
        }

        // Temporarily disable the "before" filters
        $this->filter->disable('before');

        // Redirect to the login page
        return redirect()->to('/login');
    }

    private function setUserSession($user)
    {
        // 🚀 Ambil nm_role via query tabel tb_roles
        $db = \Config\Database::connect();
        $roleRow = $db->table('tb_roles')->select('nm_role')->where('id_role', $user['role_id'])->get()->getRowArray();
        $nmRole = $roleRow['nm_role'] ?? 'Pengguna';

        // $previousPage = previous_url();
        $data = [
            'id'              => $user['id'],
            'nik'             => $user['nik'],
            'fullname'        => $user['fullname'],
            'email'           => $user['email'],
            'level'           => $user['level'],
            'nope'            => $user['nope'],
            'role_id'         => $user['role_id'],
            'nm_role'         => $nmRole,
            'status'          => $user['status'],
            'kode_desa'       => $user['kode_desa'] ?? '32.05.33.2006', // Opsional: Beri default jika null
            'kode_kec'        => $user['kode_kec'] ?? '32.05.33',       // Opsional: Beri default jika null
            'kode_kab'        => $user['kode_kab'] ?? '32.05',

            // 🚀 INI BARIS YANG DIPERBAIKI:
            // Jika kode_prov tidak ada di DB, otomatis gunakan '32' (Jawa Barat)
            'kode_prov'       => $user['kode_prov'] ?? '32',

            'jabatan'         => $user['level'],
            'opr_sch'         => $user['opr_sch'],
            'user_image'      => $user['user_image'],
            'user_lembaga_id' => $user['user_lembaga_id'],
            'wilayah_tugas'   => $user['wilayah_tugas'],
            'logDtks'         => true,
            // 'previousPage' => $previousPage,
        ];

        session()->set($data);
        return true;
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

    public function lupaPassword()
    {
        $data = [
            'title' => 'Form. Reset Password'
        ];
        return view('dtks/auth/lupa-password', $data);
    }

    public function resetPassword()
    {
        $title = 'Reset Password';
        $token = $this->request->getVar('token');
        log_message('debug', 'Token from URL: ' . $token); // Debug log untuk token dari URL

        $user = $this->AuthModel->where('reset_token', $token)
            ->where('reset_expiry >=', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            log_message('error', 'Token not valid or expired: ' . $token);
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Token tidak valid atau telah kedaluwarsa.'
            ]);
            return redirect()->to(base_url('login'));
        }

        return view('dtks/auth/reset_password', ['token' => $token, 'title' => $title]);
    }

    public function processResetPassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        // Validasi token
        $user = $this->AuthModel->where('reset_token', $token)
            ->where('reset_expiry >=', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Token tidak valid atau telah kedaluwarsa.'
            ]);
            return redirect()->to(base_url('login'));
        }

        // Validasi password
        if ($password !== $passwordConfirm) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Password dan konfirmasi password tidak sama.'
            ]);
            return redirect()->back();
        }

        if (strlen($password) < 6) {
            session()->setFlashdata('message', [
                'type' => 'error',
                'text' => 'Password harus terdiri dari minimal 6 karakter.'
            ]);
            return redirect()->back();
        }

        // Update password di database
        $this->AuthModel->save([
            'id' => $user['id'],
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_expiry' => null
        ]);

        // 🚀 MITIGASI KEAMANAN TINGKAT TINGGI: LOGOUT SEMUA PERANGKAT!
        // Begitu password berhasil di-reset, hapus semua sesi aktif di dtks_users_login.
        // Detik itu juga, penyusup yang sedang asyik menggunakan akun ini akan langsung tertendang keluar!
        $db = \Config\Database::connect();
        $db->table('dtks_users_login')->where('dul_du_id', $user['id'])->delete();

        session()->setFlashdata('message', [
            'type' => 'success',
            'text' => 'Password berhasil diubah. Keamanan dipulihkan, Anda telah dilogout dari semua perangkat. Silakan login dengan password baru Anda.',
            'context' => 'processResetPassword' // Identifikasi konteks
        ]);

        return redirect()->to(base_url('login'));
    }

    public function adminResetPassword()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid access'
            ]);
        }

        $id = $this->request->getJSON()->id ?? null;

        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak ditemukan'
            ]);
        }

        $user = $this->AuthModel->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ]);
        }

        try {
            // Generate token baru
            $token = bin2hex(random_bytes(32));

            $this->AuthModel->update($id, [
                'reset_token'  => $token,
                'reset_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour'))
            ]);

            // ==========================
            //  KIRIM EMAIL
            // ==========================
            $resetLink = base_url("reset-password?token={$token}");
            $subject   = 'Reset Password Akun Anda';
            $message   = "
            <p>Halo, <strong>{$user['fullname']}</strong>.</p>
            <p>Admin desa telah mengirimkan link reset password.</p>
            <p>Klik tombol berikut:</p>
            <p><a href='{$resetLink}' style='background:#007bff;color:white;padding:10px 15px;border-radius:4px;text-decoration:none;'>Reset Password</a></p>
            <p>Berlaku 1 jam.</p>
        ";

            $email = \Config\Services::email();
            $config = config('Email');
            $email->initialize((array)$config);

            $email->setTo($user['email']);
            $email->setFrom($config->fromEmail, $config->fromName);
            $email->setSubject($subject);
            $email->setMessage($message);

            if (!$email->send()) {
                log_message('error', 'Email gagal dikirim: ' . $email->printDebugger(['headers']));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Email gagal dikirim, cek konfigurasi email.'
                ]);
            }

            // ==========================
            //  KIRIM WHATSAPP
            // ==========================
            $wa = new \App\Libraries\WaService();
            $resetLink = base_url("reset-password?token={$token}");

            $fullname = ucwords(strtolower($user['fullname'])); // kapital per kata

            $messageWA = "*== SINDEN System ==*\n\n"
                . "Halo *{$fullname}*,\n"
                . "Admin desa telah mengirimkan link reset password untuk akun Anda.\n"
                . "Silakan cek email Anda: *{$user['email']}*.\n\n"
                . "Jika email tidak muncul, periksa folder *Spam* atau *Promosi*.\n\n"
                . "Atau klik link berikut untuk langsung reset password:\n"
                . "{$resetLink}\n\n"
                . "_(Pesan otomatis – tidak perlu dibalas)_";

            $wa->sendText($user['nope'], $messageWA);

            // ==========================
            //  RESPONSE KE AJAX
            // ==========================
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Link reset password telah dikirim via Email dan WhatsApp.'
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'adminResetPassword() error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan internal.'
            ]);
        }
    }

    public function logoutAllDevices()
    {
        $userId = session()->get('id');
        $db = \Config\Database::connect();

        // 🚀 SAPU BERSIH: Hapus SEMUA token milik ID ini di tabel dtks_users_login
        // Otomatis semua perangkat yang sedang login (termasuk milik penyusup) akan tertendang!
        $db->table('dtks_users_login')->where('dul_du_id', $userId)->delete();

        // Hancurkan session di perangkat Jenderal saat ini
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Keamanan dipulihkan! Anda telah berhasil logout dari SELURUH perangkat.');
    }
}
