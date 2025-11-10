<?php

namespace App\Controllers\Dtsen;

use App\Models\DtsenKkModel;
use App\Models\DtsenRtModel;
use App\Models\GenModel;
use CodeIgniter\Controller;

class DtsenSe extends Controller
{
    protected $kkModel;
    protected $rtModel;
    protected $genModel;

    public function __construct()
    {
        $this->kkModel = new DtsenKkModel();
        $this->rtModel = new DtsenRtModel();
        $this->genModel = new GenModel();
    }

    // 🏠 Halaman utama Data Keluarga
    public function index()
    {
        $session = session();
        $kodeDesa = $session->get('kode_desa');
        $roleId   = $session->get('role_id');
        $rwUser   = $session->get('level'); // RW untuk pendata

        // Ambil daftar RW di desa tersebut
        $dataRW = $this->genModel->getRWByDesa($kodeDesa);

        $data = [
            'title'       => 'Data Keluarga',
            'namaApp'     => 'SINDEN',
            'user_login'  => $session->get(),
            'kode_desa'   => $kodeDesa,
            'rwUser'      => $rwUser,
            'role_id'     => $roleId,
            'dataRW'      => $dataRW,
        ];

        return view('dtsen/se/index', $data);
    }

    public function tabel_data()
    {
        try {
            $session = session();
            $kodeDesa     = $session->get('kode_desa');
            $roleId       = $session->get('role_id');
            $rwUser       = $session->get('level');
            $filterRW     = $this->request->getPost('filterRW') ?? null;
            $wilayahTugas = $session->get('wilayah_tugas');

            $filter = [
                'kode_desa'     => $kodeDesa,
                'rw'            => ($roleId >= 4 ? $rwUser : $filterRW),
                'wilayah_tugas' => $wilayahTugas,
            ];

            $dataKeluarga = $this->kkModel->getFilteredData($filter);

            return $this->response->setJSON(['data' => $dataKeluarga]);
        } catch (\Throwable $e) {
            log_message('error', '❌ tabel_data() error: ' . $e->getMessage());
            return $this->response->setJSON(['data' => [], 'error' => true, 'message' => $e->getMessage()]);
        }
    }

    // 📝 Update kategori desil
    public function updateDesil()
    {
        try {
            $session = session();
            $role_id = (int) $session->get('role_id');

            // 🚫 Proteksi: hanya role_id <= 3 yang boleh ubah kategori_desil
            if ($role_id > 3) {
                return $this->response->setJSON([
                    'status' => 'forbidden',
                    'message' => 'Anda tidak memiliki izin untuk mengubah kategori desil.'
                ]);
            }

            $idKk = $this->request->getPost('id_kk');
            $kategoriDesil = $this->request->getPost('kategori_desil');

            if (empty($idKk) || empty($kategoriDesil)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID KK atau Desil tidak boleh kosong.'
                ]);
            }

            $db = db_connect();
            $cek = $db->table('dtsen_se')->where('id_kk', $idKk)->get()->getRow();

            if ($cek) {
                $db->table('dtsen_se')
                    ->where('id_kk', $idKk)
                    ->update(['kategori_desil' => $kategoriDesil]);
            } else {
                $db->table('dtsen_se')->insert([
                    'id_kk' => $idKk,
                    'kategori_desil' => $kategoriDesil,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->response->setJSON(['status' => 'success']);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
