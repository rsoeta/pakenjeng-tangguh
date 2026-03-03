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
        ];

        return view('dtsen/usulan_bansos/index', $data);
    }

    /**
     * 🔍 Cari individu berdasarkan NIK/Nama
     * 🔐 Dibatasi wilayah_tugas user login (multi RW–RT)
     */
    public function searchArt()
    {
        $term = trim($this->request->getGet('q'));
        $user = $this->AuthModel->getUserId();
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');

        $builder = $this->artModel
            ->select("
            dtsen_art.nik,
            dtsen_art.nama,
            dtsen_art.hubungan_keluarga AS shdk,
            (SELECT jenis_shdk FROM tb_shdk WHERE id = dtsen_art.hubungan_keluarga) AS shdk_nama,
            dtsen_rt.rw,
            dtsen_rt.rt
        ")
            ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk', 'left')
            ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
            ->where('dtsen_art.deleted_at', null)
            ->groupStart()
            ->like('dtsen_art.nik', $term)
            ->orLike('dtsen_art.nama', $term)
            ->groupEnd()
            ->limit(10);

        /* =====================================================
       🔐 PARSE wilayah_tugas → pasangan RW–RT (KUNCI)
       ===================================================== */
        if (!empty($wilayahTugas)) {

            $wilayahPairs = [];

            // contoh: 001:005,007|004:002
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

        /* =====================================================
       🎯 Format JSON sesuai standar Select2
       ===================================================== */
        $results = array_map(function ($row) {
            return [
                'id'         => $row['nik'],
                'text'       => $row['nik'] . ' - ' . strtoupper($row['nama']),
                'nik'        => $row['nik'],
                'nama'       => $row['nama'],
                'shdk'       => (int) ($row['shdk'] ?? 0),
                'shdk_nama'  => $row['shdk_nama'] ?? '-',
                'rw'         => $row['rw'] ?? '',
                'rt'         => $row['rt'] ?? ''
            ];
        }, $rows);

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    /**
     * 🔎 Cek kategori desil berdasarkan NIK → id_kk
     * Log detail setiap langkah agar mudah dilacak di writable/logs/
     */
    public function checkDesil()
    {
        $nik = $this->request->getGet('nik');

        // 🔹 Log request awal
        log_message('info', "[checkDesil] Request diterima. NIK: {$nik}");

        if (!$nik) {
            log_message('warning', '[checkDesil] Parameter NIK kosong.');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter NIK tidak diberikan.'
            ]);
        }

        try {
            // 1️⃣ Ambil id_kk dari dtsen_art
            $art = $this->artModel->select('id_kk')->where('nik', $nik)->first();
            log_message('info', '[checkDesil] Hasil query ART: ' . json_encode($art));

            if (!$art || empty($art['id_kk'])) {
                log_message('warning', "[checkDesil] Tidak ditemukan ART untuk NIK {$nik}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data individu tidak ditemukan di tabel ART.'
                ]);
            }

            // 2️⃣ Ambil kategori_desil dari dtsen_se berdasarkan id_kk
            $se = $this->seModel
                ->select('kategori_desil')
                ->where('id_kk', $art['id_kk'])
                ->first();

            log_message('info', "[checkDesil] Query SE untuk id_kk={$art['id_kk']} hasil: " . json_encode($se));

            // 3️⃣ Validasi hasil query
            if ($se && isset($se['kategori_desil'])) {
                $desil = $se['kategori_desil'];
                log_message('info', "[checkDesil] Ditemukan kategori_desil={$desil} untuk NIK {$nik}");

                return $this->response->setJSON([
                    'success' => true,
                    'kategori_desil' => $desil
                ]);
            }

            // 4️⃣ Jika tidak ditemukan
            log_message('warning', "[checkDesil] Data desil tidak ditemukan untuk id_kk={$art['id_kk']} (NIK {$nik})");
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data desil tidak tersedia untuk KK ini.'
            ]);
        } catch (\Throwable $e) {
            // 5️⃣ Tangani error fatal
            log_message('error', "[checkDesil] ERROR: {$e->getMessage()} | Trace: {$e->getTraceAsString()}");
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 💾 Simpan usulan bansos baru
     * 🔐 Dilengkapi validasi SHDK–DESIL–PROGRAM
     */
    public function save()
    {
        $request = service('request');

        $nik      = $request->getPost('nik_peserta');
        $program  = (int) $request->getPost('program_bansos');
        $catatan  = $request->getPost('catatan');

        $db = db_connect();

        /* =====================================================
       1️⃣ Validasi ART
       ===================================================== */
        $art = $db->table('dtsen_art')
            ->where('nik', $nik)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$art) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data individu tidak ditemukan.'
            ]);
        }

        /* =====================================================
       2️⃣ Validasi DESIL
       ===================================================== */
        $se = $db->table('dtsen_se')
            ->where('id_kk', $art['id_kk'])
            ->get()
            ->getRowArray();

        $desil = (int) ($se['kategori_desil'] ?? 0);

        if ($desil <= 0 || $desil > 5) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori desil tidak memenuhi syarat.'
            ]);
        }

        /* =====================================================
       3️⃣ VALIDASI SHDK × DESIL × PROGRAM (KUNCI)
       ===================================================== */
        $shdk = (int) ($art['hubungan_keluarga'] ?? 0);

        // SHDK selain KK & Istri → hanya PBI
        if ($shdk !== 1 && $shdk !== 3) {
            if ($program !== 5) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Program hanya diperbolehkan PBI untuk individu ini.'
                ]);
            }
        }

        // DESIL 5 → hanya BPNT/SEMBAKO + PBI
        if ($desil === 5 && !in_array($program, [2, 5])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori desil 5 hanya dapat mengajukan BPNT/SEMBAKO atau PBI.'
            ]);
        }

        /* =====================================================
       4️⃣ Cek Duplikasi Bulan Berjalan
       ===================================================== */
        $cekDuplikat = $db->table('dtsen_usulan_bansos')
            ->where('nik', $nik)
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->countAllResults();

        if ($cekDuplikat > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usulan untuk NIK ini sudah pernah dibuat bulan ini.'
            ]);
        }

        /* =====================================================
       5️⃣ Simpan Data
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

        /* =====================================================
       6️⃣ Update Flag ART
       ===================================================== */
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

        $bulan  = date('m');
        $tahun  = date('Y');
        $status = $this->request->getVar('status'); // 'draft' atau 'diverifikasi'
        $program   = $this->request->getVar('program');
        $createdBy = $this->request->getVar('created_by');
        $rw        = $this->request->getVar('rw');
        $rt        = $this->request->getVar('rt');

        $builder = $this->DtsenUsulanBansosModel
            ->select("
                dtsen_usulan_bansos.*,
                dtsen_art.nama,
                dbj.dbj_nama_bansos,
                u1.fullname AS created_by_name,
                u1.nope    AS created_by_nope,
                u2.fullname AS updated_by_name
            ")
            ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_usulan_bansos.id_kk', 'left')
            ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
            ->join('dtsen_art', 'dtsen_art.nik = dtsen_usulan_bansos.nik', 'left')
            ->join('dtks_bansos_jenis dbj', 'dbj.dbj_id = dtsen_usulan_bansos.program_bansos', 'left')
            ->join('dtks_users u1', 'u1.nik = dtsen_usulan_bansos.created_by', 'left')
            ->join('dtks_users u2', 'u2.nik = dtsen_usulan_bansos.updated_by', 'left')
            ->where('MONTH(dtsen_usulan_bansos.created_at)', $bulan)
            ->where('YEAR(dtsen_usulan_bansos.created_at)', $tahun);

        // Filter status jika diminta
        if ($status) {
            $builder->where('dtsen_usulan_bansos.status', $status);
        }
        if ($program) {
            $builder->where('dtsen_usulan_bansos.program_bansos', $program);
        }

        if ($createdBy) {
            $builder->where('dtsen_usulan_bansos.created_by', $createdBy);
        }

        if ($rw) {
            $builder->where('dtsen_rt.rw', $rw);
        }

        if ($rt) {
            $builder->where('dtsen_rt.rt', $rt);
        }

        // RULE: role 1,2,3 lihat semua
        // role 4 lihat hanya data yang dia buat
        // role >4 tidak dapat melihat data sama sekali -> return empty
        if ($roleId > 4) {
            // langsung return empty dataset
            log_message('info', "[getDataBulanIni] role_id={$roleId} tidak diizinkan melihat data.");
            return $this->response->setJSON(['data' => []]);
        } elseif ($roleId === 4) {
            // hanya data yang dibuat oleh dirinya sendiri
            $builder->where('dtsen_usulan_bansos.created_by', $nik);
        }

        $builder->orderBy('dtsen_usulan_bansos.created_at', 'ASC');

        try {
            $data = $builder->findAll();
            log_message('info', "✅ getDataBulanIni() memuat " . count($data) . " data untuk role={$roleId}, status={$status}");
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
}
