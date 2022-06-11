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
    }
    public function index()
    {
        $user_id = session()->get('id');
        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Profil',
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_id' => $user_id,
            'user_login' => $this->AuthModel->getUserId(),
            'lembaga' => $this->LembagaModel->findAll(),

        ];
        // dd($data['lembaga']);
        return view('profil/index', $data);
    }

    public function update_user()
    {
        if ($this->request->isAJAX()) {
            $id_user = $this->request->getPost('id_user');
            $fullname = $this->request->getPost('fullname');
            $nik = $this->request->getPost('nik');
            $email = $this->request->getPost('email');
            $nope = $this->request->getPost('nope');

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
                'user_image' => $nama_gambar,
            ];
            // var_dump($personalData);


            $data = $this->AuthModel->updatePersonalData($id_user, $personalData);
            echo json_encode($data);
        }
    }
}
