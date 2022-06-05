<?php

namespace App\Controllers\Dtks;


use CodeIgniter\Controller;
use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\UsersModel;
use App\Models\Dtks\VervalPbiModel;
use App\Models\RoleModel;
use App\Models\RwModel;
use App\Models\GenModel;
use App\Models\WilayahModel;


class Users extends BaseController
{
    public function __construct()
    {
        helper(['form']);
        $this->VervalPbiModel = new VervalPbiModel();
        $this->User = new UsersModel();
        $this->Role = new RoleModel();
        $this->RwModel = new RwModel();
        $this->GenModel = new GenModel();
        $this->WilayahModel = new WilayahModel();
    }

    public function index()
    {
        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Daftar Users',
            'title1' => 'Tambah User',
            'role' => $this->Role->getRole(),
            'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            'datarw' => $this->WilayahModel->getDataRW()->getResultArray(),
            'users' => $this->User->getFindAll()->getResultArray(),
            'roles' => $this->Role->getRole()->getResultArray(),
            'percentages' => $this->VervalPbiModel->jml_persentase(),
            'statusRole' => $this->GenModel->getStatusRole(),

        ];
        // return view('dtks/data/yatim/index');
        return view('dtks/users/index', $data);
    }

    // function tambah user
    public function tambah()
    {
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
                return view('/dtks/users/index', [
                    "validation" => $this->validator,
                    'namaApp' => 'Opr NewDTKS',
                    'title' => 'Daftar Users',
                    'title1' => 'Tambah User',
                    'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                    'datarw' => $this->WilayahModel->getDataRW()->getResultArray(),
                    'users' => $this->User->getFindAll()->getResultArray(),
                    'roles' => $this->Role->getRole()->getResultArray(),
                    'statusRole' => $this->GenModel->getStatusRole(),

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
                return redirect()->to('/users');
            }
        }
    }
    function hapus()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->User->delete($id);

            $msg = [
                'sukses' => 'User berhasil dihapus'
            ];
            echo json_encode($msg);
        } else {
            return redirect()->to('denied');
            exit;
        }
    }

    public function formview()
    {
        if ($this->request->isAJAX()) {

            $UsersModel = new UsersModel;

            $id = $this->request->getVar('id');
            $row = $UsersModel->find($id);

            $data = [
                'title' => 'View User',
                'modTtl' => 'Form. View User',
                'id' => $row['id'],
                'nik' => $row['nik'],
                'kode_desa' => $row['kode_desa'],
                'username' => $row['username'],
                'fullname' => $row['fullname'],
                'email' => $row['email'],
                'status' => $row['status'],
                'level' => $row['level'],
                'role_id' => $row['role_id'],
                'datarw' => $this->RwModel->noRw(),
                'user_image' => $row['user_image'],
                'roles' => $this->Role->getRole()->getResultArray(),
                'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),

            ];
            // var_dump($data);
            $msg = [
                'sukses' => view('dtks/users/formview', $data),
            ];

            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
            exit;
        }
    }

    public function updatedata()
    {
        if ($this->request->isAJAX()) {
            $simpandata = [
                'nik' => $this->request->getVar('nik'),
                'fullname' => $this->request->getVar('fullname'),
                'email' => $this->request->getVar('email'),
                'kode_desa' => $this->request->getVar('kode_desa'),
                'role_id' => $this->request->getVar('role'),
                'level' => $this->request->getVar('no_rw'),
                'status' => $this->request->getVar('status'),
            ];
            // var_dump($simpandata);

            $id = $this->request->getVar('id');
            $this->User->update($id, $simpandata);

            $msg = [
                'sukses' => 'Data Berhasil di update!'
            ];
            echo json_encode($msg);
        } else {
            return redirect()->to('denied');
            exit;
        }
    }

    public function update_status($uid, $ustatus)
    {
        // if (null !== ($this->request->getVar('ustatus'))) {
        $model = new UsersModel();
        $updated_status = $model->update_status($uid, $ustatus);
        $session = session();

        if ($updated_status > 0) {
            $session->setFlashdata('success', 'Status berhasil diubah');
        } else {
            $session->setFlashdata('danger', 'Status gagal diubah');
        }
        return redirect()->to('/dtks/users');
        // }
    }
}
