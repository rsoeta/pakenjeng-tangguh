<?php

namespace App\Controllers\Dtks;

use App\Models\Dtks\AuthModel;
use App\Models\WilayahModel;
use App\Models\RwModel;


use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        helper(['form']);

        $data = [];

        if ($this->request->getMethod() == 'post') {

            // var_dump($this->request->getvar());
            // $nik = $this->request->getVar('nik');

            $rules = [
                'email' => 'required|min_length[6]|max_length[50]|valid_email',
                'password' => 'required|min_length[6]|max_length[255]|validateUser[email,password]',
            ];

            $errors = [
                'password' => [
                    'validateUser' => "Email atau Password tidak sesuai",
                ],
            ];

            if (!$this->validate($rules, $errors)) {
                $session = session();
                $session->setFlashdata('message', 'User atau Password tidak sesuai!');
                return view('dtks/auth/login', [
                    "validation" => $this->validator,
                    'namaApp' => 'Opr NewDTKS',
                    "title" => 'Login',
                ]);
            } else {

                $model = new AuthModel();

                $user = $model->where('email', $this->request->getVar('email'))->first();
                // dd($user);
                $this->setUserSession($user);


                // dd($this->setUserSession($user));
                // Redirecting to dashboard after login
                if ($user['status'] !== 1) {
                    $session = session();
                    $session->setFlashdata('message', 'User Non-Aktif, Silakan kontak Admin!');
                    return redirect()->to('login');
                } else if ($user['status'] == 1 && $user['role_id'] == 5) {
                    return redirect()->to('/operatorsch');
                    // } else if ($user['status'] == 1) {
                    //     return redirect()->to('/pages');
                }
            }
        }
        // echo 'test';
        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Sign In',
            'pass_log' => [
                'Admin' => password_hash('admindesa', PASSWORD_DEFAULT),
                '001' => password_hash('2P1PZG9H', PASSWORD_DEFAULT),
                '002' => password_hash('9VNYUI1W', PASSWORD_DEFAULT),
                '003' => password_hash('19BW8NCI', PASSWORD_DEFAULT),
                '004' => password_hash('N4W6BYJ1', PASSWORD_DEFAULT),
                '005' => password_hash('PXVBSKE8', PASSWORD_DEFAULT),
                '006' => password_hash('W6CY679Z', PASSWORD_DEFAULT),
                '007' => password_hash('MU1LEU2Q', PASSWORD_DEFAULT),
            ],
        ];

        return view('dtks/auth/login', $data);
    }

    private function setUserSession($user)
    {
        $data = [
            'id' => $user['id'],
            'nik' => $user['nik'],
            'fullname' => $user['fullname'],
            'email' => $user['email'],
            'level' => $user['level'],
            'role_id' => $user['role_id'],
            'status' => $user['status'],
            'kode_desa' => $user['kode_desa'],
            'opr_sch' => $user['opr_sch'],
            'user_image' => $user['user_image'],
            'logDtks' => true,
        ];

        session()->set($data);
        return true;
    }


    public function register()
    {
        // $data = [];
        helper(['form']);

        $this->WilayahModel = new WilayahModel();

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Registration',
            'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
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
                    'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                    'datarw' => $this->WilayahModel->getDataRW()->getResultArray(),

                ]);
            } else {
                //strore the user to database
                $model = new AuthModel();

                $newData = [
                    'nik' => $this->request->getVar('nik'),
                    // 'username' => $this->request->getVar('username'),
                    'fullname' => $this->request->getVar('fullname'),
                    'email' => $this->request->getVar('email'),
                    'kode_desa' => $this->request->getVar('kelurahan'),
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
                $session->setFlashdata('success', 'Registrasi Berhasil, silahkan hubungi Admin untuk aktivasi');
                return redirect()->to('/login');
            }
        }

        return view('dtks/auth/register', $data);
    }

    public function regOpSek()
    {
        // $data = [];
        helper(['form']);

        $this->WilayahModel = new WilayahModel();

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Registration',
            'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
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
                    'namaApp' => 'Opr NewDTKS',
                    'title' => 'Registration',
                    'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                    'datarw' => $this->WilayahModel->getDataRW()->getResultArray(),

                ]);
            } else {
                //strore the user to database
                $model = new AuthModel();

                $newData = [
                    'nik' => $this->request->getVar('nik'),
                    // 'username' => $this->request->getVar('username'),
                    'fullname' => strtoupper($this->request->getVar('fullname')),
                    'email' => $this->request->getVar('email'),
                    'kode_desa' => $this->request->getVar('kelurahan'),
                    'status' => 0,
                    'opr_sch' => strtoupper($this->request->getVar('opr_sch')),
                    'nope' => $this->request->getVar('nope'),
                    'role_id' => 5,
                    'password' => $this->request->getVar('password'),
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
}
