<?php

namespace App\Controllers\Dtsen;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\IncomingRequest;


use App\Models\Dtsen\DtsenKkModel;
use App\Models\Dtsen\DtsenRtModel;
use App\Models\GenModel;
use App\Traits\WilayahFilterTrait;

class DtsenSe extends Controller
{
    use WilayahFilterTrait; // 🔐 Mesin karantina wilayah (role_id >= 4) + mode penyamaran admin

    /**
     * @var IncomingRequest
     */
    protected $request;

    protected $kkModel;
    protected $rtModel;
    protected $genModel;
    protected $db;

    public function __construct()
    {
        $this->kkModel = new DtsenKkModel();
        $this->rtModel = new DtsenRtModel();
        $this->genModel = new GenModel();
        $this->db = \Config\Database::connect();
    }

    // ========================================================
    // 🛠️ FUNGSI BANTUAN (HELPER) UNTUK VIEW
    // ========================================================

    /**
     * Menghitung bobot tahap salur untuk menentukan tahap terakhir secara akurat
     */
    private function getTahapScore(?string $tahapStr): int
    {
        if (empty($tahapStr)) {
            return 0;
        }

        if (preg_match('/Tahap\s*(\d+)\s*Tahun\s*(\d+)/i', $tahapStr, $m)) {
            return ((int) $m[2] * 10) + (int) $m[1];
        }

        if (preg_match('/(\d{4})/', $tahapStr, $m)) {
            return (int) $m[1] * 10;
        }

        return 0;
    }

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
            'petugas_list'   => $this->getPetugasEntriList($kodeDesa),
        ];
    }

    /**
     * 👥 Daftar Petugas Entri aktif (role_id >= 4) di desa yang sedang login
     */
    private function getPetugasEntriList($kodeDesa): array
    {
        if (empty($kodeDesa)) {
            return [];
        }

        return $this->db->table('dtks_users')
            ->select('id, fullname, wilayah_tugas, level, role_id')
            ->where('kode_desa', $kodeDesa)
            ->where('role_id >=', 4)
            ->where('status', 1)
            ->orderBy('fullname', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * 🔐 Tentukan lingkup wilayah efektif (konsep WilayahFilterTrait)
     * - role_id >= 4  : SELALU dikarantina ke wilayah tugas akun sendiri (parameter petugas diabaikan)
     * - role_id <= 3  : bebas, KECUALI memilih Petugas Entri tertentu → "meminjam" wilayah tugas
     *                   petugas tsb dan role dipaksa >= 4 agar data terpotong layaknya petugas itu.
     *
     * @return array{wilayah_tugas:string, level:string, role_id:int}
     */
    private function resolveWilayahScope($petugasId = null): array
    {
        $session  = session();
        $roleId   = (int) $session->get('role_id');
        $kodeDesa = $session->get('kode_desa');

        $scope = [
            'wilayah_tugas' => trim((string) $session->get('wilayah_tugas')),
            'level'         => trim((string) $session->get('level')),
            'role_id'       => $roleId,
        ];

        if ($roleId <= 3 && !empty($petugasId)) {
            $petugas = $this->db->table('dtks_users')
                ->select('wilayah_tugas, level, role_id')
                ->where('id', $petugasId)
                ->where('kode_desa', $kodeDesa)
                ->where('role_id >=', 4)
                ->get()
                ->getRowArray();

            if ($petugas) {
                $scope = [
                    'wilayah_tugas' => trim((string) ($petugas['wilayah_tugas'] ?? '')),
                    'level'         => trim((string) ($petugas['level'] ?? '')),
                    'role_id'       => max(4, (int) $petugas['role_id']),
                ];
            }
        }

        return $scope;
    }

    /**
     * Normalisasi nomor wilayah ke format 3 digit (001) jika numerik
     */
    private function padWilayah($val): string
    {
        $val = trim((string) $val);
        return is_numeric($val) ? str_pad((string) (int) $val, 3, '0', STR_PAD_LEFT) : $val;
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

    // 🚨 Halaman 4: Pemulihan Data Wilayah
    // 1. Buka Gembok Akses Halaman Pemulihan
    public function pemulihan()
    {
        $data = $this->getBaseData('Pemulihan Data Wilayah', 'pemulihan');

        // Opsi B: Petugas Entri (role 4 & 5) sekarang diizinkan masuk
        if (empty($data['menu_pemulihan'])) {
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
            $roleId       = (int) $session->get('role_id'); // Role asli login (untuk hak aksi, bukan lingkup data)

            // 2️⃣ FILTER DARI FRONTEND (Gunakan getVar untuk menangkap data POST dari DataTables)
            $filterClient = $this->request->getVar('filter') ?? [];

            // Jika filter dikirim sebagai JSON string
            if (is_string($filterClient)) {
                $filterClient = json_decode($filterClient, true) ?? [];
            }

            // 3️⃣ LINGKUP WILAYAH (WilayahFilterTrait + mode penyamaran Petugas Entri)
            $scope = $this->resolveWilayahScope($filterClient['petugas'] ?? null);

            // 4️⃣ FILTER AKSES (ROLE)
            // RW/RT dropdown tetap murni filter manual UI; karantina wilayah petugas
            // ditangani oleh wilayah_tugas / level + role_id di model (tidak lagi menimpa filter RW).
            $filter = [
                'kode_desa'     => $kodeDesa,
                'wilayah_tugas' => $scope['wilayah_tugas'],
                'level'         => $scope['level'],
                'role_id'       => $scope['role_id'],
                'rw'            => $filterClient['rw']     ?? null,
                'rt'            => $filterClient['rt']     ?? null,
                'status'        => $filterClient['status']    ?? null,
                'desil'         => $filterClient['desil']     ?? null,
                'perlinsos'     => $filterClient['perlinsos'] ?? null,
                'bansos'        => $filterClient['bansos']    ?? null,
            ];

            // 5️⃣ AMBIL DATA
            $dataKeluarga = $this->kkModel->getFilteredData($filter);

            // 6️⃣ AMBIL MAPPING BANSOS AKTIF TAHAP TERAKHIR DARI DOKUMENTASI BANSOS (dtsen_bansos_kks)
            $bansosMap = [];
            $idKkList  = array_column($dataKeluarga, 'id_kk');

            if (!empty($idKkList)) {
                $chunks = array_chunk($idKkList, 1000);
                foreach ($chunks as $chunk) {
                    $bansosQuery = $this->db->table('dtsen_bansos_kks b')
                        ->select('a.id_kk, b.jenis_bansos, b.tahap_salur, b.status_salur')
                        ->join('dtsen_art a', 'a.nik = b.nik_kpm AND a.deleted_at IS NULL', 'inner')
                        ->whereIn('a.id_kk', $chunk)
                        ->groupStart()
                            ->where('b.status_salur IS NULL')
                            ->orWhere('b.status_salur', '')
                            ->orGroupStart()
                                ->notLike('b.status_salur', 'Tidak Transaksi', 'after')
                                ->notLike('b.status_salur', 'Gagal', 'after')
                            ->groupEnd()
                        ->groupEnd()
                        ->get()->getResultArray();

                    foreach ($bansosQuery as $bq) {
                        $kk = $bq['id_kk'];
                        $score = $this->getTahapScore($bq['tahap_salur']);
                        if (!isset($bansosMap[$kk])) {
                            $bansosMap[$kk] = [
                                'score' => $score,
                                'tahap' => $bq['tahap_salur'],
                                'jenis' => [$bq['jenis_bansos']]
                            ];
                        } else {
                            if ($score > $bansosMap[$kk]['score']) {
                                $bansosMap[$kk] = [
                                    'score' => $score,
                                    'tahap' => $bq['tahap_salur'],
                                    'jenis' => [$bq['jenis_bansos']]
                                ];
                            } elseif ($score === $bansosMap[$kk]['score']) {
                                $bansosMap[$kk]['jenis'][] = $bq['jenis_bansos'];
                            }
                        }
                    }
                }
            }

            // 6.5️⃣ AMBIL DATA STATUS PERLINSOS (dtsen_perlinsos)
            $perlinsosMap = [];
            if (!empty($idKkList)) {
                $chunks = array_chunk($idKkList, 1000);
                foreach ($chunks as $chunk) {
                    $perlinsosRows = $this->db->table('dtsen_perlinsos')
                        ->select('id_kk, status')
                        ->whereIn('id_kk', $chunk)
                        ->get()->getResultArray();

                    foreach ($perlinsosRows as $pr) {
                        $perlinsosMap[$pr['id_kk']] = $pr['status'] ?? 'draft';
                    }
                }
            }

            // 7️⃣ TAMBAHKAN FLAG AKSES, DATA BANSOS & PERLINSOS
            $canInputDesil = ($roleId <= 3);

            foreach ($dataKeluarga as &$row) {
                $row['id_kk_encoded']   = encode_id($row['id_kk']);
                $row['can_input_desil'] = $canInputDesil;

                // Status Perlinsos
                $row['perlinsos_status'] = $perlinsosMap[$row['id_kk']] ?? null;

                // Status Bansos Aktif Tahap Terakhir
                if (isset($bansosMap[$row['id_kk']])) {
                    $jenisList = array_unique($bansosMap[$row['id_kk']]['jenis']);
                    if (in_array('PKH', $jenisList) && in_array('SEMBAKO', $jenisList)) {
                        $row['bansos'] = 'PKH + SEMBAKO';
                    } elseif (in_array('PKH + SEMBAKO', $jenisList)) {
                        $row['bansos'] = 'PKH + SEMBAKO';
                    } else {
                        $row['bansos'] = implode(', ', $jenisList);
                    }
                    $row['bansos_tahap'] = $bansosMap[$row['id_kk']]['tahap'];
                } else {
                    $row['bansos'] = '-';
                    $row['bansos_tahap'] = null;
                }
            }
            unset($row);

            // 8️⃣ FILTER PERLINSOS & BANSOS (JIKA DIPILIH)
            $fPerlinsos = $filter['perlinsos'] ?? null;
            $fBansos    = $filter['bansos'] ?? null;

            if ((!empty($fPerlinsos) && $fPerlinsos !== 'all') || (!empty($fBansos) && $fBansos !== 'all')) {
                $dataKeluarga = array_values(array_filter($dataKeluarga, function ($row) use ($fPerlinsos, $fBansos) {
                    // Filter Perlinsos
                    if (!empty($fPerlinsos) && $fPerlinsos !== 'all') {
                        $pStatus = $row['perlinsos_status'] ?? null;
                        if ($fPerlinsos === 'terverifikasi') {
                            if ($pStatus !== 'tersinkron') return false;
                        } elseif ($fPerlinsos === 'belum_verifikasi') {
                            if (empty($pStatus) || $pStatus === 'tersinkron') return false;
                        } elseif ($fPerlinsos === 'none') {
                            if (!empty($pStatus)) return false;
                        }
                    }

                    // Filter Bansos (Identik / Eksklusif)
                    if (!empty($fBansos) && $fBansos !== 'all') {
                        $b = strtoupper(trim((string)($row['bansos'] ?? '-')));
                        $targetBansos = strtoupper(trim((string)$fBansos));

                        if ($targetBansos === 'NONE') {
                            if ($b !== '-' && $b !== '') return false;
                        } else {
                            if ($b !== $targetBansos) return false;
                        }
                    }

                    return true;
                }));
            }

            // Kembalikan parameter draw (jika dikirim oleh DataTables)
            $draw = $this->request->getVar('draw');

            $responsePayload = [
                'data' => $dataKeluarga
            ];

            if ($draw) {
                $responsePayload['draw'] = intval($draw);
            }

            return $this->response->setJSON($responsePayload);
        } catch (\Throwable $e) {
            log_message('error', '❌ tabel_data() error: ' . $e->getMessage());

            return $this->response->setJSON([
                'draw'    => intval($this->request->getVar('draw')),
                'data'    => [],
                'error'   => true,
                'message' => $e->getMessage()
            ]);
        }
    }

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

            // 🚀 TANGKAP TAHUN DAN TRIWULAN DARI FORM (Menggunakan Float untuk Triwulan)
            $tahun_berlaku = (int) $this->request->getPost('tahun_berlaku');
            $triwulan_berlaku = floatval($this->request->getPost('triwulan_berlaku'));

            // 🔎 Validasi input
            if (!$idKk || $kategoriDesil < 0 || $kategoriDesil > 10 || !$tahun_berlaku || !$triwulan_berlaku) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data desil atau periode tidak valid/lengkap.'
                ]);
            }

            $db = db_connect();
            $db->transBegin();

            // =========================
            // 1️⃣ UPDATE dtsen_se (Tabel Master Desil Nasional Terkini)
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
            // 2️⃣ INSERT / UPDATE HISTORI (Menggunakan Periode Form)
            // =========================
            $label = 'TW' . $triwulan_berlaku . ' ' . $tahun_berlaku;

            $exists = $db->table('dtsen_desil_history')
                ->where([
                    'id_kk'    => $idKk,
                    'tahun'    => $tahun_berlaku,
                    'triwulan' => $triwulan_berlaku
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
                    'tahun'         => $tahun_berlaku,
                    'triwulan'      => $triwulan_berlaku,
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

    /**
     * Resolusi nama SHDK dari ID numerik atau teks
     */
    private function resolveShdkLabel($shdkVal, $hubunganVal, array $shdkMap): string
    {
        if (!empty($shdkVal) && is_numeric($shdkVal) && (int) $shdkVal > 0 && isset($shdkMap[(int) $shdkVal])) {
            return $shdkMap[(int) $shdkVal];
        }
        if (!empty($hubunganVal) && is_numeric($hubunganVal) && (int) $hubunganVal > 0 && isset($shdkMap[(int) $hubunganVal])) {
            return $shdkMap[(int) $hubunganVal];
        }
        if (!empty($hubunganVal) && !is_numeric($hubunganVal)) {
            return (string) $hubunganVal;
        }
        return 'Belum Ditentukan';
    }

    /**
     * Kategorisasi alasan penghapusan untuk filter pintar di UI Arsip
     */
    private function categorizeDeleteReason(?string $reason): string
    {
        $r = strtolower(trim((string) $reason));
        if ($r === '') {
            return 'lainnya';
        }
        if (preg_match('/(meninggal|mati|wafat|alm)/i', $r)) {
            return 'meninggal';
        }
        if (preg_match('/(pindah|domisili|keluar|nikah|menikah|pisah|kk sendiri|keluarga sendiri|punya keluarga|punya kk)/i', $r)) {
            return 'pindah';
        }
        if (preg_match('/(duplikat|ganda|dobel|double|doble)/i', $r)) {
            return 'duplikat';
        }
        if (preg_match('/(ditimpa|usulan)/i', $r)) {
            return 'usulan';
        }
        if (preg_match('/(pemeriksaan|verval|anomali|akl kosong|salah|tidak dikenal|tidak ditemukan|bukan|nik <)/i', $r)) {
            return 'pemeriksaan';
        }
        return 'lainnya';
    }

    /**
     * Helper internal: Pulihkan seluruh anggota terarsip milik suatu id_kk
     * dengan mencegah duplikasi NIK aktif pada keluarga yang sama.
     */
    private function restoreMembersOfKk($db, int $idKk, bool $forceAll = false): int
    {
        if ($idKk <= 0) {
            return 0;
        }

        $restoredCount = 0;

        // 1. Cek NIK yang saat ini sudah aktif di dtsen_art pada KK ini
        $activeRows = $db->table('dtsen_art')
            ->select('nik')
            ->where('id_kk', $idKk)
            ->where('deleted_at IS NULL')
            ->get()
            ->getResultArray();

        $activeNiks = [];
        foreach ($activeRows as $ar) {
            $nik = trim((string) ($ar['nik'] ?? ''));
            if ($nik !== '') {
                $activeNiks[$nik] = true;
            }
        }

        // 2. Ambil anggota dtsen_art yang terarsip pada KK ini (urutkan dari yang terbaru dihapus)
        $archivedMaster = $db->table('dtsen_art')
            ->select('id_art, nik, deleted_at')
            ->where('id_kk', $idKk)
            ->where('deleted_at IS NOT NULL')
            ->orderBy('deleted_at', 'DESC')
            ->orderBy('id_art', 'DESC')
            ->get()
            ->getResultArray();

        $masterIdsToRestore = [];
        foreach ($archivedMaster as $am) {
            $nik = trim((string) ($am['nik'] ?? ''));
            if ($forceAll || $nik === '' || !isset($activeNiks[$nik])) {
                $masterIdsToRestore[] = (int) $am['id_art'];
                if ($nik !== '') {
                    $activeNiks[$nik] = true;
                }
            }
        }

        if (!empty($masterIdsToRestore)) {
            $db->table('dtsen_art')
                ->whereIn('id_art', $masterIdsToRestore)
                ->update([
                    'deleted_at'    => null,
                    'delete_reason' => null,
                ]);
            $restoredCount += count($masterIdsToRestore);
        }

        // 3. Cek juga di dtsen_usulan_art untuk usulan milik KK ini
        $usulanRows = $db->table('dtsen_usulan')
            ->select('id')
            ->where('dtsen_kk_id', $idKk)
            ->get()
            ->getResultArray();

        if (!empty($usulanRows)) {
            $usulanIds = array_column($usulanRows, 'id');

            $activeUsulanArts = $db->table('dtsen_usulan_art')
                ->select('dtsen_usulan_id, nik')
                ->whereIn('dtsen_usulan_id', $usulanIds)
                ->where('deleted_at IS NULL')
                ->get()
                ->getResultArray();

            $activeUsulanNikMap = [];
            foreach ($activeUsulanArts as $aua) {
                $uId = (int) $aua['dtsen_usulan_id'];
                $nik = trim((string) ($aua['nik'] ?? ''));
                if ($nik !== '') {
                    $activeUsulanNikMap[$uId][$nik] = true;
                }
            }

            $archivedUsulanArts = $db->table('dtsen_usulan_art')
                ->select('id, dtsen_usulan_id, nik')
                ->whereIn('dtsen_usulan_id', $usulanIds)
                ->where('deleted_at IS NOT NULL')
                ->orderBy('deleted_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();

            $usulanArtIdsToRestore = [];
            foreach ($archivedUsulanArts as $aua) {
                $uId = (int) $aua['dtsen_usulan_id'];
                $nik = trim((string) ($aua['nik'] ?? ''));
                if ($forceAll || $nik === '' || empty($activeUsulanNikMap[$uId][$nik])) {
                    $usulanArtIdsToRestore[] = (int) $aua['id'];
                    if ($nik !== '') {
                        $activeUsulanNikMap[$uId][$nik] = true;
                    }
                }
            }

            if (!empty($usulanArtIdsToRestore)) {
                $db->table('dtsen_usulan_art')
                    ->whereIn('id', $usulanArtIdsToRestore)
                    ->update([
                        'deleted_at'    => null,
                        'delete_reason' => null,
                    ]);
                $restoredCount += count($usulanArtIdsToRestore);
            }
        }

        return $restoredCount;
    }

    public function restoreKeluarga()
    {
        try {
            $id = (int) $this->request->getPost('id_kk');
            $includeMembers = (bool) $this->request->getPost('include_members');

            if (!$id) {
                return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
            }

            $db = \Config\Database::connect();
            $db->transBegin();

            // Restore KK
            $this->kkModel->update($id, [
                'deleted_at'    => null,
                'delete_reason' => null,
            ]);

            $restoredArt = 0;
            if ($includeMembers) {
                $restoredArt = $this->restoreMembersOfKk($db, $id);
            }

            $db->transCommit();

            $msg = 'Data keluarga berhasil dipulihkan.';
            if ($restoredArt > 0) {
                $msg = "Data keluarga beserta {$restoredArt} anggota keluarga berhasil dipulihkan.";
            }

            return $this->response->setJSON([
                'status'       => true,
                'restored_kk'  => 1,
                'restored_art' => $restoredArt,
                'message'      => $msg
            ]);
        } catch (\Throwable $e) {
            if (isset($db)) {
                $db->transRollback();
            }
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Pulihkan 1 Keluarga Utuh (KK + Seluruh Anggota Terarsip) dari tab/baris manapun
     */
    public function restoreSekeluarga()
    {
        try {
            $idKk = (int) $this->request->getPost('id_kk');
            $mode = trim((string) ($this->request->getPost('mode') ?? 'full')); // 'full' | 'kk_only' | 'art_only'

            if ($idKk <= 0) {
                return $this->response->setJSON(['status' => false, 'message' => 'ID Keluarga (KK) tidak valid.']);
            }

            $db = \Config\Database::connect();
            $kkRow = $db->table('dtsen_kk')->where('id_kk', $idKk)->get()->getRowArray();
            if (!$kkRow) {
                return $this->response->setJSON(['status' => false, 'message' => 'Data Kartu Keluarga tidak ditemukan.']);
            }

            $db->transBegin();

            $restoredKk = 0;
            $restoredArt = 0;

            if ($mode !== 'art_only' && !empty($kkRow['deleted_at']) && $kkRow['deleted_at'] !== '0000-00-00 00:00:00') {
                $db->table('dtsen_kk')->where('id_kk', $idKk)->update([
                    'deleted_at'    => null,
                    'delete_reason' => null,
                ]);
                $restoredKk = 1;
            }

            if ($mode !== 'kk_only') {
                $restoredArt = $this->restoreMembersOfKk($db, $idKk);
            }

            $db->transCommit();

            $namaKK = $kkRow['kepala_keluarga'] ?? 'Keluarga';
            $noKK   = $kkRow['no_kk'] ?? '-';

            $parts = [];
            if ($restoredKk > 0) {
                $parts[] = "Kartu Keluarga ({$namaKK})";
            }
            if ($restoredArt > 0) {
                $parts[] = "{$restoredArt} anggota keluarga";
            }

            $summaryText = !empty($parts)
                ? implode(' beserta ', $parts) . ' berhasil dipulihkan.'
                : "Keluarga {$namaKK} (No. KK: {$noKK}) telah aktif dan tidak ada anggota terarsip tambahan yang perlu dipulihkan.";

            return $this->response->setJSON([
                'status'          => true,
                'id_kk'           => $idKk,
                'restored_kk'     => $restoredKk,
                'restored_art'    => $restoredArt,
                'kepala_keluarga' => $namaKK,
                'no_kk'           => $noKK,
                'message'         => $summaryText
            ]);
        } catch (\Throwable $e) {
            if (isset($db)) {
                $db->transRollback();
            }
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal memulihkan keluarga: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk Restore (Pulihkan Massal): Mendukung Keluarga, Penduduk (Master & Usulan), atau campuran keduanya
     */
    public function restoreBulk()
    {
        try {
            $rawItems       = $this->request->getPost('items');
            $includeRelated = (bool) $this->request->getPost('include_related');

            if (is_string($rawItems)) {
                $rawItems = json_decode($rawItems, true);
            }

            if (empty($rawItems) || !is_array($rawItems)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Tidak ada data yang dipilih untuk dipulihkan.'
                ]);
            }

            $db = \Config\Database::connect();
            $db->transBegin();

            $kkIdsToRestore         = [];
            $artUtamaIdsToRestore   = [];
            $artUsulanIdsToRestore  = [];
            $relatedKkForMembers    = [];
            $parentKkToEnsureActive = [];

            foreach ($rawItems as $item) {
                if (is_string($item)) {
                    // Format UID: kk_123 | art_utama_456 | art_usulan_789
                    if (strpos($item, 'kk_') === 0) {
                        $idKk = (int) substr($item, 3);
                        if ($idKk > 0) {
                            $kkIdsToRestore[$idKk] = $idKk;
                            if ($includeRelated) {
                                $relatedKkForMembers[$idKk] = $idKk;
                            }
                        }
                    } elseif (strpos($item, 'art_utama_') === 0) {
                        $idArt = (int) substr($item, 10);
                        if ($idArt > 0) {
                            $artUtamaIdsToRestore[$idArt] = $idArt;
                        }
                    } elseif (strpos($item, 'art_usulan_') === 0) {
                        $idUa = (int) substr($item, 11);
                        if ($idUa > 0) {
                            $artUsulanIdsToRestore[$idUa] = $idUa;
                        }
                    }
                    continue;
                }

                if (!is_array($item)) {
                    continue;
                }

                $type   = $item['record_type'] ?? ($item['type'] ?? '');
                $sumber = $item['sumber'] ?? 'utama';
                $id     = (int) ($item['id'] ?? 0);
                $idKk   = (int) ($item['id_kk'] ?? 0);

                if ($type === 'keluarga' || $sumber === 'kk') {
                    $targetKk = $id > 0 ? $id : $idKk;
                    if ($targetKk > 0) {
                        $kkIdsToRestore[$targetKk] = $targetKk;
                        if ($includeRelated) {
                            $relatedKkForMembers[$targetKk] = $targetKk;
                        }
                    }
                } elseif ($type === 'grup_keluarga') {
                    $targetKk = $idKk > 0 ? $idKk : $id;
                    if ($targetKk > 0) {
                        $kkIdsToRestore[$targetKk] = $targetKk;
                        $relatedKkForMembers[$targetKk] = $targetKk;
                    }
                } else {
                    if ($sumber === 'usulan' && $id > 0) {
                        $artUsulanIdsToRestore[$id] = $id;
                    } elseif ($id > 0) {
                        $artUtamaIdsToRestore[$id] = $id;
                    }
                    if ($includeRelated && $idKk > 0) {
                        $parentKkToEnsureActive[$idKk] = $idKk;
                    }
                }
            }

            $restoredKkCount  = 0;
            $restoredArtCount = 0;

            // 1. Jika ada penduduk yang dipilih dan opsi include_related aktif, cari id_kk dari DB jika belum dikirim
            if ($includeRelated) {
                if (!empty($artUtamaIdsToRestore)) {
                    $rows = $db->table('dtsen_art')
                        ->select('id_kk')
                        ->whereIn('id_art', array_values($artUtamaIdsToRestore))
                        ->get()
                        ->getResultArray();
                    foreach ($rows as $r) {
                        if (!empty($r['id_kk'])) {
                            $parentKkToEnsureActive[(int) $r['id_kk']] = (int) $r['id_kk'];
                        }
                    }
                }
                if (!empty($artUsulanIdsToRestore)) {
                    $rows = $db->table('dtsen_usulan_art ua')
                        ->select('u.dtsen_kk_id AS id_kk')
                        ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'left')
                        ->whereIn('ua.id', array_values($artUsulanIdsToRestore))
                        ->get()
                        ->getResultArray();
                    foreach ($rows as $r) {
                        if (!empty($r['id_kk'])) {
                            $parentKkToEnsureActive[(int) $r['id_kk']] = (int) $r['id_kk'];
                        }
                    }
                }
            }

            $allKkIds = array_unique(array_merge(array_values($kkIdsToRestore), array_values($parentKkToEnsureActive)));
            if (!empty($allKkIds)) {
                $archivedKkRows = $db->table('dtsen_kk')
                    ->select('id_kk')
                    ->whereIn('id_kk', $allKkIds)
                    ->where('deleted_at IS NOT NULL')
                    ->get()
                    ->getResultArray();

                $validKkIds = array_column($archivedKkRows, 'id_kk');
                if (!empty($validKkIds)) {
                    $db->table('dtsen_kk')
                        ->whereIn('id_kk', $validKkIds)
                        ->update([
                            'deleted_at'    => null,
                            'delete_reason' => null,
                        ]);
                    $restoredKkCount += count($validKkIds);
                }
            }

            // 2. Pulihkan anggota spesifik yang dicentang (Master ART)
            if (!empty($artUtamaIdsToRestore)) {
                $db->table('dtsen_art')
                    ->whereIn('id_art', array_values($artUtamaIdsToRestore))
                    ->where('deleted_at IS NOT NULL')
                    ->update([
                        'deleted_at'    => null,
                        'delete_reason' => null,
                    ]);
                $restoredArtCount += $db->affectedRows();
            }

            // 3. Pulihkan anggota spesifik yang dicentang (Usulan ART)
            if (!empty($artUsulanIdsToRestore)) {
                $db->table('dtsen_usulan_art')
                    ->whereIn('id', array_values($artUsulanIdsToRestore))
                    ->where('deleted_at IS NOT NULL')
                    ->update([
                        'deleted_at'    => null,
                        'delete_reason' => null,
                    ]);
                $restoredArtCount += $db->affectedRows();
            }

            // 4. Pulihkan anggota keluarga terkait untuk setiap KK yang dipulihkan (jika include_related aktif)
            if (!empty($relatedKkForMembers)) {
                foreach ($relatedKkForMembers as $kId) {
                    $restoredArtCount += $this->restoreMembersOfKk($db, (int) $kId);
                }
            }

            $db->transCommit();

            $msgParts = [];
            if ($restoredKkCount > 0) {
                $msgParts[] = "{$restoredKkCount} Keluarga (KK)";
            }
            if ($restoredArtCount > 0) {
                $msgParts[] = "{$restoredArtCount} Penduduk (Anggota)";
            }

            $msg = !empty($msgParts)
                ? 'Berhasil memulihkan ' . implode(' dan ', $msgParts) . ' dari arsip.'
                : 'Data terpilih berhasil diproses.';

            return $this->response->setJSON([
                'status'         => true,
                'restored_kk'    => $restoredKkCount,
                'restored_art'   => $restoredArtCount,
                'total_restored' => $restoredKkCount + $restoredArtCount,
                'message'        => $msg
            ]);
        } catch (\Throwable $e) {
            if (isset($db)) {
                $db->transRollback();
            }
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal memproses pemulihan massal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint Terpadu: Menggabungkan Arsip Keluarga & Arsip Penduduk dalam 1 Workspace Interaktif
     */
    public function arsipTerpadu()
    {
        try {
            $db = \Config\Database::connect();

            // 1. Lookup SHDK di memori agar sangat cepat & akurat
            $shdkRows = $db->table('tb_shdk')->select('id, jenis_shdk')->get()->getResultArray();
            $shdkMap  = array_column($shdkRows, 'jenis_shdk', 'id');

            // 2. Ambil seluruh KK yang terarsip
            $arsipKK = $db->table('dtsen_kk k')
                ->select('
                    k.id_kk,
                    k.no_kk,
                    k.kepala_keluarga,
                    k.nik_kepala_keluarga,
                    k.alamat AS alamat_kk,
                    k.jumlah_anggota,
                    k.deleted_at,
                    k.delete_reason,
                    rt.alamat AS alamat_rt,
                    rt.rw,
                    rt.rt
                ')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->where('k.deleted_at IS NOT NULL')
                ->where('k.deleted_at !=', '0000-00-00 00:00:00')
                ->orderBy('k.deleted_at', 'DESC')
                ->get()
                ->getResultArray();

            // 3. Ambil seluruh Anggota Master (dtsen_art) yang terarsip
            $arsipArtUtama = $db->table('dtsen_art a')
                ->select('
                    a.id_art AS id,
                    a.id_kk,
                    a.nik,
                    a.nama,
                    a.jenis_kelamin,
                    a.tanggal_lahir,
                    a.shdk,
                    a.hubungan_keluarga,
                    a.deleted_at,
                    a.delete_reason,
                    k.no_kk,
                    k.kepala_keluarga,
                    k.nik_kepala_keluarga,
                    k.alamat AS alamat_kk,
                    k.deleted_at AS kk_deleted_at,
                    k.delete_reason AS kk_delete_reason,
                    rt.alamat AS alamat_rt,
                    rt.rw,
                    rt.rt
                ')
                ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->where('a.deleted_at IS NOT NULL')
                ->where('a.deleted_at !=', '0000-00-00 00:00:00')
                ->get()
                ->getResultArray();

            // 4. Ambil seluruh Anggota Usulan (dtsen_usulan_art) yang terarsip
            $arsipArtUsulan = $db->table('dtsen_usulan_art ua')
                ->select('
                    ua.id AS id,
                    u.dtsen_kk_id AS id_kk,
                    u.id AS usulan_id,
                    u.status AS usulan_status,
                    ua.nik,
                    ua.nama,
                    ua.hubungan,
                    ua.deleted_at,
                    ua.delete_reason,
                    k.no_kk,
                    k.kepala_keluarga,
                    k.nik_kepala_keluarga,
                    k.alamat AS alamat_kk,
                    k.deleted_at AS kk_deleted_at,
                    k.delete_reason AS kk_delete_reason,
                    rt.alamat AS alamat_rt,
                    rt.rw,
                    rt.rt
                ')
                ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'left')
                ->join('dtsen_kk k', 'k.id_kk = u.dtsen_kk_id', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->where('ua.deleted_at IS NOT NULL')
                ->where('ua.deleted_at !=', '0000-00-00 00:00:00')
                ->get()
                ->getResultArray();

            // 5. Kumpulkan seluruh id_kk yang terdampak untuk menghitung anggota aktif & terarsip per KK
            $affectedKkIds = [];
            foreach ($arsipKK as $k) {
                if (!empty($k['id_kk'])) {
                    $affectedKkIds[(int) $k['id_kk']] = (int) $k['id_kk'];
                }
            }
            foreach ($arsipArtUtama as $a) {
                if (!empty($a['id_kk'])) {
                    $affectedKkIds[(int) $a['id_kk']] = (int) $a['id_kk'];
                }
            }
            foreach ($arsipArtUsulan as $ua) {
                if (!empty($ua['id_kk'])) {
                    $affectedKkIds[(int) $ua['id_kk']] = (int) $ua['id_kk'];
                }
            }

            $activeArtByKk  = [];
            $deletedArtByKk = [];
            $activeNikByKk  = [];
            $searchBlobByKk = [];

            if (!empty($affectedKkIds)) {
                $chunks = array_chunk(array_values($affectedKkIds), 500);
                foreach ($chunks as $chunk) {
                    $allArts = $db->table('dtsen_art')
                        ->select('id_art, id_kk, nik, nama, jenis_kelamin, tanggal_lahir, shdk, hubungan_keluarga, deleted_at, delete_reason')
                        ->whereIn('id_kk', $chunk)
                        ->orderBy('shdk', 'ASC')
                        ->get()
                        ->getResultArray();

                    foreach ($allArts as $member) {
                        $kId = (int) $member['id_kk'];
                        $nik = trim((string) ($member['nik'] ?? ''));
                        $nama = trim((string) ($member['nama'] ?? ''));
                        $shdkLabel = $this->resolveShdkLabel($member['shdk'] ?? null, $member['hubungan_keluarga'] ?? null, $shdkMap);

                        $searchBlobByKk[$kId][] = $nik . ' ' . $nama;

                        $isDel = !empty($member['deleted_at']) && $member['deleted_at'] !== '0000-00-00 00:00:00';
                        if (!$isDel) {
                            $activeArtByKk[$kId][] = [
                                'id'         => (int) $member['id_art'],
                                'nik'        => $nik,
                                'nama'       => $nama,
                                'jenis_shdk' => $shdkLabel,
                                'jk'         => $member['jenis_kelamin'] ?? '-',
                                'sumber'     => 'utama'
                            ];
                            if ($nik !== '') {
                                $activeNikByKk[$kId][$nik] = true;
                            }
                        } else {
                            $deletedArtByKk[$kId][] = [
                                'uid'           => 'art_utama_' . (int) $member['id_art'],
                                'id'            => (int) $member['id_art'],
                                'nik'           => $nik,
                                'nama'          => $nama,
                                'jenis_shdk'    => $shdkLabel,
                                'jk'            => $member['jenis_kelamin'] ?? '-',
                                'deleted_at'    => $member['deleted_at'],
                                'delete_reason' => $member['delete_reason'] ?? '-',
                                'sumber'        => 'utama'
                            ];
                        }
                    }
                }
            }

            // Tambahkan anggota usulan terarsip ke deletedArtByKk
            foreach ($arsipArtUsulan as $ua) {
                $kId = (int) ($ua['id_kk'] ?? 0);
                if ($kId <= 0) {
                    continue;
                }
                $nik = trim((string) ($ua['nik'] ?? ''));
                $nama = trim((string) ($ua['nama'] ?? ''));
                $shdkLabel = $this->resolveShdkLabel(null, $ua['hubungan'] ?? null, $shdkMap);
                $searchBlobByKk[$kId][] = $nik . ' ' . $nama;

                $deletedArtByKk[$kId][] = [
                    'uid'           => 'art_usulan_' . (int) $ua['id'],
                    'id'            => (int) $ua['id'],
                    'nik'           => $nik,
                    'nama'          => $nama,
                    'jenis_shdk'    => $shdkLabel,
                    'jk'            => '-',
                    'deleted_at'    => $ua['deleted_at'],
                    'delete_reason' => $ua['delete_reason'] ?? '-',
                    'sumber'        => 'usulan'
                ];
            }

            // 6. Rakit Unified Items & Family Groups
            $items      = [];
            $rwSet      = [];
            $reasonStat = [
                'meninggal'   => 0,
                'pindah'      => 0,
                'duplikat'    => 0,
                'usulan'      => 0,
                'pemeriksaan' => 0,
                'lainnya'     => 0,
            ];

            // A) Item Arsip Keluarga (KK)
            foreach ($arsipKK as $k) {
                $kId    = (int) $k['id_kk'];
                $rwNorm = !empty($k['rw']) ? (is_numeric($k['rw']) ? str_pad((string) $k['rw'], 3, '0', STR_PAD_LEFT) : trim((string) $k['rw'])) : '-';
                $rtNorm = !empty($k['rt']) ? (is_numeric($k['rt']) ? str_pad((string) $k['rt'], 3, '0', STR_PAD_LEFT) : trim((string) $k['rt'])) : '-';
                if ($rwNorm !== '-') {
                    $rwSet[$rwNorm] = $rwNorm;
                }

                $alamat = !empty($k['alamat_kk']) ? $k['alamat_kk'] : ($k['alamat_rt'] ?? '-');
                $cat    = $this->categorizeDeleteReason($k['delete_reason'] ?? '');
                $reasonStat[$cat] = ($reasonStat[$cat] ?? 0) + 1;

                $activeList  = $activeArtByKk[$kId] ?? [];
                $deletedList = $deletedArtByKk[$kId] ?? [];
                $blob        = isset($searchBlobByKk[$kId]) ? implode(' | ', array_unique($searchBlobByKk[$kId])) : '';

                $items[] = [
                    'uid'               => 'kk_' . $kId,
                    'record_type'       => 'keluarga',
                    'sumber'            => 'kk',
                    'id'                => $kId,
                    'id_kk'             => $kId,
                    'id_kk_encoded'     => function_exists('encode_id') ? encode_id($kId) : $kId,
                    'nama'              => $k['kepala_keluarga'] ?? '-',
                    'nik'               => $k['nik_kepala_keluarga'] ?? '',
                    'no_kk'             => $k['no_kk'] ?? '-',
                    'kepala_keluarga'   => $k['kepala_keluarga'] ?? '-',
                    'jenis_shdk'        => 'KEPALA KELUARGA',
                    'jenis_kelamin'     => null,
                    'tanggal_lahir'     => null,
                    'alamat'            => $alamat ?: '-',
                    'rw'                => $rwNorm,
                    'rt'                => $rtNorm,
                    'deleted_at'        => $k['deleted_at'],
                    'delete_reason'     => $k['delete_reason'] ?? '-',
                    'reason_category'   => $cat,
                    'kk_is_deleted'     => true,
                    'kk_deleted_at'     => $k['deleted_at'],
                    'kk_delete_reason'  => $k['delete_reason'] ?? '-',
                    'active_art_count'  => count($activeList),
                    'deleted_art_count' => count($deletedList),
                    'active_same_nik'   => false,
                    'search_blob'       => $blob,
                ];
            }

            // B) Item Arsip Penduduk Master (dtsen_art)
            foreach ($arsipArtUtama as $a) {
                $idArt  = (int) $a['id'];
                $kId    = (int) ($a['id_kk'] ?? 0);
                $nik    = trim((string) ($a['nik'] ?? ''));
                $rwNorm = !empty($a['rw']) ? (is_numeric($a['rw']) ? str_pad((string) $a['rw'], 3, '0', STR_PAD_LEFT) : trim((string) $a['rw'])) : '-';
                $rtNorm = !empty($a['rt']) ? (is_numeric($a['rt']) ? str_pad((string) $a['rt'], 3, '0', STR_PAD_LEFT) : trim((string) $a['rt'])) : '-';
                if ($rwNorm !== '-') {
                    $rwSet[$rwNorm] = $rwNorm;
                }

                $alamat       = !empty($a['alamat_kk']) ? $a['alamat_kk'] : ($a['alamat_rt'] ?? '-');
                $shdkLabel    = $this->resolveShdkLabel($a['shdk'] ?? null, $a['hubungan_keluarga'] ?? null, $shdkMap);
                $cat          = $this->categorizeDeleteReason($a['delete_reason'] ?? '');
                $reasonStat[$cat] = ($reasonStat[$cat] ?? 0) + 1;

                $kkIsDeleted  = !empty($a['kk_deleted_at']) && $a['kk_deleted_at'] !== '0000-00-00 00:00:00';
                $activeList   = $activeArtByKk[$kId] ?? [];
                $deletedList  = $deletedArtByKk[$kId] ?? [];
                $sameNikActive = ($nik !== '' && !empty($activeNikByKk[$kId][$nik]));

                $items[] = [
                    'uid'               => 'art_utama_' . $idArt,
                    'record_type'       => 'penduduk',
                    'sumber'            => 'utama',
                    'id'                => $idArt,
                    'id_kk'             => $kId,
                    'id_kk_encoded'     => ($kId > 0 && function_exists('encode_id')) ? encode_id($kId) : $kId,
                    'nama'              => $a['nama'] ?? '-',
                    'nik'               => $nik ?: '-',
                    'no_kk'             => $a['no_kk'] ?? '-',
                    'kepala_keluarga'   => $a['kepala_keluarga'] ?? '-',
                    'jenis_shdk'        => $shdkLabel,
                    'jenis_kelamin'     => $a['jenis_kelamin'] ?? null,
                    'tanggal_lahir'     => ($a['tanggal_lahir'] && $a['tanggal_lahir'] !== '0000-00-00') ? $a['tanggal_lahir'] : null,
                    'alamat'            => $alamat ?: '-',
                    'rw'                => $rwNorm,
                    'rt'                => $rtNorm,
                    'deleted_at'        => $a['deleted_at'],
                    'delete_reason'     => $a['delete_reason'] ?? '-',
                    'reason_category'   => $cat,
                    'kk_is_deleted'     => $kkIsDeleted,
                    'kk_deleted_at'     => $a['kk_deleted_at'] ?? null,
                    'kk_delete_reason'  => $a['kk_delete_reason'] ?? null,
                    'active_art_count'  => count($activeList),
                    'deleted_art_count' => count($deletedList),
                    'active_same_nik'   => $sameNikActive,
                    'search_blob'       => ($a['no_kk'] ?? '') . ' ' . ($a['kepala_keluarga'] ?? ''),
                ];
            }

            // C) Item Arsip Penduduk Usulan (dtsen_usulan_art)
            foreach ($arsipArtUsulan as $ua) {
                $idUa   = (int) $ua['id'];
                $kId    = (int) ($ua['id_kk'] ?? 0);
                $nik    = trim((string) ($ua['nik'] ?? ''));
                $rwNorm = !empty($ua['rw']) ? (is_numeric($ua['rw']) ? str_pad((string) $ua['rw'], 3, '0', STR_PAD_LEFT) : trim((string) $ua['rw'])) : '-';
                $rtNorm = !empty($ua['rt']) ? (is_numeric($ua['rt']) ? str_pad((string) $ua['rt'], 3, '0', STR_PAD_LEFT) : trim((string) $ua['rt'])) : '-';
                if ($rwNorm !== '-') {
                    $rwSet[$rwNorm] = $rwNorm;
                }

                $alamat        = !empty($ua['alamat_kk']) ? $ua['alamat_kk'] : ($ua['alamat_rt'] ?? '-');
                $shdkLabel     = $this->resolveShdkLabel(null, $ua['hubungan'] ?? null, $shdkMap);
                $cat           = $this->categorizeDeleteReason($ua['delete_reason'] ?? '');
                $reasonStat[$cat] = ($reasonStat[$cat] ?? 0) + 1;

                $kkIsDeleted   = !empty($ua['kk_deleted_at']) && $ua['kk_deleted_at'] !== '0000-00-00 00:00:00';
                $activeList    = $activeArtByKk[$kId] ?? [];
                $deletedList   = $deletedArtByKk[$kId] ?? [];
                $sameNikActive = ($nik !== '' && !empty($activeNikByKk[$kId][$nik]));

                $items[] = [
                    'uid'               => 'art_usulan_' . $idUa,
                    'record_type'       => 'penduduk',
                    'sumber'            => 'usulan',
                    'id'                => $idUa,
                    'id_kk'             => $kId,
                    'id_kk_encoded'     => ($kId > 0 && function_exists('encode_id')) ? encode_id($kId) : $kId,
                    'nama'              => $ua['nama'] ?? '-',
                    'nik'               => $nik ?: '-',
                    'no_kk'             => $ua['no_kk'] ?? '-',
                    'kepala_keluarga'   => $ua['kepala_keluarga'] ?? '-',
                    'jenis_shdk'        => $shdkLabel,
                    'jenis_kelamin'     => null,
                    'tanggal_lahir'     => null,
                    'alamat'            => $alamat ?: '-',
                    'rw'                => $rwNorm,
                    'rt'                => $rtNorm,
                    'deleted_at'        => $ua['deleted_at'],
                    'delete_reason'     => $ua['delete_reason'] ?? '-',
                    'reason_category'   => $cat,
                    'kk_is_deleted'     => $kkIsDeleted,
                    'kk_deleted_at'     => $ua['kk_deleted_at'] ?? null,
                    'kk_delete_reason'  => $ua['kk_delete_reason'] ?? null,
                    'active_art_count'  => count($activeList),
                    'deleted_art_count' => count($deletedList),
                    'active_same_nik'   => $sameNikActive,
                    'search_blob'       => ($ua['no_kk'] ?? '') . ' ' . ($ua['kepala_keluarga'] ?? ''),
                ];
            }

            // Urutkan seluruh item berdasarkan waktu hapus terbaru
            usort($items, function ($a, $b) {
                return strtotime($b['deleted_at'] ?? '1970-01-01') <=> strtotime($a['deleted_at'] ?? '1970-01-01');
            });

            ksort($rwSet);

            return $this->response->setJSON([
                'status'  => true,
                'data'    => $items,
                'summary' => [
                    'total_all'        => count($items),
                    'total_kk'         => count($arsipKK),
                    'total_art'        => count($arsipArtUtama) + count($arsipArtUsulan),
                    'total_art_utama'  => count($arsipArtUtama),
                    'total_art_usulan' => count($arsipArtUsulan),
                    'total_kk_groups'  => count($affectedKkIds),
                    'by_reason'        => $reasonStat,
                ],
                'rw_list' => array_values($rwSet),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'data'    => [],
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Detail lengkap 1 Keluarga (KK + Anggota Aktif + Anggota Arsip) untuk panel/modal interaktif Arsip
     */
    public function detailKeluargaArsip()
    {
        try {
            $rawIdKk = method_exists($this->request, 'getVar')
                ? $this->request->getVar('id_kk')
                : ($_GET['id_kk'] ?? ($_POST['id_kk'] ?? 0));
            $idKk = (int) ($rawIdKk ?? 0);
            if ($idKk <= 0) {
                return $this->response->setJSON(['status' => false, 'message' => 'ID KK tidak valid.']);
            }

            $db = \Config\Database::connect();
            $shdkRows = $db->table('tb_shdk')->select('id, jenis_shdk')->get()->getResultArray();
            $shdkMap  = array_column($shdkRows, 'jenis_shdk', 'id');

            $kk = $db->table('dtsen_kk k')
                ->select('k.*, rt.rw, rt.rt, rt.alamat AS alamat_rt, se.kategori_desil')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->join('dtsen_se se', 'se.id_kk = k.id_kk', 'left')
                ->where('k.id_kk', $idKk)
                ->get()
                ->getRowArray();

            if (!$kk) {
                return $this->response->setJSON(['status' => false, 'message' => 'Data Keluarga tidak ditemukan.']);
            }

            $kk['kk_is_deleted'] = !empty($kk['deleted_at']) && $kk['deleted_at'] !== '0000-00-00 00:00:00';
            $kk['id_kk_encoded'] = function_exists('encode_id') ? encode_id($idKk) : $idKk;
            $kk['rw'] = !empty($kk['rw']) ? (is_numeric($kk['rw']) ? str_pad((string) $kk['rw'], 3, '0', STR_PAD_LEFT) : $kk['rw']) : '-';
            $kk['rt'] = !empty($kk['rt']) ? (is_numeric($kk['rt']) ? str_pad((string) $kk['rt'], 3, '0', STR_PAD_LEFT) : $kk['rt']) : '-';
            $kk['alamat_final'] = !empty($kk['alamat']) ? $kk['alamat'] : ($kk['alamat_rt'] ?? '-');

            // Ambil semua anggota di dtsen_art (aktif & arsip)
            $masterArts = $db->table('dtsen_art')
                ->where('id_kk', $idKk)
                ->orderBy('shdk', 'ASC')
                ->orderBy('id_art', 'ASC')
                ->get()
                ->getResultArray();

            $activeMembers   = [];
            $archivedMembers = [];
            $activeNiks      = [];

            foreach ($masterArts as $m) {
                $nik = trim((string) ($m['nik'] ?? ''));
                $shdkLabel = $this->resolveShdkLabel($m['shdk'] ?? null, $m['hubungan_keluarga'] ?? null, $shdkMap);
                $isDel = !empty($m['deleted_at']) && $m['deleted_at'] !== '0000-00-00 00:00:00';

                if (!$isDel) {
                    if ($nik !== '') {
                        $activeNiks[$nik] = true;
                    }
                    $activeMembers[] = [
                        'id'            => (int) $m['id_art'],
                        'nik'           => $nik ?: '-',
                        'nama'          => $m['nama'] ?? '-',
                        'jenis_shdk'    => $shdkLabel,
                        'jenis_kelamin' => $m['jenis_kelamin'] ?? '-',
                        'tanggal_lahir' => ($m['tanggal_lahir'] && $m['tanggal_lahir'] !== '0000-00-00') ? $m['tanggal_lahir'] : '-',
                        'sumber'        => 'utama',
                    ];
                } else {
                    $archivedMembers[] = [
                        'uid'             => 'art_utama_' . (int) $m['id_art'],
                        'id'              => (int) $m['id_art'],
                        'id_kk'           => $idKk,
                        'nik'             => $nik ?: '-',
                        'nama'            => $m['nama'] ?? '-',
                        'jenis_shdk'      => $shdkLabel,
                        'jenis_kelamin'   => $m['jenis_kelamin'] ?? '-',
                        'tanggal_lahir'   => ($m['tanggal_lahir'] && $m['tanggal_lahir'] !== '0000-00-00') ? $m['tanggal_lahir'] : '-',
                        'deleted_at'      => $m['deleted_at'],
                        'delete_reason'   => $m['delete_reason'] ?? '-',
                        'sumber'          => 'utama',
                        'active_same_nik' => false, // dievaluasi di bawah
                    ];
                }
            }

            // Ambil juga anggota terarsip dari dtsen_usulan_art untuk KK ini
            $usulanArts = $db->table('dtsen_usulan_art ua')
                ->select('ua.*, u.status AS usulan_status')
                ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'inner')
                ->where('u.dtsen_kk_id', $idKk)
                ->where('ua.deleted_at IS NOT NULL')
                ->where('ua.deleted_at !=', '0000-00-00 00:00:00')
                ->orderBy('ua.deleted_at', 'DESC')
                ->get()
                ->getResultArray();

            foreach ($usulanArts as $ua) {
                $nik = trim((string) ($ua['nik'] ?? ''));
                $shdkLabel = $this->resolveShdkLabel(null, $ua['hubungan'] ?? null, $shdkMap);
                $archivedMembers[] = [
                    'uid'             => 'art_usulan_' . (int) $ua['id'],
                    'id'              => (int) $ua['id'],
                    'id_kk'           => $idKk,
                    'nik'             => $nik ?: '-',
                    'nama'            => $ua['nama'] ?? '-',
                    'jenis_shdk'      => $shdkLabel,
                    'jenis_kelamin'   => '-',
                    'tanggal_lahir'   => '-',
                    'deleted_at'      => $ua['deleted_at'],
                    'delete_reason'   => $ua['delete_reason'] ?? '-',
                    'sumber'          => 'usulan',
                    'active_same_nik' => false,
                ];
            }

            foreach ($archivedMembers as &$am) {
                if ($am['nik'] !== '-' && isset($activeNiks[$am['nik']])) {
                    $am['active_same_nik'] = true;
                }
            }
            unset($am);

            return $this->response->setJSON([
                'status'           => true,
                'kk'               => $kk,
                'active_members'   => $activeMembers,
                'archived_members' => $archivedMembers,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
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
            $shdkRows = $db->table('tb_shdk')->select('id, jenis_shdk')->get()->getResultArray();
            $shdkMap  = array_column($shdkRows, 'jenis_shdk', 'id');

            // ============================================================
            // 1) ARSIP DARI TABEL UTAMA: dtsen_art
            // ============================================================
            $arsipUtama = $db->table('dtsen_art a')
                ->select("
                a.id_art AS id,
                a.id_kk,
                a.nik,
                a.nama,
                a.shdk,
                a.hubungan_keluarga,
                a.deleted_at,
                a.delete_reason,
                k.no_kk,
                k.kepala_keluarga,
                'utama' AS sumber
            ")
                ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
                ->where('a.deleted_at IS NOT NULL')
                ->get()
                ->getResultArray();

            foreach ($arsipUtama as &$row) {
                $row['jenis_shdk'] = $this->resolveShdkLabel($row['shdk'] ?? null, $row['hubungan_keluarga'] ?? null, $shdkMap);
            }
            unset($row);

            // ============================================================
            // 2) ARSIP DARI TABEL USULAN: dtsen_usulan_art
            // ============================================================
            $arsipUsulan = $db->table('dtsen_usulan_art ua')
                ->select("
                ua.id AS id,
                u.dtsen_kk_id AS id_kk,
                ua.nik,
                ua.nama,
                ua.hubungan,
                ua.deleted_at,
                ua.delete_reason,
                k.no_kk,
                k.kepala_keluarga,
                'usulan' AS sumber
            ")
                ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'left')
                ->join('dtsen_kk k', 'k.id_kk = u.dtsen_kk_id', 'left')
                ->where('ua.deleted_at IS NOT NULL')
                ->get()
                ->getResultArray();

            foreach ($arsipUsulan as &$row) {
                $row['jenis_shdk'] = $this->resolveShdkLabel(null, $row['hubungan'] ?? null, $shdkMap);
            }
            unset($row);

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
            $id        = (int) ($this->request->getPost('id_art') ?? $this->request->getPost('id') ?? 0);
            $sumber    = trim((string) ($this->request->getPost('sumber') ?? 'utama'));
            $restoreKk = (bool) $this->request->getPost('restore_kk');
            $idKkParam = (int) ($this->request->getPost('id_kk') ?? 0);

            if ($id <= 0) {
                return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
            }

            $db = \Config\Database::connect();
            $db->transBegin();

            $targetIdKk = $idKkParam;

            if ($sumber === 'usulan') {
                if ($targetIdKk <= 0) {
                    $uaRow = $db->table('dtsen_usulan_art ua')
                        ->select('u.dtsen_kk_id AS id_kk')
                        ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'left')
                        ->where('ua.id', $id)
                        ->get()
                        ->getRowArray();
                    $targetIdKk = (int) ($uaRow['id_kk'] ?? 0);
                }

                $db->table('dtsen_usulan_art')->where('id', $id)->update([
                    'deleted_at'    => null,
                    'delete_reason' => null,
                ]);
            } else {
                $artRow = $db->table('dtsen_art')->select('id_art, id_kk')->where('id_art', $id)->get()->getRowArray();
                if ($artRow) {
                    if ($targetIdKk <= 0) {
                        $targetIdKk = (int) ($artRow['id_kk'] ?? 0);
                    }
                    $db->table('dtsen_art')->where('id_art', $id)->update([
                        'deleted_at'    => null,
                        'delete_reason' => null,
                    ]);
                } else {
                    // Fallback jika ID ternyata milik dtsen_usulan_art
                    $uaRow = $db->table('dtsen_usulan_art ua')
                        ->select('u.dtsen_kk_id AS id_kk')
                        ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'left')
                        ->where('ua.id', $id)
                        ->get()
                        ->getRowArray();
                    $targetIdKk = (int) ($uaRow['id_kk'] ?? 0);

                    $db->table('dtsen_usulan_art')->where('id', $id)->update([
                        'deleted_at'    => null,
                        'delete_reason' => null,
                    ]);
                }
            }

            $restoredKk = 0;
            if ($restoreKk && $targetIdKk > 0) {
                $db->table('dtsen_kk')
                    ->where('id_kk', $targetIdKk)
                    ->where('deleted_at IS NOT NULL')
                    ->update([
                        'deleted_at'    => null,
                        'delete_reason' => null,
                    ]);
                $restoredKk = $db->affectedRows() > 0 ? 1 : 0;
            }

            $db->transCommit();

            $msg = $restoredKk > 0
                ? 'Data penduduk beserta Kartu Keluarga (KK) induknya berhasil dipulihkan.'
                : 'Data penduduk berhasil dipulihkan.';

            return $this->response->setJSON([
                'status'       => true,
                'restored_art' => 1,
                'restored_kk'  => $restoredKk,
                'message'      => $msg
            ]);
        } catch (\Throwable $e) {
            if (isset($db)) {
                $db->transRollback();
            }
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // app/Controllers/Dtsen/DtsenSe.php
    // 🔐 Daftar RW mengikuti WilayahFilterTrait:
    //    - role_id >= 4                     → hanya RW wilayah tugas sendiri
    //    - role_id <= 3 + ?petugas=<id>     → hanya RW wilayah tugas petugas tsb
    //    - role_id <= 3 tanpa petugas        → seluruh RW desa
    public function listRW()
    {
        $session  = session();
        $kodeDesa = $session->get('kode_desa');

        $scope = $this->resolveWilayahScope($this->request->getGet('petugas'));

        if ($scope['role_id'] >= 4) {
            $allowedRw = self::getAllowedRwList($scope['wilayah_tugas'], $scope['level']);
            $data = array_values(array_unique(array_map(fn($rw) => $this->padWilayah($rw), $allowedRw)));
            sort($data);

            return $this->response->setJSON(['data' => $data]);
        }

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

        $scope = $this->resolveWilayahScope($this->request->getGet('petugas'));

        if ($scope['role_id'] >= 4) {
            // RW harus termasuk wilayah tugas
            $allowedRw = array_map(fn($r) => $this->padWilayah($r), self::getAllowedRwList($scope['wilayah_tugas'], $scope['level']));
            if (!in_array($this->padWilayah($rw), $allowedRw, true)) {
                return $this->response->setJSON(['data' => []]);
            }

            // RT spesifik yang ditugaskan (kosong = seluruh RT di RW tsb)
            $allowedRt = self::getAllowedRtList($scope['wilayah_tugas'], (string) $rw, $scope['level']);
            if (!empty($allowedRt)) {
                $data = array_values(array_unique(array_map(fn($rt) => $this->padWilayah($rt), $allowedRt)));
                sort($data);

                return $this->response->setJSON(['data' => $data]);
            }
        }

        $db = \Config\Database::connect();

        $rows = $db->table('tb_rt')
            ->select('no_rt')
            ->distinct()
            ->where('kode_desa', $kodeDesa)
            ->whereIn('no_rw', self::getWilayahVariants($rw) ?: [$rw])
            ->orderBy('no_rt', 'ASC')
            ->get()
            ->getResultArray();

        $data = array_map(fn($r) => $r['no_rt'], $rows);

        return $this->response->setJSON(['data' => $data]);
    }

    // ========================================================
    // 🚨 QUERY TABEL PEMULIHAN (MODE X-RAY / PUKAT HARIMAU)
    // ========================================================
    // 2. Update Query Tabel Pemulihan (Masukkan data yang di-flag)
    public function tabel_pemulihan()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('dtsen_kk k')
            ->select('k.id_kk, k.no_kk, k.kepala_keluarga, k.alamat as alamat_kk, r.id_rt, r.rt, r.rw, r.alamat as alamat_rt, k.is_recovery_needed')
            ->join('dtsen_rt r', 'k.id_rt = r.id_rt', 'left')
            ->where('k.deleted_at IS NULL')
            ->groupStart()
            ->where('r.rt', null)->orWhere('r.rt', '')
            ->orWhere('LENGTH(TRIM(r.rt)) <', 3)
            ->orWhere('k.is_recovery_needed', 1) // 🚩 Tarik data yang ditandai Pentri
            ->groupEnd();

        return $this->response->setJSON(['data' => $builder->get()->getResultArray()]);
    }

    // 3. Fungsi untuk Pentri "Menarik" Data
    public function tarik_ke_pemulihan()
    {
        $id_kk = $this->request->getPost('id_kk');
        $db = \Config\Database::connect();

        $db->table('dtsen_kk')->where('id_kk', $id_kk)->update([
            'is_recovery_needed' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dikirim ke tabel Pemulihan Admin.']);
    }

    // ========================================================
    // 🪄 EKSEKUSI AUTO-FIX DARI MODAL FRONTEND
    // ========================================================
    public function autofix_rt_rw()
    {
        // 1. Ambil data dari form (sesuaikan nama variabel dengan rapi)
        $id_rt   = $this->request->getPost('id_rt');
        $rt_baru = $this->request->getPost('rt_baru');
        $rw_baru = $this->request->getPost('rw_baru');

        // 2. Validasi
        if (empty($id_rt)) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID RT tidak valid.']);
        }

        try {
            // 3. Panggil koneksi database
            $db = \Config\Database::connect();

            // 4. Eksekusi Update ke tabel dtsen_rt
            $db->table('dtsen_rt')
                ->where('id_rt', $id_rt)
                ->update([
                    'rt'         => $rt_baru,
                    'rw'         => $rw_baru,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => session()->get('id') ?? 'system'
                ]);

            // 5. Kembalikan respon sukses
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Format RT/RW berhasil dipulihkan!'
            ]);
        } catch (\Throwable $e) {
            // Tangkap jika ada error server/database
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menerapkan data: ' . $e->getMessage()
            ]);
        }
    }

    // ========================================================
    // 🔍 SEARCH KK UNTUK SELECT2 (PENCARIAN GLOBAL)
    // ========================================================
    public function search_kk_select2()
    {
        $searchTerm = $this->request->getPost('searchTerm');
        $db = \Config\Database::connect();

        $data = $db->table('dtsen_kk k')
            ->select('k.id_kk, k.no_kk, k.kepala_keluarga, r.rt, r.rw')
            ->join('dtsen_rt r', 'k.id_rt = r.id_rt', 'left')
            ->like('k.no_kk', $searchTerm)
            ->orLike('k.kepala_keluarga', $searchTerm)
            ->where('k.deleted_at IS NULL')
            ->limit(10)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                "id"   => $row['id_kk'],
                "text" => $row['no_kk'] . " - " . strtoupper($row['kepala_keluarga']) . " (Wilayah: RT " . ($row['rt'] ?? '-') . "/RW " . ($row['rw'] ?? '-') . ")"
            ];
        }

        return $this->response->setJSON($result);
    }
}
