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

use App\Libraries\MailgunService;

class Auth extends BaseController
{
    protected $AuthModel;
    protected $WilayahModel;
    protected $UsersLoginModel;

    public function __construct()
    {
        $this->AuthModel = new AuthModel();
        $this->WilayahModel = new WilayahModel();
        $this->UsersLoginModel = new UsersLoginModel();
    }

    // public function login()
    // {

    //     $data = [];

    //     if ($this->request->getPost()) {

    //         // var_dump($this->request->getvar());
    //         // $nik = $this->request->getVar('nik');

    //         $rules = [
    //             // captha
    //             // 'captha' => 'required',

    //             'email' => 'required|min_length[6]|max_length[50]|valid_email',
    //             'password' => 'required|min_length[6]|max_length[255]|validateUser[email,password]',
    //         ];

    //         $errors = [
    //             // 'captha' => [
    //             //     'errors' => 'Captha tidak sesuai',
    //             // ],
    //             'password' => [
    //                 'validateUser' => "User atau Password tidak sesuai",
    //             ],
    //         ];

    //         if (!$this->validate($rules, $errors)) {
    //             $session = session();
    //             $session->setFlashdata('message', [
    //                 'type' => 'error', // Tambahkan tipe pesan jika perlu
    //                 'text' => 'User atau Password tidak sesuai!'
    //             ]);
    //             return view('dtks/auth/login', [
    //                 "validation" => $this->validator,
    //                 "title" => 'Login',
    //             ]);
    //         } else {

    //             $model = new AuthModel();

    //             $user = $model->where('email', $this->request->getVar('email'))->first();

    //             $secret = '6LctvBomAAAAAF900Ud_B6iOfcKX2R9ZvAGPg2bo';

    //             $credential = array(
    //                 'secret' => $secret,
    //                 'response' => $this->request->getVar('g-recaptcha-response')
    //             );

    //             $verify = curl_init();
    //             curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    //             curl_setopt($verify, CURLOPT_POST, true);
    //             curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($credential));
    //             curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
    //             curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    //             $response = curl_exec($verify);

    //             $status = json_decode($response, true);
    //             if ($status['success']) {

    //                 // tanpa captha
    //                 // dd($user);
    //                 $this->setUserSession($user);

    //                 $UsersLogin = new UsersLoginModel();
    //                 $data_login = [
    //                     'dul_du_id' => $user['id'],
    //                     'dul_last_activity' => date('Y-m-d H:i:s'),
    //                 ];
    //                 $last_id = $UsersLogin->where('dul_du_id', $user['id'])->first();
    //                 if (!empty($last_id)) {
    //                     $UsersLogin->update_data($last_id, $data_login);
    //                 } else {
    //                     $UsersLogin->save_data($data_login);
    //                 }

    //                 // dd($this->setUserSession($user));
    //                 // Redirecting to dashboard after login
    //                 if ($user['status'] !== 1) {
    //                     $session = session();
    //                     $session->setFlashdata('message', [
    //                         'type' => 'error',
    //                         'text' => 'User Non-Aktif, Silakan hubungi Admin!'
    //                     ]);
    //                     return redirect()->to('/login');
    //                 }

    //                 // } else if ($user['status'] == 1 && $user['role_id'] == 5) {
    //                 //     return redirect()->to('/operatorsch');
    //                 // } else if ($user['status'] == 1) {
    //                 //     return redirect()->to('/pages');
    //                 // Redirecting to the previous URL
    //                 // Get the redirect URL from the session if available
    //                 // Check if there is a redirect URL stored in the session
    //                 $redirectUrl = session()->get('redirectUrl');

    //                 if ($redirectUrl) {
    //                     // Remove the redirect URL from the session
    //                     session()->remove('redirectUrl');

    //                     // Temporarily disable the "before" filters
    //                     $this->filter->disable('before');

    //                     // Redirect to the stored redirect URL
    //                     return redirect()->to(base_url($redirectUrl));
    //                     // } else {
    //                     //     // Redirect to a default page if no redirect URL is set
    //                     //     return redirect()->to('/pages');
    //                 }
    //                 // tanpa captha
    //             }
    //         }
    //     }
    //     // echo 'test';
    //     $data = [
    //         'title' => 'Sign In',
    //     ];

