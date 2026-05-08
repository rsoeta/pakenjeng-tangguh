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

    // ========================================================
    // 🛠️ FUNGSI BANTUAN (HELPER) UNTUK VIEW
    // ========================================================

    // Mengecek apakah fitur pemulihan sedang diaktifkan di tb_menu
    private function checkMenuPemulihan()
    {
        // 💡 Tambahkan baris ini untuk menghubungkan ke database
        $db = \Config\Database::connect();

        return $db->table('tb_menu')
            ->where('tm_url', 'pembaruan-keluarga/pemulihan') // sudah dipastikan sama dengan di DB
            ->where('tm_status', 1)
            ->get()
            ->getRowArray();
    }

    // Mengumpulkan semua variabel wajib agar tidak ditulis berulang-ulang
    private function getBaseData($title, $segment)
    {
        $session  = session();
        $kodeDesa = $session->get('kode_desa');

        return [
            'title'          => $title,
            'segment'        => $segment,
            'namaApp'        => 'SINDEN',
            'user_login'     => $session->get(),
            'kode_desa'      => $kodeDesa,
            'rwUser'         => $session->get('level'), // RW untuk pendata
            'role_id'        => $session->get('role_id'),
            'dataRW'         => $this->genModel->getRWByDesa($kodeDesa),
            'menu_pemulihan' => $this->checkMenuPemulihan(),
        ];
    }

    // ========================================================
    // 🌐 ROUTING HALAMAN (NAV FLOW)
    // ========================================================

    // 🏠 Halaman 1: Daftar Keluarga Utama
    public function index()
    {
        $data = $this->getBaseData('Data Keluarga', 'daftar');
        return view('dtsen/se/v_daftar', $data);
    }

    // 🟡 Halaman 2: Draft Pembaruan
    public function draft()
    {
        $data = $this->getBaseData('Draft Pembaruan', 'draft');
        return view('dtsen/se/v_draft', $data);
    }

    // 🟢 Halaman 3: Submitted
    public function submitted()
    {
        $data = $this->getBaseData('Data Submitted', 'submitted');
        return view('dtsen/se/v_submitted', $data);
    }

    // 🚨 Halaman 4: Pemulihan Data Wilayah (Hanya Admin)
    public function pemulihan()
    {
        $data = $this->getBaseData('Pemulihan Data Wilayah', 'pemulihan');

        // Proteksi Lapis Ganda: Jika saklar OFF atau bukan Admin, tendang ke index
        if ($data['role_id'] > 3) {
            return redirect()->to('/dtsen-se');
        }

        return view('dtsen/se/v_pemulihan', $data);
    }

    // 🔴 Halaman 5: Arsip (Hanya Admin)
    public function arsip()
    {
        $data = $this->getBaseData('Arsip Keluarga & Anggota', 'arsip');

        // Proteksi Lapis Ganda: Hanya Admin yang bisa akses Arsip
        if ($data['role_id'] > 3) {
            return redirect()->to('/dtsen-se');
        }

        return view('dtsen/se/v_arsip', $data);
    }

    // ========================================================
    // 📡 AJAX DATATABLES
    // ========================================================

    public function tabel_data()
    {
        try {
            $session = session();

            // 1️⃣ DATA SESSION
            $kodeDesa     = $session->get('kode_desa');
            $roleId       = (int) $session->get('role_id');
            $rwUser       = $session->get('level');
            $wilayahTugas = $session->get('wilayah_tugas');

            // 2️⃣ FILTER DARI FRONTEND
            $filterClient = $this->request->getPost('filter') ?? [];

            $filter = [
                'kode_desa'     => $kodeDesa,
                'wilayah_tugas' => $wilayahTugas,
                'rw'            => $filterClient['rw']     ?? null,
                'rt'            => $filterClient['rt']     ?? null,
                'status'        => $filterClient['status'] ?? null,
                'desil'         => $filterClient['desil']  ?? null,
            ];

            // 4️⃣ FILTER AKSES (ROLE)
            if ($roleId >= 4) {
                $filter['rw'] = $rwUser;
            }

            // 5️⃣ AMBIL DATA
            $dataKeluarga = $this->kkModel->getFilteredData($filter);

            // 6️⃣ TAMBAHKAN FLAG AKSES
            $canInputDesil = ($roleId <= 3);

            foreach ($dataKeluarga as &$row) {
                $row['can_input_desil'] = $canInputDesil;
            }

            return $this->response->setJSON([
                'data' => $dataKeluarga
            ]);
        } catch (\Throwable $e) {
            log_message('error', '❌ tabel_data() error: ' . $e->getMessage());

            return $this->response->setJSON([
                'data'    => [],
                'error'   => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    // // 🏠 Halaman utama Data Keluarga
    // public function index()
    // {
    //     $session = session();
    //     $kodeDesa = $session->get('kode_desa');
    //     $roleId   = $session->get('role_id');
    //     $rwUser   = $session->get('level'); // RW untuk pendata

    //     // Ambil daftar RW di desa tersebut
    //     $dataRW = $this->genModel->getRWByDesa($kodeDesa);

    //     $data = [
    //         'title'       => 'Data Keluarga',
    //         'namaApp'     => 'SINDEN',
    //         'user_login'  => $session->get(),
    //         'kode_desa'   => $kodeDesa,
    //         'rwUser'      => $rwUser,
    //         'role_id'     => $roleId,
    //         'dataRW'      => $dataRW,
    //     ];

    //     return view('dtsen/se/index', $data);
    // }

    // public function tabel_data()
    // {
    //     try {
    //         $session = session();

    //         // =============================
    //         // 1️⃣ DATA SESSION
    //         // =============================
    //         $kodeDesa     = $session->get('kode_desa');
    //         $roleId       = (int) $session->get('role_id');
    //         $rwUser       = $session->get('level');
    //         $wilayahTugas = $session->get('wilayah_tugas');

    //         // =============================
    //         // 2️⃣ FILTER DARI FRONTEND
    //         // =============================
    //         $filterClient = $this->request->getPost('filter') ?? [];

    //         $filter = [
    //             'kode_desa'     => $kodeDesa,
    //             'wilayah_tugas' => $wilayahTugas,
    //             'rw'            => $filterClient['rw']     ?? null,
    //             'rt'            => $filterClient['rt']     ?? null,
    //             'status'        => $filterClient['status'] ?? null,
    //             'desil'         => $filterClient['desil']  ?? null,
    //         ];

    //         // =============================
    //         // 4️⃣ FILTER AKSES (ROLE)
    //         // =============================
    //         if ($roleId >= 4) {
    //             $filter['rw'] = $rwUser;
    //         }

    //         // =============================
    //         // 5️⃣ AMBIL DATA
    //         // =============================
    //         $dataKeluarga = $this->kkModel->getFilteredData($filter);

    //         // =============================
    //         // 6️⃣ TAMBAHKAN FLAG AKSES
    //         // =============================
    //         $canInputDesil = ($roleId <= 3);

    //         foreach ($dataKeluarga as &$row) {
    //             $row['can_input_desil'] = $canInputDesil;
    //         }

    //         return $this->response->setJSON([
    //             'data' => $dataKeluarga
    //         ]);
    //     } catch (\Throwable $e) {

    //         log_message('error', '❌ tabel_data() error: ' . $e->getMessage());

    //         return $this->response->setJSON([
    //             'data'    => [],
    //             'error'   => true,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // 📝 Update kategori desil
    public function updateDesil()
    {
        log_message('error', 'POST: ' . json_encode($this->request->getPost()));

        try {

            $session = session();
            $role_id = (int) $session->get('role_id');
            $userId  = $session->get('id_user') ?? 0;

            // 🚫 Proteksi Role
            if ($role_id > 3) {
                return $this->response->setJSON([
                    'status' => 'forbidden',
                    'message' => 'Anda tidak memiliki izin untuk mengubah kategori desil.'
                ]);
            }

            $idKk = (int) $this->request->getPost('id_kk');
            $kategoriDesil = (int) $this->request->getPost('kategori_desil');

            // 🔎 Validasi input
            if (!$idKk || $kategoriDesil < 1 || $kategoriDesil > 10) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data desil tidak valid.'
                ]);
            }

            $db = db_connect();
            $db->transBegin();

            // =========================
            // 1️⃣ UPDATE dtsen_se
            // =========================
            $cek = $db->table('dtsen_se')
                ->where('id_kk', $idKk)
                ->get()
                ->getRow();

            if ($cek) {
                $db->table('dtsen_se')
                    ->where('id_kk', $idKk)
                    ->update([
                        'kategori_desil' => $kategoriDesil,
                        'updated_at'     => date('Y-m-d H:i:s')
                    ]);
            } else {
                $db->table('dtsen_se')->insert([
                    'id_kk'           => $idKk,
                    'kategori_desil'  => $kategoriDesil,
                    'created_at'      => date('Y-m-d H:i:s')
                ]);
            }

            // =========================
            // 2️⃣ TENTUKAN TRIWULAN BERJALAN
            // =========================
            $bulan = (int) date('n');
            $tahun = (int) date('Y');
            $triwulan = (int) ceil($bulan / 3);
            $label = 'TW' . $triwulan . ' ' . $tahun;

            // =========================
            // INSERT / UPDATE HISTORI
            // =========================

            $exists = $db->table('dtsen_desil_history')
                ->where([
                    'id_kk'    => $idKk,
                    'tahun'    => $tahun,
                    'triwulan' => $triwulan
                ])
                ->get()
                ->getRow();

            if ($exists) {

                $db->table('dtsen_desil_history')
                    ->where('id', $exists->id)
                    ->update([
                        'desil'      => $kategoriDesil,
                        'created_by' => $userId
                    ]);
            } else {

                $db->table('dtsen_desil_history')->insert([
                    'id_kk'         => $idKk,
                    'desil'         => $kategoriDesil,
                    'tahun'         => $tahun,
                    'triwulan'      => $triwulan,
                    'periode_label' => $label,
                    'source'        => 'manual_input',
                    'created_by'    => $userId
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status' => 'success'
            ]);
        } catch (\Throwable $e) {

            if (isset($db)) $db->transRollback();

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

    // app/Controllers/Dtsen/DtsenSe.php
    public function listRW()
    {
        $session  = session();
        $kodeDesa = $session->get('kode_desa');

        $db = \Config\Database::connect();

        $rows = $db->table('tb_rt')
            ->select('no_rw')
            ->distinct()
            ->where('kode_desa', $kodeDesa)
            ->orderBy('no_rw', 'ASC')
            ->get()
            ->getResultArray();

        $data = array_map(fn($r) => $r['no_rw'], $rows);

        return $this->response->setJSON(['data' => $data]);
    }

    public function listRT($rw)
    {
        $session  = session();
        $kodeDesa = $session->get('kode_desa');

        $db = \Config\Database::connect();

        $rows = $db->table('tb_rt')
            ->select('no_rt')
            ->where([
                'kode_desa' => $kodeDesa,
                'no_rw'     => $rw
            ])
            ->orderBy('no_rt', 'ASC')
            ->get()
            ->getResultArray();

        $data = array_map(fn($r) => $r['no_rt'], $rows);

        return $this->response->setJSON(['data' => $data]);
    }

    // ========================================================
    // 🚑 QUERY TABEL PEMULIHAN DATA WILAYAH
    // ========================================================
    public function tabel_pemulihan()
    {
        try {
            $db = \Config\Database::connect();

            // Ambil data KK dan join dengan dtsen_rt
            $builder = $db->table('dtsen_kk k')
                ->select('k.id_kk, k.no_kk, k.kepala_keluarga, k.alamat as alamat_kk, r.id_rt, r.rt, r.rw, r.alamat as alamat_rt')
                ->join('dtsen_rt r', 'k.id_rt = r.id_rt', 'left')
                ->groupStart()
                ->where('r.rt', null)
                ->orWhere('r.rt', '')
                ->orWhere('r.rw', null)
                ->orWhere('r.rw', '')
                ->orWhere('LENGTH(r.rt) <', 3)
                ->orWhere('LENGTH(r.rw) <', 3)
                ->groupEnd();

            $dataBermasalah = $builder->get()->getResultArray();

            return $this->response->setJSON(['data' => $dataBermasalah]);
        } catch (\Throwable $e) {
            log_message('error', '❌ tabel_pemulihan() error: ' . $e->getMessage());
            return $this->response->setJSON(['data' => [], 'error' => true]);
        }
    }

    // ========================================================
    // 🪄 EKSEKUSI AUTO-FIX DARI MODAL FRONTEND
    // ========================================================
    public function autofix_rt_rw()
    {
        $idRt  = $this->request->getPost('id_rt');
        $rtFix = $this->request->getPost('rt_baru');
        $rwFix = $this->request->getPost('rw_baru');

        if (!$idRt) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID RT tidak valid.']);
        }

        try {
            $this->db->table('dtsen_rt')->where('id_rt', $idRt)->update([
                'rt' => $rtFix, // Sudah diformat 3 digit dari Frontend
                'rw' => $rwFix,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session()->get('id') ?? 'system'
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Format RT/RW berhasil dipulihkan!']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal memulihkan: ' . $e->getMessage()]);
        }
    }
}
