<?php

namespace App\Controllers\Dtsen;

use App\Models\Dtks\AuthModel;
use App\Models\Dtsen\DtsenArtModel;
use App\Models\Dtsen\DtsenSeModel;
use App\Models\Dtsen\DtsenUsulanBansosModel;
use App\Models\GenModel;
use App\Models\Dtks\Usulan22Model;
use App\Models\Dtks\BansosModel;
use CodeIgniter\Controller;

class UsulanBansos extends Controller
{
    protected $AuthModel;
    protected $artModel;
    protected $seModel;
    protected $usulanModel;
    protected $DtsenUsulanBansosModel;
    protected $BansosModel;
    protected $GenModel;
    protected $session;
    protected $request;
    protected $response;
    protected $validation;
    protected $db;

    public function __construct()
    {
        $this->AuthModel = new AuthModel();
        $this->artModel = new DtsenArtModel();
        $this->seModel = new DtsenSeModel();
        $this->usulanModel = new Usulan22Model();
        $this->DtsenUsulanBansosModel = new DtsenUsulanBansosModel();
        $this->BansosModel = new BansosModel();
        $this->GenModel = new GenModel();
        $this->db = \Config\Database::connect();

        helper(['opdtks_helper', 'opdtsen_helper']);
    }

    /**
     * 🏠 Halaman utama daftar usulan bansos
     */
    public function index()
    {
        $session = session();

        // 🔹 Ambil data user dari model Auth
        $userInfo = $this->AuthModel->getUserId();

        // 🔹 Ambil daftar program bansos dari database
        $bansosModel = new BansosModel();
        $bansos = $bansosModel->select('dbj_id, dbj_nama_bansos')
            ->where('is_active', 1)
            ->orderBy('dbj_id', 'ASC')
            ->findAll();

        // dd($bansos);

        // 🔹 Parsing wilayah_tugas → multi RW–RT (FINAL)
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');
        $wilayahAktif = [];

        if (!empty($wilayahTugas)) {
            // contoh: 001:005,007|004:002
            $blocks = explode('|', $wilayahTugas);

            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);

                if ($rw !== '') {
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    $wilayahAktif[] = [
                        'rw'  => $rw,
                        'rts' => $rts
                    ];
                }
            }
        }

        // 🔹 Gabungkan data session dan database user
        $userData = array_merge(
            [
                'nama'        => $session->get('fullname') ?? '-',
                'username'    => $session->get('username') ?? '-',
                'kode_desa'   => $session->get('kode_desa') ?? '',
                'role_id'     => $session->get('role_id') ?? '',
                'level'       => $session->get('level') ?? '',
                'nik'         => $session->get('nik') ?? '',
                'user_image'  => 'default.png', // fallback default
            ],
            is_array($userInfo) ? $userInfo : []
        );

        $data = [
            'title'           => 'Usulan Bansos',
            'namaApp'         => nameApp(),
            'bansos'          => $bansos,
            'user_login'      => $userData,
            'wilayah_aktif'   => $wilayahAktif, // ✅ GANTI INI
            'statusRole'      => $this->GenModel->getStatusRole(),
            // 🚀 SUNTIKAN DATA DEADLINE UNTUK OFFCANVAS PENGATURAN AKSES
            'deadline'         => $this->GenModel->getDeadline(),
            'deadline_general' => $this->GenModel->getDeadlinePpks(),
        ];

        return view('dtsen/usulan_bansos/v_usulan_bansos', $data);
    }

    // /**
    //  * 🔍 Cari individu berdasarkan NIK/Nama
    //  * 🔐 Dibatasi wilayah_tugas user login (multi RW–RT)
    //  * 🛡️ Anti Data Ganda & Bypass Soft Delete
    //  */
    // public function searchArt()
    // {
    //     $term = trim($this->request->getGet('q'));
    //     $user = $this->AuthModel->getUserId();
    //     $wilayahTugas = trim($user['wilayah_tugas'] ?? '');

    //     $builder = $this->artModel
    //         ->select("
    //         dtsen_art.nik,
    //         dtsen_art.nama,
    //         dtsen_art.hubungan_keluarga AS shdk,
    //         (SELECT jenis_shdk FROM tb_shdk WHERE id = dtsen_art.hubungan_keluarga) AS shdk_nama,
    //         dtsen_rt.rw,
    //         dtsen_rt.rt
    //     ")
    //         // 🚀 PERBAIKAN 1: Pastikan KK yang ditarik adalah KK yang masih aktif
    //         ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk AND dtsen_kk.deleted_at IS NULL', 'left')
    //         ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')

    //         // 🚀 PERBAIKAN 2: Gunakan sintaks string statis agar Query Builder mutlak membaca IS NULL
    //         ->where('dtsen_art.deleted_at IS NULL')
    //         ->groupStart()
    //         ->like('dtsen_art.nik', $term)
    //         // ->orLike('dtsen_art.nama', $term)
    //         ->groupEnd()

    //         // 🚀 PERBAIKAN 3: Kunci mutlak NIK agar tidak ada nama yang sama muncul dua kali
    //         ->groupBy('dtsen_art.nik')
    //         ->limit(10);

    //     /* =====================================================
    //    🔐 PARSE wilayah_tugas → pasangan RW–RT (KUNCI)
    //    ===================================================== */
    //     if (!empty($wilayahTugas)) {

    //         $wilayahPairs = [];

    //         // contoh: 001:005,007|004:002
    //         $blocks = explode('|', $wilayahTugas);

    //         foreach ($blocks as $block) {
    //             [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
    //             $rw = trim($rw);

    //             foreach (explode(',', $rtList) as $rt) {
    //                 $rt = trim($rt);
    //                 if ($rw !== '' && $rt !== '') {
    //                     $wilayahPairs[] = [
    //                         'rw' => $rw,
    //                         'rt' => $rt
    //                     ];
    //                 }
    //             }
    //         }

    //         if (!empty($wilayahPairs)) {
    //             $builder->groupStart();
    //             foreach ($wilayahPairs as $pair) {
    //                 $builder->orGroupStart()
    //                     ->where('dtsen_rt.rw', $pair['rw'])
    //                     ->where('dtsen_rt.rt', $pair['rt'])
    //                     ->groupEnd();
    //             }
    //             $builder->groupEnd();
    //         }
    //     }

    //     $rows = $builder->get()->getResultArray();

    //     /* =====================================================
    //    🎯 Format JSON sesuai standar Select2
    //    ===================================================== */
    //     $results = array_map(function ($row) {
    //         return [
    //             'id'         => $row['nik'],
    //             'text'       => $row['nik'] . ' - ' . strtoupper($row['nama']),
    //             'nik'        => $row['nik'],
    //             'nama'       => $row['nama'],
    //             'shdk'       => (int) ($row['shdk'] ?? 0),
    //             'shdk_nama'  => $row['shdk_nama'] ?? '-',
    //             'rw'         => $row['rw'] ?? '',
    //             'rt'         => $row['rt'] ?? ''
    //         ];
    //     }, $rows);

    //     return $this->response->setJSON([
    //         'results' => $results
    //     ]);
    // }

    // /**
    //  * 🔎 Cek kategori desil berdasarkan NIK → id_kk
    //  * 🔐 Terproteksi dari pengambilan KK yang sudah dihapus (Soft Delete)
    //  * Log detail setiap langkah agar mudah dilacak di writable/logs/
    //  */
    // public function checkDesil()
    // {
    //     $nik = $this->request->getGet('nik');

    //     // 🔹 Log request awal
    //     log_message('info', "[checkDesil] Request diterima. NIK: {$nik}");

    //     if (!$nik) {
    //         log_message('warning', '[checkDesil] Parameter NIK kosong.');
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Parameter NIK tidak diberikan.'
    //         ]);
    //     }

    //     try {
    //         // 1️⃣ Ambil id_kk dari dtsen_art dan pastikan KK-nya berstatus AKTIF
    //         $art = $this->artModel
    //             ->select('dtsen_art.id_kk')
    //             // 🚀 PERBAIKAN: Join ke dtsen_kk untuk mengecek status hapusnya
    //             ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk', 'left')
    //             ->where('dtsen_art.nik', $nik)
    //             // 🚀 KUNCI UTAMA: Pastikan baik Individu maupun KK-nya tidak berstatus terhapus
    //             ->where('dtsen_art.deleted_at IS NULL')
    //             ->where('dtsen_kk.deleted_at IS NULL')
    //             ->orderBy('dtsen_art.id_art', 'DESC') // Opsional: Ambil data paling baru jika ada ganda
    //             ->first();

    //         log_message('info', '[checkDesil] Hasil query ART (Hanya KK Aktif): ' . json_encode($art));

    //         if (!$art || empty($art['id_kk'])) {
    //             log_message('warning', "[checkDesil] Tidak ditemukan ART aktif untuk NIK {$nik}");
    //             return $this->response->setJSON([
    //                 'success' => false,
    //                 'message' => 'Data individu tidak ditemukan atau KK sudah tidak aktif.'
    //             ]);
    //         }

    //         // 2️⃣ Ambil kategori_desil dari dtsen_se berdasarkan id_kk yang valid/aktif
    //         $se = $this->seModel
    //             ->select('kategori_desil')
    //             ->where('id_kk', $art['id_kk'])
    //             ->first();

    //         log_message('info', "[checkDesil] Query SE untuk id_kk={$art['id_kk']} hasil: " . json_encode($se));

    //         // 3️⃣ Validasi hasil query
    //         if ($se && isset($se['kategori_desil'])) {
    //             $desil = $se['kategori_desil'];
    //             log_message('info', "[checkDesil] Ditemukan kategori_desil={$desil} untuk NIK {$nik}");

    //             return $this->response->setJSON([
    //                 'success' => true,
    //                 'kategori_desil' => $desil
    //             ]);
    //         }

    //         // 4️⃣ Jika tidak ditemukan
    //         log_message('warning', "[checkDesil] Data desil tidak ditemukan untuk id_kk={$art['id_kk']} (NIK {$nik})");
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Data desil tidak tersedia untuk KK ini.'
    //         ]);
    //     } catch (\Throwable $e) {
    //         // 5️⃣ Tangani error fatal
    //         log_message('error', "[checkDesil] ERROR: {$e->getMessage()} | Trace: {$e->getTraceAsString()}");
    //         return $this->response->setStatusCode(500)->setJSON([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
    //         ]);
    //     }
    // }

    // /**
    //  * 💾 Simpan usulan bansos baru
    //  * 🔐 Dilengkapi validasi SHDK–DESIL–PROGRAM
    //  */
    // public function save()
    // {
    //     $request = service('request');

    //     $nik      = $request->getPost('nik_peserta');
    //     $program  = (int) $request->getPost('program_bansos');
    //     $catatan  = $request->getPost('catatan');

    //     $db = db_connect();

    //     /* =====================================================
    //    1️⃣ Validasi ART (Terproteksi dari KK yang terhapus)
    //    ===================================================== */
    //     $art = $db->table('dtsen_art')
    //         ->select('dtsen_art.*') // Ambil semua data ART
    //         ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk', 'left') // 🚀 Gabungkan dengan tabel KK
    //         ->where('dtsen_art.nik', $nik)
    //         ->where('dtsen_art.deleted_at IS NULL')
    //         ->where('dtsen_kk.deleted_at IS NULL') // 🚀 KUNCI: Pastikan KK masih aktif
    //         ->orderBy('dtsen_art.id_art', 'DESC') // Ambil data ter-update
    //         ->get()
    //         ->getRowArray();

    //     if (!$art) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Data individu tidak ditemukan atau KK sudah tidak aktif.'
    //         ]);
    //     }

    //     /* =====================================================
    //    2️⃣ Validasi DESIL & PROGRAM BANSOS (Aturan Kemensos)
    //    ===================================================== */
    //     $se = $db->table('dtsen_se')
    //         ->where('id_kk', $art['id_kk'])
    //         ->get()
    //         ->getRowArray();

    //     $desil = (int) ($se['kategori_desil'] ?? 0);

    //     // 🔴 Tolak jika Desil 0 atau lebih dari 5 (misal: 6, 7, dst)
    //     if ($desil <= 0 || $desil > 5) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => "Kategori Desil {$desil} tidak memenuhi syarat kelayakan usulan bansos."
    //         ]);
    //     }

    //     // 🔵 Aturan Khusus Desil 5: HANYA BOLEH PBI-JK
    //     // Asumsi nilai opsi/value untuk PBI di database dbj_id adalah 5
    //     if ($desil === 5 && $program !== 5) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Desil 5 HANYA diperbolehkan untuk program PBI-JK.'
    //         ]);
    //     }

    //     /* =====================================================
    //    3️⃣ VALIDASI SHDK × DESIL × PROGRAM (KUNCI)
    //    ===================================================== */
    //     $shdk = (int) ($art['hubungan_keluarga'] ?? 0);

    //     // SHDK selain KK & Istri → hanya PBI
    //     if ($shdk !== 1 && $shdk !== 3) {
    //         if ($program !== 5) {
    //             return $this->response->setJSON([
    //                 'success' => false,
    //                 'message' => 'Program hanya diperbolehkan PBI untuk individu ini.'
    //             ]);
    //         }
    //     }

    //     /* =====================================================
    //    4️⃣ Cek Duplikasi Bulan Berjalan
    //    ===================================================== */
    //     $cekDuplikat = $db->table('dtsen_usulan_bansos')
    //         ->where('nik', $nik)
    //         ->where('MONTH(created_at)', date('m'))
    //         ->where('YEAR(created_at)', date('Y'))
    //         ->countAllResults();

    //     if ($cekDuplikat > 0) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Usulan untuk NIK ini sudah pernah dibuat bulan ini.'
    //         ]);
    //     }

    //     /* =====================================================
    //    5️⃣ Simpan Data
    //    ===================================================== */
    //     $this->DtsenUsulanBansosModel->insert([
    //         'id_kk'          => $art['id_kk'],
    //         'nik'            => $nik,
    //         'program_bansos' => $program,
    //         'catatan'        => $catatan,
    //         'status'         => 'draft',
    //         'created_at'     => date('Y-m-d H:i:s'),
    //         'created_by'     => session()->get('nik') ?? 'system',
    //     ]);

    //     /* =====================================================
    //    6️⃣ Update Flag ART
    //    ===================================================== */
    //     $db->table('dtsen_art')
    //         ->where('id_art', $art['id_art'])
    //         ->update([
    //             'usulan_status'    => 'draft',
    //             'is_usulan_bansos' => 1
    //         ]);

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'message' => 'Usulan bansos berhasil disimpan!'
    //     ]);
    // }
    /**
     * 🔍 Cari individu berdasarkan NIK/Nama
     * 🔐 Dibatasi wilayah_tugas user login (multi RW–RT) & Kode Desa
     * 🛡️ Anti Data Ganda, Bypass Soft Delete, & Info Status PBI Aktif
     */
    // public function searchArt()
    // {
    //     $term = trim((string) $this->request->getGet('q'));
    //     $user = $this->AuthModel->getUserId();
    //     $kodeDesa = $user['kode_desa'] ?? session()->get('kode_desa');
    //     $wilayahTugas = trim($user['wilayah_tugas'] ?? '');

    //     $builder = $this->artModel
    //         ->select("
    //             dtsen_art.nik,
    //             dtsen_art.nama,
    //             dtsen_art.hubungan_keluarga AS shdk,
    //             (SELECT jenis_shdk FROM tb_shdk WHERE id = dtsen_art.hubungan_keluarga) AS shdk_nama,
    //             dtsen_rt.rw,
    //             dtsen_rt.rt,
    //             pbi.status_kepesertaan AS pbi_status,
    //             pbi.jenis_kepesertaan AS pbi_jenis
    //         ")
    //         ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk AND dtsen_kk.deleted_at IS NULL', 'left')
    //         ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
    //         // 🚀 INTEGRASI PBI: Cek apakah NIK ini berstatus Aktif di pbi_master_data
    //         ->join('pbi_master_data pbi', "pbi.nik = dtsen_art.nik AND pbi.status_kepesertaan = 'Aktif'", 'left')
    //         ->where('dtsen_art.deleted_at IS NULL')
    //         ->where('dtsen_kk.deleted_at IS NULL');

    //     // 🔐 Kunci kepemilikan data level desa
    //     if (!empty($kodeDesa)) {
    //         $builder->where('dtsen_rt.kode_desa', $kodeDesa);
    //     }

    //     $builder->groupStart()
    //         ->like('dtsen_art.nik', $term)
    //         ->groupEnd()
    //         ->groupBy('dtsen_art.nik')
    //         ->limit(10);

    //     /* =====================================================
    //        🔐 PARSE wilayah_tugas → pasangan RW–RT (KUNCI)
    //        ===================================================== */
    //     if (!empty($wilayahTugas)) {
    //         $wilayahPairs = [];
    //         $blocks = explode('|', $wilayahTugas);

    //         foreach ($blocks as $block) {
    //             [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
    //             $rw = trim($rw);

    //             foreach (explode(',', $rtList) as $rt) {
    //                 $rt = trim($rt);
    //                 if ($rw !== '' && $rt !== '') {
    //                     $wilayahPairs[] = [
    //                         'rw' => $rw,
    //                         'rt' => $rt
    //                     ];
    //                 }
    //             }
    //         }

    //         if (!empty($wilayahPairs)) {
    //             $builder->groupStart();
    //             foreach ($wilayahPairs as $pair) {
    //                 $builder->orGroupStart()
    //                     ->where('dtsen_rt.rw', $pair['rw'])
    //                     ->where('dtsen_rt.rt', $pair['rt'])
    //                     ->groupEnd();
    //             }
    //             $builder->groupEnd();
    //         }
    //     }

    //     $rows = $builder->get()->getResultArray();

    //     $results = array_map(function ($row) {
    //         $isPbiAktif = (strtoupper(trim($row['pbi_status'] ?? '')) === 'AKTIF');
    //         return [
    //             'id'           => $row['nik'],
    //             'text'         => $row['nik'] . ' - ' . strtoupper($row['nama']),
    //             'nik'          => $row['nik'],
    //             'nama'         => $row['nama'],
    //             'shdk'         => (int) ($row['shdk'] ?? 0),
    //             'shdk_nama'    => $row['shdk_nama'] ?? '-',
    //             'rw'           => $row['rw'] ?? '',
    //             'rt'           => $row['rt'] ?? '',
    //             'is_pbi_aktif' => $isPbiAktif,
    //             'pbi_jenis'    => $row['pbi_jenis'] ?? 'PBI-JKN'
    //         ];
    //     }, $rows);

    //     return $this->response->setJSON([
    //         'results' => $results
    //     ]);
    // }
    /**
     * 🔍 Cari individu berdasarkan NIK
     * 🔐 Dibatasi wilayah_tugas user login (multi RW–RT) & Kode Desa
     * 🛡️ Anti Data Ganda, Bypass Soft Delete, & Info Status PBI Aktif
     */
    public function searchArt()
    {
        // Bersihkan input agar murni hanya mengeksekusi angka NIK
        $term = preg_replace('/\D/', '', trim((string) $this->request->getGet('q')));
        if (strlen($term) < 3) {
            return $this->response->setJSON(['results' => []]);
        }

        $user = $this->AuthModel->getUserId();
        $kodeDesa = $user['kode_desa'] ?? session()->get('kode_desa');
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');

        $builder = $this->artModel
            ->select("
                dtsen_art.nik,
                dtsen_art.nama,
                dtsen_art.hubungan_keluarga AS shdk,
                (SELECT jenis_shdk FROM tb_shdk WHERE id = dtsen_art.hubungan_keluarga) AS shdk_nama,
                dtsen_rt.rw,
                dtsen_rt.rt,
                pbi.status_kepesertaan AS pbi_status,
                pbi.jenis_kepesertaan AS pbi_jenis
            ")
            ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk AND dtsen_kk.deleted_at IS NULL', 'left')
            ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
            // 🚀 INTEGRASI PBI: Cek apakah NIK ini berstatus Aktif di pbi_master_data
            ->join('pbi_master_data pbi', "pbi.nik = dtsen_art.nik AND pbi.status_kepesertaan = 'Aktif'", 'left')
            ->where('dtsen_art.deleted_at IS NULL')
            ->where('dtsen_kk.deleted_at IS NULL');

        // 🔐 Kunci kepemilikan data level desa
        if (!empty($kodeDesa)) {
            $builder->where('dtsen_rt.kode_desa', $kodeDesa);
        }

        // 🎯 Pencarian murni berdasarkan NIK
        $builder->like('dtsen_art.nik', $term)
            ->groupBy('dtsen_art.nik')
            ->limit(10);

        /* =====================================================
           🔐 PARSE wilayah_tugas → pasangan RW–RT (KUNCI)
           ===================================================== */
        if (!empty($wilayahTugas)) {
            $wilayahPairs = [];
            $blocks = explode('|', $wilayahTugas);

            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);

                foreach (explode(',', $rtList) as $rt) {
                    $rt = trim($rt);
                    if ($rw !== '' && $rt !== '') {
                        $wilayahPairs[] = [
                            'rw' => $rw,
                            'rt' => $rt
                        ];
                    }
                }
            }

            if (!empty($wilayahPairs)) {
                $builder->groupStart();
                foreach ($wilayahPairs as $pair) {
                    $builder->orGroupStart()
                        ->where('dtsen_rt.rw', $pair['rw'])
                        ->where('dtsen_rt.rt', $pair['rt'])
                        ->groupEnd();
                }
                $builder->groupEnd();
            }
        }

        $rows = $builder->get()->getResultArray();

        $results = array_map(function ($row) {
            $isPbiAktif = (strtoupper(trim($row['pbi_status'] ?? '')) === 'AKTIF');
            return [
                'id'           => $row['nik'],
                'text'         => $row['nik'] . ' - ' . strtoupper($row['nama']),
                'nik'          => $row['nik'],
                'nama'         => $row['nama'],
                'shdk'         => (int) ($row['shdk'] ?? 0),
                'shdk_nama'    => $row['shdk_nama'] ?? '-',
                'rw'           => $row['rw'] ?? '',
                'rt'           => $row['rt'] ?? '',
                'is_pbi_aktif' => $isPbiAktif,
                'pbi_jenis'    => $row['pbi_jenis'] ?? 'PBI-JKN'
            ];
        }, $rows);

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    /**
     * 🔎 Cek kategori desil & Status PBI Aktif berdasarkan NIK
     * 🔐 Terproteksi kepemilikan wilayah desa & Soft Delete KK
     */
    public function checkDesil()
    {
        $nik = trim((string) $this->request->getGet('nik'));
        $kodeDesa = session()->get('kode_desa');

        if (!$nik) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter NIK tidak diberikan.'
            ]);
        }

        try {
            // 1️⃣ Ambil id_kk dari dtsen_art dan pastikan KK-nya berstatus AKTIF & sesuai Desa
            $artBuilder = $this->artModel
                ->select('dtsen_art.id_kk, dtsen_rt.kode_desa')
                ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk', 'left')
                ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
                ->where('dtsen_art.nik', $nik)
                ->where('dtsen_art.deleted_at IS NULL')
                ->where('dtsen_kk.deleted_at IS NULL');

            if (!empty($kodeDesa)) {
                $artBuilder->where('dtsen_rt.kode_desa', $kodeDesa);
            }

            $art = $artBuilder->orderBy('dtsen_art.id_art', 'DESC')->first();

            if (!$art || empty($art['id_kk'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data individu tidak ditemukan di wilayah Anda atau KK sudah tidak aktif.'
                ]);
            }

            // 2️⃣ Ambil kategori_desil dari dtsen_se
            $se = $this->seModel
                ->select('kategori_desil')
                ->where('id_kk', $art['id_kk'])
                ->first();

            // 3️⃣ 🚀 CEK STATUS KEPESERTAAN DI PBI MASTER DATA
            $pbiBuilder = $this->db->table('pbi_master_data')
                ->select('id, no_kis, jenis_kepesertaan, status_kepesertaan')
                ->where('nik', $nik)
                ->where('status_kepesertaan', 'Aktif');

            if (!empty($kodeDesa)) {
                $pbiBuilder->where('kode_desa', $kodeDesa);
            }

            $pbiAktif = $pbiBuilder->get()->getRowArray();

            if ($se && isset($se['kategori_desil'])) {
                return $this->response->setJSON([
                    'success'        => true,
                    'kategori_desil' => (int) $se['kategori_desil'],
                    'is_pbi_aktif'   => !empty($pbiAktif),
                    'pbi_info'       => $pbiAktif ? [
                        'no_kis'            => $pbiAktif['no_kis'] ?? '-',
                        'jenis_kepesertaan' => $pbiAktif['jenis_kepesertaan'] ?? 'PBI-JKN'
                    ] : null
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data desil tidak tersedia untuk KK ini.'
            ]);
        } catch (\Throwable $e) {
            log_message('error', "[checkDesil] ERROR: {$e->getMessage()}");
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat memeriksa kelayakan.'
            ]);
        }
    }

    /**
     * 💾 Simpan usulan bansos baru
     * 🔐 Dilengkapi validasi SHDK – DESIL – PROGRAM – MASTER PBI AKTIF
     */
    public function save()
    {
        $request = service('request');

        $nik     = preg_replace('/\D/', '', trim((string) $request->getPost('nik_peserta')));
        $program = (int) $request->getPost('program_bansos');
        $catatan = trim(strip_tags((string) $request->getPost('catatan')));

        $kodeDesa = session()->get('kode_desa');
        $db = db_connect();

        /* =====================================================
           1️⃣ Validasi ART & Kepemilikan Wilayah Desa
           ===================================================== */
        $artBuilder = $db->table('dtsen_art')
            // 🚀 PERBAIKAN: Tambahkan dtsen_kk.no_kk di sini agar Nomor KK ikut terbawa
            ->select('dtsen_art.*, dtsen_kk.no_kk, dtsen_rt.kode_desa')
            ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk', 'left')
            ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
            ->where('dtsen_art.nik', $nik)
            ->where('dtsen_art.deleted_at IS NULL')
            ->where('dtsen_kk.deleted_at IS NULL');

        if (!empty($kodeDesa)) {
            $artBuilder->where('dtsen_rt.kode_desa', $kodeDesa);
        }

        $art = $artBuilder->orderBy('dtsen_art.id_art', 'DESC')->get()->getRowArray();

        if (!$art) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data individu tidak ditemukan atau di luar wilayah kerja Anda.'
            ]);
        }

        /* =====================================================
           2️⃣ Cek Nama Program yang Dipilih (Deteksi Apakah PBI)
           ===================================================== */
        $rowProgram = $db->table('dtks_bansos_jenis')
            ->select('dbj_id, dbj_nama_bansos')
            ->where('dbj_id', $program)
            ->get()
            ->getRowArray();

        if (!$rowProgram) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program bantuan sosial tidak valid.'
            ]);
        }

        // Deteksi apakah program ini adalah PBI (baik berdasarkan ID = 5 maupun teks 'PBI')
        $namaProgramUpper = strtoupper(trim($rowProgram['dbj_nama_bansos'] ?? ''));
        $isProgramPbi = ($program === 5 || strpos($namaProgramUpper, 'PBI') !== false);

        /* =====================================================
           3️⃣ 🚀 GATEKEEPER PBI AKTIF: Tolak jika sudah PBI Aktif!
           ===================================================== */
        if ($isProgramPbi) {
            $cekPbiBuilder = $db->table('pbi_master_data')
                ->select('id, jenis_kepesertaan, no_kis')
                ->where('nik', $nik)
                ->where('status_kepesertaan', 'Aktif');

            if (!empty($kodeDesa)) {
                $cekPbiBuilder->where('kode_desa', $kodeDesa);
            }

            $sudahPbiAktif = $cekPbiBuilder->get()->getRowArray();

            if ($sudahPbiAktif) {
                $jenisPbi = $sudahPbiAktif['jenis_kepesertaan'] ?: 'PBI-JKN';
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Pengajuan ditolak! Warga dengan NIK {$nik} sudah terdaftar sebagai peserta {$jenisPbi} AKTIF."
                ]);
            }
        }

        /* =====================================================
           4️⃣ Validasi DESIL & PROGRAM BANSOS (Aturan Kemensos)
           ===================================================== */
        $se = $db->table('dtsen_se')
            ->where('id_kk', $art['id_kk'])
            ->get()
            ->getRowArray();

        $desil = (int) ($se['kategori_desil'] ?? 0);

        if ($desil <= 0 || $desil > 5) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Kategori Desil {$desil} tidak memenuhi syarat kelayakan usulan bansos."
            ]);
        }

        if ($desil === 5 && !$isProgramPbi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Desil 5 HANYA diperbolehkan untuk program PBI-JK.'
            ]);
        }

        /* =====================================================
           5️⃣ VALIDASI SHDK × DESIL × PROGRAM
           ===================================================== */
        $shdk = (int) ($art['hubungan_keluarga'] ?? 0);

        if ($shdk !== 1 && $shdk !== 3) {
            if (!$isProgramPbi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anggota keluarga selain Kepala Keluarga & Istri hanya diperbolehkan untuk usulan PBI.'
                ]);
            }
        }

        // /* =====================================================
        //    6️⃣ Cek Duplikasi Bulan Berjalan
        //    ===================================================== */
        // $cekDuplikat = $db->table('dtsen_usulan_bansos')
        //     ->where('nik', $nik)
        //     ->where('MONTH(created_at)', date('m'))
        //     ->where('YEAR(created_at)', date('Y'))
        //     ->countAllResults();

        // if ($cekDuplikat > 0) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Usulan untuk NIK ini sudah pernah dibuat pada bulan ini.'
        //     ]);
        // }
        /* =====================================================
           4️⃣ 🚀 CEK DUPLIKASI & PEMBATASAN KELUARGA (BERBASIS NO KK)
           - PKH / Sembako (Non-PBI): Maksimal 1 usulan per No. KK per bulan!
           - PBI-JK: Pengecualian, boleh multiple dalam 1 KK.
           ===================================================== */
        $idKk = $art['id_kk'];
        $noKk = $art['no_kk'] ?? '-';

        // Ambil daftar usulan pada KK yang sama di bulan & tahun berjalan
        $builderCekKk = $db->table('dtsen_usulan_bansos us')
            ->select('us.id, us.nik, us.program_bansos, dbj.dbj_nama_bansos')
            ->join('dtsen_art a', 'a.nik = us.nik', 'left')
            // 🚀 INI BARIS PENYELAMATNYA: Join tabel bansos agar alias 'dbj' dikenali database
            ->join('dtks_bansos_jenis dbj', 'dbj.dbj_id = us.program_bansos', 'left')
            ->where('a.id_kk', $idKk)
            ->where('MONTH(us.created_at)', date('m'))
            ->where('YEAR(us.created_at)', date('Y'));

        $usulanExistingKk = $builderCekKk->get()->getResultArray();

        foreach ($usulanExistingKk as $existing) {
            // Jika NIK yang diusulkan persis sama (duplikasi individu langsung)
            if ($existing['nik'] === $nik) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usulan untuk NIK ini sudah pernah dibuat pada bulan ini.'
                ]);
            }

            // Jika program yang ingin diajukan saat ini BUKAN PBI, 
            // DAN di KK ini sudah ada usulan non-PBI (PKH/Sembako) pada bulan ini -> TOLAK!
            $existingNamaProg = strtoupper(trim($existing['dbj_nama_bansos'] ?? ''));
            $existingIsPbi    = ($existing['program_bansos'] === 5 || strpos($existingNamaProg, 'PBI') !== false);

            if (!$isProgramPbi && !$existingIsPbi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Pengajuan ditolak! Dalam 1 Keluarga (No. KK: {$noKk}) pada bulan ini sudah terdapat usulan bansos reguler ({$existing['dbj_nama_bansos']}). Setiap KK hanya diperbolehkan 1 usulan PKH/Sembako per periode."
                ]);
            }
        }

        /* =====================================================
           7️⃣ Simpan Data & Update Flag ART
           ===================================================== */
        $this->DtsenUsulanBansosModel->insert([
            'id_kk'          => $art['id_kk'],
            'nik'            => $nik,
            'program_bansos' => $program,
            'catatan'        => $catatan,
            'status'         => 'draft',
            'created_at'     => date('Y-m-d H:i:s'),
            'created_by'     => session()->get('nik') ?? 'system',
        ]);

        $db->table('dtsen_art')
            ->where('id_art', $art['id_art'])
            ->update([
                'usulan_status'    => 'draft',
                'is_usulan_bansos' => 1
            ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Usulan bansos berhasil disimpan!'
        ]);
    }

    /**
     * 🗑️ Hapus usulan bansos
     */
    public function delete($id = null)
    {
        try {
            $session = session();
            $role_id = (int) $session->get('role_id');

            // 🔒 Proteksi: hanya operator dan di bawahnya (role_id ≤ 4)
            if ($role_id > 4) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus data.'
                ]);
            }

            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID usulan tidak valid.'
                ]);
            }

            $model = new DtsenUsulanBansosModel();
            $data = $model->find($id);

            if (!$data) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data usulan tidak ditemukan.'
                ]);
            }

            // 🚮 Lakukan penghapusan
            $model->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data usulan berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {
            // return $this->response->setJSON([
            //     'success' => false,
            //     'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            // ]);
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data berhasil dihapus.',
                'csrfToken' => csrf_hash()
            ]);
        }
    }

    public function getDataBulanIni()
    {
        $session = session();
        $roleId = (int) $session->get('role_id');
        $nik = $session->get('nik');

        $bulan  = $this->request->getVar('bulan');
        $tahun  = $this->request->getVar('tahun');

        $status     = $this->request->getVar('status');
        $program    = $this->request->getVar('program');
        $createdBy  = $this->request->getVar('created_by');
        $rw         = $this->request->getVar('rw');
        $rt         = $this->request->getVar('rt');

        // 🔧 INIT BUILDER DULU (WAJIB DI ATAS)
        $builder = $this->DtsenUsulanBansosModel
            ->select("
            dtsen_usulan_bansos.*,
            dtsen_kk.no_kk, 
            dtsen_art.nama,
            dbj.dbj_nama_bansos,
            u1.fullname AS created_by_name,
            u1.nope    AS created_by_nope,
            u2.fullname AS updated_by_name
        ")
            // 🚀 BUG FIX: Filter soft-delete langsung di dalam JOIN
            ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_usulan_bansos.id_kk AND dtsen_kk.deleted_at IS NULL', 'left')
            ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
            ->join('dtsen_art', 'dtsen_art.nik = dtsen_usulan_bansos.nik AND dtsen_art.deleted_at IS NULL', 'left')
            ->join('dtks_bansos_jenis dbj', 'dbj.dbj_id = dtsen_usulan_bansos.program_bansos', 'left')
            ->join('dtks_users u1', 'u1.nik = dtsen_usulan_bansos.created_by', 'left')
            ->join('dtks_users u2', 'u2.nik = dtsen_usulan_bansos.updated_by', 'left')
            // 🚀 BUG FIX: Sabuk pengaman agar 1 ID Usulan hanya dirender 1 baris
            ->groupBy('dtsen_usulan_bansos.id');

        // 👉 DEFAULT hanya jika dua-duanya kosong
        if (empty($bulan) && empty($tahun)) {
            $bulan = date('m');
            $tahun = date('Y');
        }

        // 👉 FILTER DINAMIS
        if (!empty($bulan)) {
            $builder->where('MONTH(dtsen_usulan_bansos.created_at)', $bulan);
        }

        if (!empty($tahun)) {
            $builder->where('YEAR(dtsen_usulan_bansos.created_at)', $tahun);
        }

        // 👉 FILTER TAMBAHAN
        if (!empty($status)) {
            $builder->where('dtsen_usulan_bansos.status', $status);
        }

        if (!empty($program)) {
            $builder->where('dtsen_usulan_bansos.program_bansos', $program);
        }

        if (!empty($createdBy)) {
            $builder->where('dtsen_usulan_bansos.created_by', $createdBy);
        }

        if (!empty($rw)) {
            $builder->where('dtsen_rt.rw', $rw);
        }

        if (!empty($rt)) {
            $builder->where('dtsen_rt.rt', $rt);
        }

        // 👉 ROLE FILTER
        if ($roleId > 4) {
            log_message('info', "[getDataBulanIni] role_id={$roleId} tidak diizinkan melihat data.");
            return $this->response->setJSON(['data' => []]);
        } elseif ($roleId === 4) {
            $builder->where('dtsen_usulan_bansos.created_by', $nik);
        }

        $builder->orderBy('dtsen_usulan_bansos.created_at', 'ASC');

        try {
            $data = $builder->findAll();
            log_message('info', "✅ getDataBulanIni() memuat " . count($data) . " data untuk role={$roleId}, status={$status}, bulan={$bulan}, tahun={$tahun}");
            return $this->response->setJSON(['data' => $data]);
        } catch (\Throwable $e) {
            log_message('error', "❌ getDataBulanIni() error: " . $e->getMessage());
            return $this->response->setJSON(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: check-deadline
     * Mengembalikan { allowed: bool, start: datetime, end: datetime }
     */
    public function checkDeadline()
    {
        try {
            $roleId = session()->get('role_id');

            $db = \Config\Database::connect();
            $dataWaktu = $db->table('dtks_deadline')
                ->where('dd_role', $roleId)
                ->get()->getRowArray();

            if (!$dataWaktu) {
                return $this->response->setJSON([
                    'allowed' => false,
                    'message' => 'Deadline tidak ditemukan.'
                ]);
            }

            // Pastikan semua DateTime memakai Asia/Jakarta
            $tz = new \DateTimeZone('Asia/Jakarta');

            $start = new \DateTime($dataWaktu['dd_waktu_start'], $tz);
            $end   = new \DateTime($dataWaktu['dd_waktu_end'], $tz);
            $now   = new \DateTime('now', $tz);

            $allowed = ($now >= $start && $now <= $end);

            return $this->response->setJSON([
                'allowed' => $allowed,
                'start'   => $start->format('Y-m-d H:i:s'),
                'end'     => $end->format('Y-m-d H:i:s'),
                'now'     => $now->format('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[checkDeadline] ' . $e->getMessage());
            return $this->response->setJSON(['allowed' => false]);
        }
    }

    public function verifikasi($id)
    {
        try {
            $session = session();
            $roleId = (int) $session->get('role_id');
            $userNik = $session->get('nik') ?? $session->get('user_nik') ?? null;

            // Hanya role 1..3 yang boleh verifikasi
            if ($roleId > 3) {
                log_message('warning', "[verifikasi] Akses ditolak untuk role_id={$roleId}, user={$userNik}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak untuk melakukan verifikasi.'
                ])->setStatusCode(403);
            }

            $usulan = $this->DtsenUsulanBansosModel->find($id);
            if (!$usulan) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.'])->setStatusCode(404);
            }

            // Optional: jika ingin membatasi verifikasi hanya untuk usulan di bulan ini juga, cek tanggal created_at
            // $bulan = date('m'); $tahun = date('Y'); if (date('m', strtotime($usulan['created_at'])) != $bulan) { ... }

            // Update status
            $this->DtsenUsulanBansosModel->update($id, [
                'status'     => 'diverifikasi',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $userNik
            ]);

            // Ambil ulang data lengkap untuk WA
            $usulanLengkap = $this->DtsenUsulanBansosModel
                ->select("
                            dtsen_usulan_bansos.*,
                            dtsen_art.nama,
                            dbj.dbj_nama_bansos,
                            u1.fullname AS created_by_name,
                            u1.nope    AS created_by_nope
                        ")
                ->join('dtsen_art', 'dtsen_art.nik = dtsen_usulan_bansos.nik', 'left')
                ->join('dtks_bansos_jenis dbj', 'dbj.dbj_id = dtsen_usulan_bansos.program_bansos', 'left')
                ->join('dtks_users u1', 'u1.nik = dtsen_usulan_bansos.created_by', 'left')
                ->where('dtsen_usulan_bansos.id', $id)
                ->first();

            // Kirim WA ke petugas entri
            try {
                if (!empty($usulanLengkap['created_by_nope'])) {
                    $this->sendWaVerified($usulanLengkap);
                } else {
                    log_message('warning', "[WA Verified] Nomor petugas kosong untuk usulan ID {$id}");
                }
            } catch (\Throwable $waError) {
                log_message('error', '[WA Verified] Gagal kirim WA: ' . $waError->getMessage());
            }

            log_message('info', "[verifikasi] ID {$id} diverifikasi oleh " . ($session->get('fullname') ?? $userNik));
            return $this->response->setJSON(['success' => true, 'message' => 'Usulan berhasil diverifikasi.']);
        } catch (\Throwable $e) {
            log_message('error', "[verifikasi] Error: " . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server.']);
        }
    }

    private function sendWaVerified(array $usulan)
    {
        $nomorWA = preg_replace('/[^0-9]/', '', $usulan['created_by_nope']);

        // Normalisasi ke format 62
        if (substr($nomorWA, 0, 1) === '0') {
            $nomorWA = '62' . substr($nomorWA, 1);
        }

        // $tanggal = date('d-m-Y H:i');
        // === Format tanggal Indonesia ===
        $hari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $now  = date('Y-m-d H:i:s');
        $hariIndo = $hari[date('l', strtotime($now))];
        $tgl      = date('d', strtotime($now));
        $bln      = $bulan[intval(date('m', strtotime($now)))];
        $thn      = date('Y', strtotime($now));
        $jam      = date('H:i', strtotime($now)) . " WIB";

        $tanggalLengkap = "{$hariIndo}, {$tgl} {$bln} {$thn}, {$jam}";

        $pesan = "== SINDEN System ==\n"
            . "🔔 *Notifikasi Verifikasi Usulan Bansos*\n\n"
            . "Halo {$usulan['created_by_name']},\n\n"
            . "Usulan atas nama:\n"
            . "> Nama   : *{$usulan['nama']}*\n"
            . "> NIK    : {$usulan['nik']}\n"
            . "> Program: {$usulan['dbj_nama_bansos']}\n"
            . "> Status : *Berhasil diverifikasi*\n\n"
            . "*_Waktu: {$tanggalLengkap}_*";

        $wa = new \App\Libraries\WaService();
        $send = $wa->sendText($nomorWA, $pesan);

        log_message('info', "[WA Verified] Terkirim ke {$nomorWA}");
    }

    public function getFilterOptions()
    {
        try {

            $bulan     = date('m');
            $tahun     = date('Y');
            $kodeDesa  = session()->get('kode_desa');

            /* ==============================
           1️⃣ PROGRAM BANSOS
        ============================== */
            $program = $this->db->table('dtks_bansos_jenis')
                ->select('dbj_id, dbj_nama_bansos')
                ->orderBy('dbj_nama_bansos', 'ASC')
                ->get()
                ->getResult();


            /* ==============================
           2️⃣ PENGUSUL (CREATED BY)
        ============================== */
            $createdBy = $this->db->table('dtsen_usulan_bansos')
                ->select('dtsen_usulan_bansos.created_by, dtks_users.fullname')
                ->join('dtks_users', 'dtks_users.nik = dtsen_usulan_bansos.created_by', 'left')
                ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_usulan_bansos.id_kk', 'left')
                ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
                ->where('MONTH(dtsen_usulan_bansos.created_at)', $bulan)
                ->where('YEAR(dtsen_usulan_bansos.created_at)', $tahun)
                ->where('dtsen_rt.kode_desa', $kodeDesa)
                ->where('dtsen_usulan_bansos.created_by IS NOT NULL')
                ->groupBy('dtsen_usulan_bansos.created_by')
                ->orderBy('dtks_users.fullname', 'ASC')
                ->get()
                ->getResult();


            /* ==============================
           3️⃣ RW (UNIQUE, by desa)
        ============================== */
            $rw = $this->db->table('dtsen_rt')
                ->select('rw')
                ->where('kode_desa', $kodeDesa)
                ->where('rw IS NOT NULL')
                ->groupBy('rw')
                ->orderBy('rw', 'ASC')
                ->get()
                ->getResult();


            /* ==============================
           4️⃣ RT (UNIQUE, by desa)
        ============================== */
            $rt = $this->db->table('dtsen_rt')
                ->select('rt')
                ->where('kode_desa', $kodeDesa)
                ->where('rt IS NOT NULL')
                ->groupBy('rt')
                ->orderBy('rt', 'ASC')
                ->get()
                ->getResult();


            return $this->response->setJSON([
                'program'    => $program,
                'created_by' => $createdBy,
                'rw'         => $rw,
                'rt'         => $rt
            ]);
        } catch (\Throwable $e) {

            log_message('error', '[FilterOptions] ' . $e->getMessage());

            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getRTByRW($rw)
    {
        try {

            $kodeDesa = session()->get('kode_desa');

            $rtList = $this->GenModel->getRTByDesaRW($kodeDesa, $rw);

            return $this->response->setJSON([
                'rt' => $rtList
            ]);
        } catch (\Throwable $e) {

            log_message('error', '[getRTByRW] ' . $e->getMessage());

            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 📋 API: Ambil daftar unik alasan non-aktif dari pbi_master_data untuk dropdown sanggah
     */
    public function getListAlasanPbi()
    {
        try {
            $kodeDesa = session()->get('kode_desa');
            $db = \Config\Database::connect();

            $builder = $db->table('pbi_master_data')
                ->select('alasan_nonaktif')
                ->where('alasan_nonaktif IS NOT NULL')
                ->where('alasan_nonaktif !=', '')
                ->groupBy('alasan_nonaktif')
                ->orderBy('alasan_nonaktif', 'ASC');

            if (!empty($kodeDesa)) {
                $builder->where('kode_desa', $kodeDesa);
            }

            $list = $builder->get()->getResultArray();

            if (empty($list)) {
                $list = [
                    ['alasan_nonaktif' => 'MENINGGAL DUNIA'],
                    ['alasan_nonaktif' => 'PINDAH DOMISILI'],
                    ['alasan_nonaktif' => 'MAMPU / TIDAK LAYAK'],
                    ['alasan_nonaktif' => 'GANDA / SUDAH MENERIMA BANTUAN LAIN'],
                    ['alasan_nonaktif' => 'TIDAK DITEMUKAN / TIDAK DIKENAL']
                ];
            }

            return $this->response->setJSON(['status' => true, 'data' => $list]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 🚀 API: Eksekusi Sanggah Status PBI Aktif (Ubah jadi Non-Aktif & Simpan Bukti)
     */
    public function prosesSanggahPbi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $nik    = preg_replace('/\D/', '', trim((string) $this->request->getPost('nik')));
        $alasan = trim(strip_tags((string) $this->request->getPost('alasan')));
        $file   = $this->request->getFile('bukti_foto');
        $userId = session()->get('id_user') ?? session()->get('id') ?? 1;
        $kodeDesa = session()->get('kode_desa');

        if (strlen($nik) !== 16 || empty($alasan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'NIK atau Alasan Sanggahan tidak valid!']);
        }

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File bukti screenshot wajib diunggah dengan benar!']);
        }

        // Validasi ekstensi file gambar
        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format file bukti harus berupa gambar (JPG, PNG, WEBP)!']);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Cek apakah data PBI Aktifnya ada
            $pbiRowBuilder = $db->table('pbi_master_data')
                ->where('nik', $nik)
                ->where('status_kepesertaan', 'Aktif');

            if (!empty($kodeDesa)) {
                $pbiRowBuilder->where('kode_desa', $kodeDesa);
            }
            $pbiRow = $pbiRowBuilder->get()->getRowArray();

            if (!$pbiRow) {
                return $this->response->setJSON(['status' => false, 'message' => 'Data PBI Aktif untuk NIK tersebut tidak ditemukan di wilayah Anda.']);
            }

            // Simpan file bukti screenshot ke folder /writable/uploads/sanggah_pbi/ atau /public/data/sanggah_pbi/
            $uploadDir = FCPATH . 'data/sanggah_pbi/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newName = 'sanggah_' . $nik . '_' . time() . '.' . $ext;
            $file->move($uploadDir, $newName);
            $pathFileDb = 'data/sanggah_pbi/' . $newName;

            // Update status pbi_master_data menjadi Non-Aktif
            $db->table('pbi_master_data')
                ->where('id', $pbiRow['id'])
                ->update([
                    'status_kepesertaan' => 'Non-Aktif',
                    'alasan_nonaktif'    => 'SANGGAH PENTRI: ' . $alasan,
                    'tanggal_nonaktif'   => date('Y-m-d'),
                    'dinonaktifkan_oleh' => $userId,
                    'updated_at'         => date('Y-m-d H:i:s')
                ]);

            // Catat log sanggahan (opsional, jika ingin tabel riwayat sanggah terpisah, atau cukup di log_message)
            log_message('info', "[SANGGAH PBI] NIK {$nik} disanggah dan dinonaktifkan oleh User ID {$userId}. Alasan: {$alasan}. Bukti: {$pathFileDb}");

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Sanggahan berhasil diproses! Status PBI warga telah diubah menjadi Non-Aktif. Silakan pilih kembali program bansos untuk diusulkan.'
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal memproses sanggahan: ' . $e->getMessage()
            ]);
        }
    }
}
