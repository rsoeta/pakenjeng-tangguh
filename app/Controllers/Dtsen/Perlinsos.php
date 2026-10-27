<?php

namespace App\Controllers\Dtsen;

use App\Models\Dtks\AuthModel;
use App\Models\Dtsen\DtsenArtModel;
use App\Models\GenModel;
use CodeIgniter\Controller;

class Perlinsos extends Controller
{
    protected $AuthModel;
    protected $artModel;
    protected $GenModel;
    protected $db;

    public function __construct()
    {
        $this->AuthModel = new AuthModel();
        $this->artModel  = new DtsenArtModel();
        $this->GenModel  = new GenModel();
        $this->db        = \Config\Database::connect();

        helper(['opdtks_helper', 'opdtsen_helper']);
    }

    public function index()
    {
        $session = session();
        $userInfo = $this->AuthModel->getUserId();

        $userData = array_merge([
            'nama'        => $session->get('fullname') ?? '-',
            'username'    => $session->get('username') ?? '-',
            'kode_desa'   => $session->get('kode_desa') ?? '',
            'role_id'     => $session->get('role_id') ?? '',
            'nik'         => $session->get('nik') ?? '',
        ], is_array($userInfo) ? $userInfo : []);

        // 🚀 GATEKEEPER: Cek Status Akses (Khusus Pentri)
        $akses = null;
        if ($userData['role_id'] == 4) {
            $akses = $this->db->table('dtsen_perlinsos_akses')->where('nik_petugas', $userData['nik'])->get()->getRowArray();
        }

        $data = [
            'title'      => 'Rekapitulasi Perlinsos',
            'namaApp'    => nameApp(),
            'user_login' => $userData,
            'akses'      => $akses // Bawa data akses ke view
        ];

        return view('dtsen/perlinsos/index', $data);
    }

    /**
     * 🔍 Cari individu berdasarkan NIK atau No. KK
     * 🔐 Dibatasi wilayah_tugas user login (multi RW–RT) & Kode Desa
     * 🛡️ Anti Data Ganda & Bypass Soft Delete
     */
    public function searchArt()
    {
        $term = trim((string) $this->request->getGet('q'));
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
            dtsen_art.id_kk, 
            dtsen_kk.no_kk,
            dtsen_art.hubungan_keluarga AS shdk,
            (SELECT jenis_shdk FROM tb_shdk WHERE id = dtsen_art.hubungan_keluarga) AS shdk_nama,
            dtsen_rt.rw,
            dtsen_rt.rt
        ")
            // 🚀 PERBAIKAN 1: Pastikan KK yang ditarik adalah KK yang masih aktif
            ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk AND dtsen_kk.deleted_at IS NULL', 'left')
            ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')

            // 🚀 PERBAIKAN 2: Gunakan sintaks string statis agar Query Builder mutlak membaca IS NULL
            ->where('dtsen_art.deleted_at IS NULL')
            ->where('dtsen_kk.deleted_at IS NULL')
            ->groupStart()
            ->like('dtsen_art.nik', $term)
            ->orLike('dtsen_kk.no_kk', $term)
            ->groupEnd();

        // 🔐 Kunci kepemilikan data level desa
        if (!empty($kodeDesa)) {
            $builder->where('dtsen_rt.kode_desa', $kodeDesa);
        }

        // 🚀 PERBAIKAN 3: Kunci mutlak NIK agar tidak ada nama yang sama muncul dua kali
        $builder->groupBy('dtsen_art.nik')
            ->limit(15);

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
       🎯 Format JSON sesuai standar Select2 & Form Perlinsos
       ===================================================== */
        $results = array_map(function ($row) {
            $noKk = !empty($row['no_kk']) ? ' (KK: ' . $row['no_kk'] . ')' : '';
            return [
                'id'         => $row['nik'],
                'text'       => $row['nik'] . ' - ' . strtoupper($row['nama']) . $noKk,
                'nik'        => $row['nik'],
                'nama'       => $row['nama'],
                'no_kk'      => $row['no_kk'] ?? '',
                'id_kk'      => $row['id_kk'], // 🚀 KUNCI SAKTI: Wajib ada untuk simpan Perlinsos
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

    // 🚀 SIMPAN NIK SEDERHANA (DENGAN PROTEKSI GANDA)
    public function save()
    {
        $nik   = $this->request->getPost('nik_peserta');
        $id_kk = $this->request->getPost('id_kk');
        $user  = session()->get('nik');

        // 1. Cek duplikasi NIK (Mencegah orang yang sama diinput 2x)
        $cekNik = $this->db->table('dtsen_perlinsos')->where('nik', $nik)->countAllResults();
        if ($cekNik > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'NIK ini sudah direkapitulasi sebelumnya!'
            ]);
        }

        // 2. 🚀 Cek duplikasi ID KK (Mencegah anggota keluarga lain dalam 1 KK diinput lagi)
        $cekKk = $this->db->table('dtsen_perlinsos')->where('id_kk', $id_kk)->countAllResults();
        if ($cekKk > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'KPM dari Kartu Keluarga (KK) ini sudah direkapitulasi sebelumnya! 1 KK hanya boleh memiliki 1 pendaftar.'
            ]);
        }

        // 3. Jika lolos kedua hadangan di atas, simpan datanya
        $this->db->table('dtsen_perlinsos')->insert([
            'nik'        => $nik,
            'id_kk'      => $id_kk,
            'created_by' => $user,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil direkap!'
        ]);
    }

