<?php

namespace App\Controllers\Dtsen;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\IncomingRequest;


use App\Models\Dtsen\DtsenKkModel;
use App\Models\Dtsen\DtsenRtModel;
use App\Models\GenModel;

class DtsenSe extends Controller
{
    /**
     * @var IncomingRequest
     */
    protected $request;

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

            if (empty($idKk)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID KK tidak boleh kosong.'
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

    public function deleteKeluarga()
    {
        try {
            $id     = $this->request->getPost('id_kk');
            $alasan = trim($this->request->getPost('alasan'));

            if (!$id) {
                return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
            }

            if (!$alasan) {
                return $this->response->setJSON(['status' => false, 'message' => 'Alasan wajib diisi']);
            }

            // simpan alasan sebelum soft delete
            $this->kkModel->update($id, [
                'delete_reason' => $alasan,
                'deleted_at'    => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Keluarga berhasil dihapus']);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function restoreKeluarga()
    {
        try {
            $id = $this->request->getPost('id_kk');

            if (!$id) {
                return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
            }

            // restore deleted_at dan delete_reason
            $this->kkModel->update($id, [
                'deleted_at'    => null,
                'delete_reason' => null,
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dipulihkan']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tabel_arsip()
    {
        try {
            $builder = $this->kkModel->onlyDeleted();

            $data = $builder
                ->select('
                dtsen_kk.id_kk,
                dtsen_kk.no_kk,
                dtsen_kk.kepala_keluarga,
                dtsen_kk.alamat,
                dtsen_kk.deleted_at,
                dtsen_kk.delete_reason,
                rt.rw,
                rt.rt
            ')
                ->join('dtsen_rt rt', 'rt.id_rt = dtsen_kk.id_rt', 'left')
                ->orderBy('deleted_at', 'DESC')
                ->findAll();

            return $this->response->setJSON(['data' => $data]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'data' => [],
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function arsipAnggota()
    {
        try {
            $db = \Config\Database::connect();

            // ============================================================
            // 1) ARSIP DARI TABEL UTAMA: dtsen_art
            // ============================================================
            $arsipUtama = $db->table('dtsen_art a')
                ->select("
                a.id_art AS id,
                a.id_kk,
                a.nik,
                a.nama,
                a.deleted_at,
                a.delete_reason,
                s.jenis_shdk,
                'utama' AS sumber
            ")
                ->join('tb_shdk s', 's.id = a.shdk', 'left')
                ->where('a.deleted_at IS NOT NULL')
                ->get()
                ->getResultArray();

            // ============================================================
            // 2) ARSIP DARI TABEL USULAN: dtsen_usulan_art
            // ============================================================
            $arsipUsulan = $db->table('dtsen_usulan_art ua')
                ->select("
                ua.id AS id,
                u.dtsen_kk_id AS id_kk,
                ua.nik,
                ua.nama,
                ua.deleted_at,
                ua.delete_reason,
                s.jenis_shdk,
                'usulan' AS sumber
            ")
                ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'left')
                ->join('tb_shdk s', 's.id = ua.hubungan', 'left')
                ->where('ua.deleted_at IS NOT NULL')
                ->get()
                ->getResultArray();

            // ============================================================
            // 3) GABUNGKAN KEDUA SUMBER
            // ============================================================
            $data = array_merge($arsipUtama, $arsipUsulan);

            // Urutkan berdasarkan tanggal hapus terbaru
            usort($data, function ($a, $b) {
                return strtotime($b['deleted_at']) <=> strtotime($a['deleted_at']);
            });

            return $this->response->setJSON([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'data'    => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteAnggota()
    {
        try {
            $id = $this->request->getPost('id_art');
            $alasan = trim($this->request->getPost('alasan'));

            if (!$id) return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
            if (!$alasan) return $this->response->setJSON(['status' => false, 'message' => 'Alasan wajib diisi']);

            $db = \Config\Database::connect();
            $db->table('dtsen_art')->where('id_art', $id)->update([
                'delete_reason' => $alasan,
                'deleted_at'    => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Anggota berhasil dihapus']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function restoreArt()
    {
        try {
            $id = $this->request->getPost('id_art');

            if (!$id) return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);

            $db = \Config\Database::connect();
            $db->table('dtsen_art')->where('id_art', $id)->update([
                'deleted_at'    => null,
                'delete_reason' => null,
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Data anggota dipulihkan']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
