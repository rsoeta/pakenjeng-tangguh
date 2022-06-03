<?php

namespace App\Controllers\Dtks;


use CodeIgniter\Controller;
use App\Controllers\BaseController;
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
            'users' => $this->User->getFindAll()->getResultArray(),
            'roles' => $this->Role->getRole()->getResultArray(),
            'percentages' => $this->VervalPbiModel->jml_persentase(),
            'statusRole' => $this->GenModel->getStatusRole(),

        ];
        // return view('dtks/data/yatim/index');
        return view('dtks/users/index', $data);
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