    // ========================================================
    // 🚀 1. FILTER OPTIONS UNTUK DROPDOWN
    // ========================================================
    public function getFilterOptions()
    {
        $kodeDesa = session()->get('kode_desa');

        // 🚀 UPDATE: Hanya ambil petugas yang masih aktif
        $petugas = $this->db->table('dtsen_perlinsos p')
            ->select('p.created_by as nik, u.fullname')
            ->join('dtks_users u', 'u.nik = p.created_by', 'left')
            ->where('u.status', 1) // 👈 SENSOR USER NON-AKTIF
            ->groupBy('p.created_by')
            ->get()->getResult();

        $rw = $this->db->table('dtsen_rt')->select('rw')->where('kode_desa', $kodeDesa)->where('rw IS NOT NULL')->groupBy('rw')->get()->getResult();

        return $this->response->setJSON(['petugas' => $petugas, 'rw' => $rw]);
    }

    // ========================================================
    // 🚀 3. CHART CAPAIAN VS TARGET (EXPERT MODE)
    // ========================================================
    public function getChartCapaian()
    {
        $kodeDesa = session()->get('kode_desa');

        $users = $this->db->table('dtks_users')
            ->select('nik, fullname, wilayah_tugas')
            ->where('role_id', 4) // Hanya PENTRI
            ->where('status', 1)
            ->get()->getResultArray();

        $categories = [];
        $seriesBelumTercapai = [];
        $seriesBelumVerifikasi = [];
        $seriesTerverifikasi = [];

        foreach ($users as $u) {
            $nikPetugas = $u['nik'];
            $namaPendek = explode(' ', trim($u['fullname']))[0];

            // 1. Total capaian KPM yang diinput oleh petugas
            $capaian = $this->db->table('dtsen_perlinsos')->where('created_by', $nikPetugas)->countAllResults();

            // 2. Data yang sudah berhasil diverifikasi (tersinkron)
            $terverifikasi = $this->db->table('dtsen_perlinsos')
                ->where('created_by', $nikPetugas)
                ->where('status', 'tersinkron')
                ->countAllResults();

            // 3. Data yang berhasil direkap tapi belum diverifikasi
            $belumVerifikasi = max(0, $capaian - $terverifikasi);

            $target = 0;
            $wilayahTugas = trim($u['wilayah_tugas']);

            if (!empty($wilayahTugas)) {
                $b_kk = $this->db->table('dtsen_kk kk')
                    ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
                    ->where('kk.deleted_at IS NULL')
                    ->where('rt.kode_desa', $kodeDesa);

                $wilayahPairs = [];
                foreach (explode('|', $wilayahTugas) as $block) {
                    [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                    foreach (explode(',', $rtList) as $rt) {
                        if (trim($rw) !== '' && trim($rt) !== '') {
                            $wilayahPairs[] = ['rw' => trim($rw), 'rt' => trim($rt)];
                        }
                    }
                }

                if (!empty($wilayahPairs)) {
                    $b_kk->groupStart();
                    foreach ($wilayahPairs as $pair) {
                        $b_kk->orGroupStart()
                            ->where('rt.rw', $pair['rw'])
                            ->where('rt.rt', $pair['rt'])
                            ->groupEnd();
                    }
                    $b_kk->groupEnd();
                    $target = $b_kk->countAllResults();
                }
            }

            if ($target > 0 || $capaian > 0) {
                $categories[] = $namaPendek;
                // 🚀 Tumpukan 1: Belum Tercapai (Merah)
                $seriesBelumTercapai[] = max(0, $target - $capaian);
                // 🚀 Tumpukan 2: Belum Diverifikasi (Kuning)
                $seriesBelumVerifikasi[] = $belumVerifikasi;
                // 🚀 Tumpukan 3: Terverifikasi (Hijau)
                $seriesTerverifikasi[] = $terverifikasi;
            }
        }

        return $this->response->setJSON([
            'categories' => $categories,
            'series'     => [
                ['name' => 'Belum Tercapai', 'data' => $seriesBelumTercapai],
                ['name' => 'Belum Verifikasi', 'data' => $seriesBelumVerifikasi],
                ['name' => 'Terverifikasi', 'data' => $seriesTerverifikasi]
            ]
        ]);
    }

    // ========================================================
    // 🚀 FILTER OPSI RT (ANTI GANDA)
    // ========================================================
    public function getRTByRW($rw)
    {
        $kodeDesa = session()->get('kode_desa');
        $rt = $this->db->table('dtsen_rt')
            ->select('rt')
            ->where('kode_desa', $kodeDesa)
            ->where('rw', $rw)
            ->where('rt IS NOT NULL')
            ->groupBy('rt') // 🚀 KUNCI SAKTI: Gabungkan RT yang kembar
            ->orderBy('rt', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON(['rt' => array_column($rt, 'rt')]);
    }

    // ========================================================
    // 🚀 2. GET DATA (DENGAN ALAMAT & ANTI DUPLIKAT)
    // ========================================================
    public function getData()
    {
        $roleId = (int) session()->get('role_id');
        $userNik = session()->get('nik');

        // Tangkap Filter
        $f_petugas = $this->request->getPost('petugas');
        $f_rw      = $this->request->getPost('rw');
        $f_rt      = $this->request->getPost('rt');
        $f_status  = $this->request->getPost('status');

        $builder = $this->db->table('dtsen_perlinsos p')
            ->select('
                p.id, p.nik, p.created_at, p.status, 
                a.nama as nama_kpm,
                u.fullname as petugas, 
                k.no_kk, k.kepala_keluarga,
                k.alamat, r.rt, r.rw
            ') // 🚀 Pastikan p.status ikut di-select
            ->join('dtsen_art a', 'a.nik = p.nik AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = p.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt r', 'r.id_rt = k.id_rt', 'left')
            ->join('dtks_users u', 'u.nik = p.created_by', 'left')
            ->groupBy('p.id')
            ->orderBy('p.created_at', 'DESC');

        // 🔐 Terapkan Filter
        if ($roleId === 4) {
            $builder->where('p.created_by', $userNik);
        } else if (!empty($f_petugas)) {
            $builder->where('p.created_by', $f_petugas);
        }

        if (!empty($f_rw)) $builder->where('r.rw', $f_rw);
        if (!empty($f_rt)) $builder->where('r.rt', $f_rt);

        // 🎯 Filter Status Verifikasi
        if (!empty($f_status)) {
            if ($f_status === 'terverifikasi') {
                $builder->where('p.status', 'tersinkron');
            } elseif ($f_status === 'belum_verifikasi') {
                $builder->groupStart()
                    ->where('p.status !=', 'tersinkron')
                    ->orWhere('p.status IS NULL')
                    ->groupEnd();
            }
        }

        return $this->response->setJSON(['data' => $builder->get()->getResultArray()]);
    }

    // // 🚀 HAPUS DATA
    // public function delete($id)
    // {
    //     $this->db->table('dtsen_perlinsos')->where('id', $id)->delete();
    //     return $this->response->setJSON(['success' => true, 'message' => 'Rekapitulasi dibatalkan.']);
    // }

    // 🚀 VALIDASI DATA OLEH OPERATOR (KUNCI SINKRONISASI)
    public function verifyData($id)
    {
        if (session()->get('role_id') > 3) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya Operator/Admin yang dapat memvalidasi sinkronisasi data.']);
        }

        $this->db->table('dtsen_perlinsos')->where('id', $id)->update([
            'status'     => 'tersinkron',
            // 'updated_at' => date('Y-m-d H:i:s') // Aktifkan jika ada kolom updated_at
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil divalidasi dan dikunci.']);
    }

    // 🚀 HAPUS DATA (DENGAN PROTEKSI STATUS SINKRON)
    public function delete($id)
    {
        // 🔒 Gatekeeper Backend: Cek apakah data sudah divalidasi (Tersinkron)
        $data = $this->db->table('dtsen_perlinsos')->where('id', $id)->get()->getRowArray();

        if ($data && ($data['status'] ?? '') === 'tersinkron') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses Ditolak! Data ini sudah divalidasi (sinkron dengan SIKS-NG) dan dikunci permanen.'
            ]);
        }

        $this->db->table('dtsen_perlinsos')->where('id', $id)->delete();
        return $this->response->setJSON(['success' => true, 'message' => 'Rekapitulasi dibatalkan.']);
    }

    // ========================================================
    // 🚀 API GATEKEEPER (PENTRI & ADMIN)
    // ========================================================

    // 1. Fungsi Pentri Upload Bukti
    public function uploadBukti()
    {
        $file = $this->request->getFile('bukti_screenshot');
        $nik  = session()->get('nik');

        // Validasi file
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid atau rusak.']);
        }

        // Pastikan format gambar
        $mime = $file->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/jpg'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Format file harus JPG, JPEG, atau PNG.']);
        }

        // Ukuran maksimal 2MB
        if ($file->getSizeByUnit('mb') > 2) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 2MB.']);
        }

        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/perlinsos', $newName);

        $cek = $this->db->table('dtsen_perlinsos_akses')->where('nik_petugas', $nik)->get()->getRowArray();

        if ($cek) {
            // Hapus file lama jika ditolak lalu upload ulang
            if (file_exists(FCPATH . 'uploads/perlinsos/' . $cek['file_bukti'])) {
                unlink(FCPATH . 'uploads/perlinsos/' . $cek['file_bukti']);
            }
            $this->db->table('dtsen_perlinsos_akses')->where('nik_petugas', $nik)->update([
                'file_bukti' => $newName,
                'status'     => 'pending',
                'catatan'    => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->db->table('dtsen_perlinsos_akses')->insert([
                'nik_petugas' => $nik,
                'file_bukti'  => $newName,
                'status'      => 'pending',
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Bukti berhasil diunggah. Silakan tunggu verifikasi admin.']);
    }

    // 2. Fungsi Admin Get Data Tabel Akses
    public function getDataAkses()
    {
        if (session()->get('role_id') > 3) return $this->response->setJSON(['data' => []]); // Tolak jika bukan admin

        $builder = $this->db->table('dtsen_perlinsos_akses a')
            ->select('a.id, a.nik_petugas, a.file_bukti, a.status, a.catatan, a.updated_at, a.created_at, u.fullname')
            ->join('dtks_users u', 'u.nik = a.nik_petugas', 'left')
            // Urutkan: Pending di atas, sisanya di bawah
            ->orderBy("FIELD(a.status, 'pending', 'rejected', 'approved')")
            ->orderBy('a.created_at', 'DESC');

        return $this->response->setJSON(['data' => $builder->get()->getResultArray()]);
    }

    // 3. Fungsi Admin Eksekusi Setuju/Tolak
    public function verifyAkses()
    {
        if (session()->get('role_id') > 3) return $this->response->setJSON(['success' => false, 'message' => 'Akses Ditolak']);

        $id      = $this->request->getPost('id');
        $status  = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan');
        $admin   = session()->get('nik');

        $this->db->table('dtsen_perlinsos_akses')->where('id', $id)->update([
            'status'     => $status,
            'catatan'    => $catatan,
            'updated_by' => $admin,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Status akses berhasil diperbarui.']);
    }
}
