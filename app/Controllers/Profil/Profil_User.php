<?php

namespace App\Controllers\Profil;

use App\Models\Dtks\AuthModel;
use App\Models\WilayahModel;
use App\Models\GenModel;
use App\Models\Dtks\LembagaModel;


use App\Controllers\BaseController;

class Profil_User extends BaseController
{
    public function __construct()
    {
        $this->AuthModel = new AuthModel();
        $this->GenModel = new GenModel();
        $this->LembagaModel = new LembagaModel();
        $this->WilayahModel = new WilayahModel();
    }
    public function index()
    {
        $user_id = session()->get('id');
        $getAjax = $this->WilayahModel->getAjaxSearch()->getResultArray();
        foreach ($getAjax as $row) {
            $nama_kab = $row['nama_kab'];
            $nama_kec = $row['nama_kec'];
            $nama_desa = $row['nama_desa'];
        }

        if (session()->get('role_id') == 1) {
            $user_role = 'Kabupaten';
            $nama_pemerintah = $nama_kab;
        } else if (session()->get('role_id') == 2) {
            $user_role = 'Kecamatan';
            $nama_pemerintah = $nama_kec;
        } else {
            $user_role = 'Desa';
            $nama_pemerintah = $nama_desa;
        }

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Profil',
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_id' => $user_id,
            'user_login' => $this->AuthModel->getUserId(),
            'lembaga' => $this->LembagaModel->getLembaga($user_id = false),
            'getAjax' => $this->WilayahModel->getAjaxSearch()->getResultArray(),
            'user_role' => $user_role,
            'nama_pemerintah' => $nama_pemerintah,

        ];
        // dd($data['getAjax']);
        // dd($data['user_login']);
        // dd(session()->get('id'));
        return view('profil/index', $data);
    }

    public function ajaxSearch()
    {
        $data = [];
        $data = $this->WilayahModel->getAjaxSearch();

        echo json_encode($data);
    }

    public function update_user()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());

            $id_user = $this->request->getPost('id_user');
            $fullname = $this->request->getPost('fullname');
            $nik = $this->request->getPost('nik');
            $email = $this->request->getPost('email');
            $nope = $this->request->getPost('nope');
            $user_lembaga_id = $this->request->getPost('user_lembaga_id');

            // if (!$this->validate([
            //     'fp_user' => [
            //         'rules' => 'mime_in[user_image,image/png,image/jpg]|ext_in[user_image,png,jpg,gif]|is_image[user_image]',
            //         'errors' => [
            //             // 'max_size' => 'Ukuran gambar terlalu besar',
            //             'ext_in' => 'Yang ada pilih bukan gambar',
            //             'is_image' => 'Yang ada pilih bukan gambar',
            //             'mime_in' => 'Yang ada pilih bukan gambar',
            //         ]
            //     ]
            // ])) {
            //     $validation = \Config\Services::validation();
            //     return redirect()->to('profil_user')->withInput()->with('validation', $validation);
            // }
            // ambil gambar
            $file_gambar = $this->request->getFile('fp_user');
            // dd($file_gambar);

            if ($file_gambar->getError() == 4) {
                $nama_gambar = 'assets/dist/img/profile/default.png';
            } else {
                // // generate nama file
                // $nama_gambar = $file_gambar->getRandomName();

                // pindahkan file ke folder profil
                $file_gambar->move('data/profil');

                //ambil nama file
                $nama_gambar = $file_gambar->getName();
            }

            $personalData = [
                'id' => $id_user,
                'fullname' => $fullname,
                'nik' => $nik,
                'email' => $email,
                'nope' => $nope,
                'user_lembaga_id' => $user_lembaga_id,
                'user_image' => $nama_gambar,
            ];
            // var_dump($personalData);


            $data = $this->AuthModel->updatePersonalData($id_user, $personalData);
            echo json_encode($data);
        }
    }

    public function submit_lembaga()
    {
        if ($this->request->isAJAX()) {
            $user_lembaga_id = $this->request->getPost('user_lembaga_id');
            $lp_kepala = $this->request->getPost('lp_kepala');
            $lp_nip = $this->request->getPost('lp_nip');
            $lp_sekretariat = $this->request->getPost('lp_sekretariat');
            $lp_kode_pos = $this->request->getPost('lp_kode_pos');
            $lp_email = $this->request->getPost('lp_email');
            $id_user = $this->request->getPost('id_user');

            // ambil gambar
            // $file_gambar = $this->request->getFile('fp_user');
            // dd($file_gambar);

            // if ($file_gambar->getError() == 4) {
            //     $nama_gambar = 'assets/dist/img/profile/default.png';
            // } else {
            // // generate nama file
            // $nama_gambar = $file_gambar->getRandomName();

            // pindahkan file ke folder profil
            // $file_gambar->move('data/profil');

            //ambil nama file
            // $nama_gambar = $file_gambar->getName();
        }

        $lembagaData = [
            'lp_kategori' => $user_lembaga_id,
            'lp_kepala' => $lp_kepala,
            'lp_nip' => $lp_nip,
            'lp_sekretariat' => $lp_sekretariat,
            'lp_kode_pos' => $lp_kode_pos,
            'lp_email' => $lp_email,
            'lp_user' => $id_user,
            // 'user_image' => $nama_gambar,
        ];
        // var_dump($lembagaData);


        $data = $this->LembagaModel->submitlembagaData($lembagaData);
        echo json_encode($data);
    }

    public function update_lembaga()
    {
        if ($this->request->isAJAX()) {
            $lp_id = $this->request->getPost('lp_id');
            $user_lembaga_id = $this->request->getPost('user_lembaga_id');
            $lp_kepala = $this->request->getPost('lp_kepala');
            $lp_nip = $this->request->getPost('lp_nip');
            $lp_sekretariat = $this->request->getPost('lp_sekretariat');
            $lp_kode_pos = $this->request->getPost('lp_kode_pos');
            $lp_email = $this->request->getPost('lp_email');
            $id_user = $this->request->getPost('id_user');

            // ambil gambar
            // $file_gambar = $this->request->getFile('fp_user');
            // dd($file_gambar);

            // if ($file_gambar->getError() == 4) {
            //     $nama_gambar = 'assets/dist/img/profile/default.png';
            // } else {
            // // generate nama file
            // $nama_gambar = $file_gambar->getRandomName();

            // pindahkan file ke folder profil
            // $file_gambar->move('data/profil');

            //ambil nama file
            // $nama_gambar = $file_gambar->getName();
        }

        $lembagaData = [
            'lp_id' => $lp_id,
            'lp_kategori' => $user_lembaga_id,
            'lp_kepala' => $lp_kepala,
            'lp_nip' => $lp_nip,
            'lp_sekretariat' => $lp_sekretariat,
            'lp_kode_pos' => $lp_kode_pos,
            'lp_email' => $lp_email,
            'lp_user' => $id_user
            // 'user_image' => $nama_gambar,
        ];
        // var_dump($lembagaData);


        $data = $this->LembagaModel->updatelembagaData($lp_id, $lembagaData);
        echo json_encode($data);
    }
}
