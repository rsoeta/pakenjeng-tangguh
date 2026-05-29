<?php

namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\Dtks\MenuModel;
use App\Models\GenModel;

class MenuApp extends BaseController
{
    protected $MenuModel;
    protected $GenModel;

    public function __construct()
    {
        // 🚀 Hanya panggil model yang benar-benar digunakan untuk Menu!
        $this->MenuModel = new MenuModel();
        $this->GenModel  = new GenModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Manajemen Menu SINDEN',
            'menu'       => $this->MenuModel->orderBy('tm_parent_id', 'asc')->findAll(),
            'statusRole' => $this->GenModel->getStatusRole(),
        ];

        return view('settings/menu', $data);
    }

    public function load_data_menu()
    {
        $data = $this->MenuModel->load_data_menu();
        return $this->response->setJSON($data);
    }

    public function insert_data_menu()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'tm_nama'         => trim((string) $this->request->getPost('tm_nama')),
                'tm_class'        => trim((string) $this->request->getPost('tm_class')) ?: null,
                'tm_url'          => trim((string) $this->request->getPost('tm_url')),
                'tm_icon'         => trim((string) $this->request->getPost('tm_icon')) ?: null,
                'tm_parent_id'    => (int) $this->request->getPost('tm_parent_id'),
                'tm_status'       => (int) $this->request->getPost('tm_status'),
                'tm_grup_akses'   => (int) $this->request->getPost('tm_grup_akses'),
                'tm_urutan'       => (int) $this->request->getPost('tm_urutan'),
                'tm_is_dashboard' => (int) $this->request->getPost('tm_is_dashboard'),
            ];

            $insert = $this->MenuModel->db->table('tb_menu')->insert($data);

            if ($insert) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Menu baru berhasil ditambahkan!']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
            }
        }
    }

    public function update_data_menu()
    {
        $id = $this->request->getPost('id');
        $data = [
            $this->request->getPost('table_column') => $this->request->getPost('value'),
        ];
        $this->MenuModel->update_data_menu($id, $data);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function delete_data_menu()
    {
        $id = $this->request->getPost('id');
        $this->MenuModel->delete_data_menu($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function get_nama_menu()
    {
        $id = $this->request->getPost('id');
        $data = $this->MenuModel->get_nama_menu($id);
        return $this->response->setJSON($data);
    }
}