    //     return view('dtks/auth/login', $data);
    // }

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
                ]);
            }

            $model = new AuthModel();
            $user = $model->where('email', $this->request->getVar('email'))->first();

            // === 🧩 Tambahan: Deteksi environment ===
            $isDev = (ENVIRONMENT === 'development');

            $captchaVerified = false;

            if ($isDev) {
                // 🔹 Skip reCAPTCHA di local/dev
                $captchaVerified = true;
            } else {
                // 🔹 Verifikasi reCAPTCHA di production
                $secret = '6LctvBomAAAAAF900Ud_B6iOfcKX2R9ZvAGPg2bo'; // Ganti dengan secret key milikmu

                $credential = [
                    'secret' => $secret,
                    'response' => $this->request->getVar('g-recaptcha-response')
                ];

                $verify = curl_init();
                curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
                curl_setopt($verify, CURLOPT_POST, true);
                curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($credential));
                curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($verify);
                curl_close($verify);

                $status = json_decode($response, true);
                $captchaVerified = isset($status['success']) && $status['success'] === true;
            }

            // === 🧩 Jika reCAPTCHA valid (atau dilewati di dev) ===
            if ($captchaVerified) {

                // Buat session login user
                $this->setUserSession($user);

                $UsersLogin = new UsersLoginModel();
                $data_login = [
                    'dul_du_id' => $user['id'],
                    'dul_last_activity' => date('Y-m-d H:i:s'),
                ];
                $last_id = $UsersLogin->where('dul_du_id', $user['id'])->first();

                if (!empty($last_id)) {
                    $UsersLogin->update_data($last_id, $data_login);
                } else {
                    $UsersLogin->save_data($data_login);
                }

                // Cek status aktif
                if ($user['status'] !== 1) {
                    session()->setFlashdata('message', [
                        'type' => 'error',
                        'text' => 'User Non-Aktif, Silakan hubungi Admin!'
                    ]);
                    return redirect()->to('/login');
                }

                // Cek redirect URL sebelumnya
                $redirectUrl = session()->get('redirectUrl');
                if ($redirectUrl) {
                    session()->remove('redirectUrl');
                    return redirect()->to(base_url($redirectUrl));
                }

                // Default redirect ke dashboard
                return redirect()->to('/pages');
            } else {
                // === reCAPTCHA gagal diverifikasi ===
                session()->setFlashdata('message', [
                    'type' => 'error',
                    'text' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'
                ]);
                return redirect()->to('/login')->withInput();
            }
        }

        // Tampilkan form login awal
        $data = ['title' => 'Sign In'];
        return view('dtks/auth/login', $data);
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
        // $previousPage = previous_url();
        $data = [
            'id' => $user['id'],
            'nik' => $user['nik'],
            'fullname' => $user['fullname'],
            'email' => $user['email'],
            'level' => $user['level'],
            'role_id' => $user['role_id'],
            'status' => $user['status'],
            'kode_desa' => $user['kode_desa'],
            'kode_kec' => $user['kode_kec'],
            'kode_kab' => $user['kode_kab'],
            'jabatan' => $user['level'],
            'opr_sch' => $user['opr_sch'],
            'user_image' => $user['user_image'],
            'user_lembaga_id' => $user['user_lembaga_id'],
            'wilayah_tugas' => $user['wilayah_tugas'],
            'logDtks' => true,
            // 'previousPage' => $previousPage,
        ];

        session()->set($data);
        return true;
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
            // let's do the validation here
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
                //strore the user to database
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
                    'role_id' => 5,
                    'password' => $this->request->getVar('password'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                // dd($newData);
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

    public function regOpSek()
    {
        // $data = [];
        $kode_kab = Profil_Admin()['kode_kab'];
        $kode_kec = Profil_Admin()['kode_kec'];
        $this->WilayahModel = new WilayahModel();

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Registration',
            'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', $kode_kec)->findAll(),
            'datarw' => $this->WilayahModel->getDataRW()->getResultArray(),

        ];

        if ($this->request->getPost()) {
            // let's do the validation here
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
                return view('dtks/auth/regopsek', [
                    "validation" => $this->validator,
                    'title' => 'Registration',
                    'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', $kode_kec)->findAll(),
                    'datarw' => $this->WilayahModel->getDataRW()->getResultArray(),

                ]);
            } else {
                //strore the user to database
                $model = new AuthModel();
                $nik = htmlentities(strip_tags(trim($this->request->getVar('nik'))));
                $fullname = htmlentities(strip_tags(trim(strtoupper($this->request->getVar('fullname')))));
                $email = htmlentities(strip_tags(trim($this->request->getVar('email'))));
                $kode_desa = htmlentities(strip_tags(trim($this->request->getVar('kelurahan'))));
                $level = htmlentities(strip_tags(trim($this->request->getVar('no_rw'))));
                $opr_sch = htmlentities(strip_tags(trim(strtoupper($this->request->getVar('opr_sch')))));
                $nope = htmlentities(strip_tags(trim($this->request->getVar('nope'))));
                $password = htmlentities(strip_tags(trim($this->request->getVar('password'))));

                $newData = [
                    'nik' => $nik,
                    // 'username' => $this->request->getVar('username'),
                    'fullname' => $fullname,
                    'email' => $email,
                    'kode_desa' => $kode_desa,
                    'kode_kec' => $kode_kec,
                    'kode_kab' => $kode_kab,
                    'level' => $level,
                    'status' => 0,
                    'opr_sch' => $opr_sch,
                    'nope' => $nope,
                    'role_id' => 5,
                    'password' => $password,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                // dd($newData);
                $model->save($newData);
                $session = session();
                $session->setFlashdata('success', 'Registrasi Berhasil, silahkan hubungi Admin untuk aktivasi');
                return redirect()->to('/login');
            }
        }

        return view('dtks/auth/regopsek', $data);
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

        session()->setFlashdata('message', [
            'type' => 'success',
            'text' => 'Password berhasil diubah. Silakan login dengan password baru Anda.',
            'context' => 'processResetPassword' // Identifikasi konteks
        ]);

        return redirect()->to(base_url('login'));
    }
}
