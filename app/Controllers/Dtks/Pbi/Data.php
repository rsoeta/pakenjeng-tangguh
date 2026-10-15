<?php

namespace App\Controllers\Dtks\Pbi;

use App\Controllers\BaseController;
use App\Models\PbiMasterDataModel;
use App\Models\Dtks\AuthModel;

class Data extends BaseController
{
    protected $pbiModel;
    protected $AuthModel;

    public function __construct()
    {
        $this->pbiModel = new PbiMasterDataModel();
        $this->AuthModel = new AuthModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Master Data PBI-JKN (Aktif)',
        ];
        return view('dtks/pbi/v_data_aktif', $data);
    }

    public function datatable()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $post = $this->request->getPost();

        // 🚀 Ambil data user dari model Auth
        $userInfo = $this->AuthModel->getUserId();
        $kodeDesa = $userInfo['kode_desa'] ?? session()->get('kode_desa');
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');

        // 🚀 Tangkap role_id di awal agar bisa dipakai untuk filter wilayah
        $roleId = (int) ($userInfo['role_id'] ?? session()->get('role_id') ?? 99);

        $draw   = (int) ($post['draw'] ?? 1);
        $start  = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $search = $post['search']['value'] ?? '';

        $builder = $this->pbiModel->builder()->where('status_kepesertaan', 'Aktif');

        if (!empty($kodeDesa)) {
            $builder->where('kode_desa', $kodeDesa);
        }

        // 1️⃣ 🔐 GEMBOK WILAYAH KERJA (Mengadopsi Logika Tangguh Exclude)
        if ($roleId <= 5 && !empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $builder->groupStart();
            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rwInt = (int) trim($rw);

                if ($rwInt > 0) {
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        foreach ($rts as $rt) {
                            $rtInt = (int) trim($rt);
                            if ($rtInt > 0) {
                                // Langsung CAST rw dan rt dari tabel PBI
                                $builder->orWhere("(CAST(rw AS UNSIGNED) = {$rwInt} AND CAST(rt AS UNSIGNED) = {$rtInt})");
                            }
                        }
                    } else {
                        $builder->orWhere("CAST(rw AS UNSIGNED) = {$rwInt}");
                    }
                }
            }
            $builder->groupEnd();
        }

        // 2️⃣ 🎛️ FILTER UI (RW / RT) - Disamakan pakai CAST agar kebal format
        if (!empty($post['rw'])) {
            $builder->where("CAST(rw AS UNSIGNED) =", (int) $post['rw']);
        }
        if (!empty($post['rt'])) {
            $builder->where("CAST(rt AS UNSIGNED) =", (int) $post['rt']);
        }

        // 🔍 Pencarian
        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama', $search)
                ->orLike('nik', $search)
                ->orLike('no_kk', $search)
                ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        if ($length != -1) $builder->limit($length, $start);

        $results = $builder->orderBy('rw', 'ASC')->orderBy('rt', 'ASC')->get()->getResultArray();

        // 3️⃣ 🔐 HITUNG TOTAL DATA MURNI (Beserta Gembok Wilayah Tugas)
        $totalBuilder = $this->pbiModel->builder()->where('status_kepesertaan', 'Aktif');
        if (!empty($kodeDesa)) $totalBuilder->where('kode_desa', $kodeDesa);

        if ($roleId <= 5 && !empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $totalBuilder->groupStart();
            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rwInt = (int) trim($rw);
                if ($rwInt > 0) {
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        foreach ($rts as $rt) {
                            $rtInt = (int) trim($rt);
                            if ($rtInt > 0) {
                                $totalBuilder->orWhere("(CAST(rw AS UNSIGNED) = {$rwInt} AND CAST(rt AS UNSIGNED) = {$rtInt})");
                            }
                        }
                    } else {
                        $totalBuilder->orWhere("CAST(rw AS UNSIGNED) = {$rwInt}");
                    }
                }
            }
            $totalBuilder->groupEnd();
        }
        $recordsTotal = $totalBuilder->countAllResults();

        // 🚀 RENDER DATA KE ARRAY (Sama seperti sebelumnya)
        $data = [];
        $no = $start + 1;

        foreach ($results as $row) {
            $btnAksi = '-';

            // 🔐 Hanya role 1, 2, 3 yang boleh melihat tombol Aksi
            if ($roleId < 4) {
                $btnAksi = '
                    <div class="d-flex gap-1 justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm px-2" onclick="editPbi(\'' . $row['id'] . '\', \'' . $row['nik'] . '\', \'' . ($row['id_art'] ?? '') . '\', \'' . esc($row['no_kk'] ?? '') . '\', \'' . esc($row['nama']) . '\', \'' . esc($row['kampung'] ?? '') . '\', \'' . str_pad($row['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . '\', \'' . str_pad($row['rw'] ?? '0', 3, '0', STR_PAD_LEFT) . '\', \'' . esc($row['no_kis'] ?? '') . '\', \'' . esc($row['jenis_kepesertaan'] ?? '') . '\', \'' . esc($row['faskes_tk1'] ?? '') . '\')" title="Edit Data Kepesertaan">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger shadow-sm px-2" onclick="nonAktifkan(\'' . $row['id'] . '\', \'' . esc($row['nama']) . '\')" title="Non-Aktifkan Data">
                            <i class="fas fa-user-times"></i>
                        </button>
                    </div>
                ';
            }

            $alamat = ($row['kampung'] ?? '-') . '<br><small class="text-muted">RT ' . str_pad($row['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($row['rw'] ?? '0', 3, '0', STR_PAD_LEFT) . '</small>';

            $jenisKepesertaan = strtoupper(trim($row['jenis_kepesertaan'] ?? ''));
            $badgeClass = 'bg-secondary';

            if (strpos($jenisKepesertaan, 'APBD') !== false) {
                $badgeClass = 'bg-success';
            } elseif (strpos($jenisKepesertaan, 'APBN') !== false) {
                $badgeClass = 'bg-primary';
            } elseif (strpos($jenisKepesertaan, 'MANDIRI') !== false || strpos($jenisKepesertaan, 'PBPU') !== false) {
                $badgeClass = 'bg-warning text-dark';
            }

            $badgeHTML = '<br><span class="badge ' . $badgeClass . ' shadow-sm mt-1"><i class="fas fa-hospital-user"></i> ' . esc($jenisKepesertaan ?: 'Belum Terdata') . '</span>';

            $data[] = [
                $no++,
                '<b>' . esc($row['nama']) . '</b><br><small class="text-muted">NIK: ' . $row['nik'] . ' <a href="javascript:void(0)" onclick="copyText(\'' . $row['nik'] . '\')" class="text-primary ms-1"><i class="far fa-copy"></i></a></small>',
                $row['no_kk'] ? $row['no_kk'] . ' <a href="javascript:void(0)" onclick="copyText(\'' . $row['no_kk'] . '\')" class="text-primary ms-1"><i class="far fa-copy"></i></a>' : '-',
                '<span class="badge bg-info text-dark shadow-sm"><i class="fas fa-id-card"></i> ' . esc($row['no_kis'] ?? '-') . '</span>' . $badgeHTML,
                esc($row['faskes_tk1'] ?? '-'),
                $alamat,
                $btnAksi
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function import_excel()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        if (session()->get('role_id') >= 4) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses ditolak!']);
        }

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File Excel tidak valid.']);
        }

        $kodeDesa = session()->get('kode_desa') ?? '32.05.33.2006';
        $userId   = session()->get('id_user') ?? session()->get('id') ?? 1;
        $db       = \Config\Database::connect();
        $periode  = date('Y-m');

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet()->toArray();

            // 1️⃣ OPTIMASI: Cache Data Warga (ART) beserta RT & RW
            $artData = $db->table('dtsen_art a')
                ->select('a.nik, a.id_art, rt.rt, rt.rw') // 👈 SEKARANG KITA TARIK JUGA RT & RW-NYA!
                ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->where('rt.kode_desa', $kodeDesa)
                ->where('a.deleted_at', null)
                ->get()->getResultArray();

            // Mapping ulang agar menyimpan 1 baris utuh berdasarkan NIK
            $mapArt = [];
            foreach ($artData as $art) {
                $mapArt[$art['nik']] = $art;
            }

            // 2️⃣ OPTIMASI: Cache Data PBI Existing
            $pbiData = $db->table('pbi_master_data')->select('nik')->where('kode_desa', $kodeDesa)->get()->getResultArray();
            $mapPbi  = array_column($pbiData, 'nik', 'nik');

            $dataInsert = [];
            $dataUpdate = [];
            $nikList    = [];

            for ($i = 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];

                $nik = trim($row[2] ?? '');

                if (empty($nik) || !is_numeric($nik) || in_array($nik, $nikList)) {
                    continue;
                }
                $nikList[] = $nik;

                // 🧠 AMBIL DATA DARI CACHE DATABASE
                $dbArt = $mapArt[$nik] ?? null;
                $idArt = $dbArt['id_art'] ?? null;

                // Prioritaskan RT/RW dari Database SINDEN
                $rt = $dbArt['rt'] ?? '000';
                $rw = $dbArt['rw'] ?? '000';

                // 🎯 EKSTRAK ALAMAT & REGEX SEBAGAI CADANGAN
                $alamatMentah = trim($row[6] ?? '');
                $kampung = $alamatMentah;

                // Jika di DB tidak ada (000), baru kita coba tebak dari teks Excel
                if ($rt === '000' && preg_match('/RT[\.\s]*0*(\d+)/i', $alamatMentah, $matchesRt)) {
                    $rt = str_pad((int)$matchesRt[1], 3, '0', STR_PAD_LEFT);
                }
                if ($rw === '000' && preg_match('/RW[\.\s]*0*(\d+)/i', $alamatMentah, $matchesRw)) {
                    $rw = str_pad((int)$matchesRw[1], 3, '0', STR_PAD_LEFT);
                }

                // 🚦 KONVERSI STATUS, KEPESERTAAN & ALASAN NON-AKTIF
                $jenisKepesertaan = trim($row[8] ?? '');
                $statusMentah = strtoupper(trim($row[9] ?? ''));

                $isAktif = (strpos($statusMentah, 'AKTIF') !== false && strpos($statusMentah, 'NON') === false);
                $statusPbi = $isAktif ? 'Aktif' : 'Non-Aktif';
                $alasanNonAktif = $isAktif ? null : $statusMentah;

                $dataRow = [
                    'nik'                => $nik,
                    'id_art'             => $idArt, // 👈 Terisi dari array mapping
                    'no_kk'              => trim($row[1] ?? ''),
                    'nama'               => trim($row[3] ?? ''),
                    'no_kis'             => trim($row[7] ?? ''),
                    'jenis_kepesertaan'  => $jenisKepesertaan,
                    'kampung'            => $kampung,
                    'rt'                 => $rt,  // 👈 Terisi presisi!
                    'rw'                 => $rw,  // 👈 Terisi presisi!
                    'kode_desa'          => $kodeDesa,
                    'status_kepesertaan' => $statusPbi,
                    'alasan_nonaktif'    => $alasanNonAktif,
                    'periode_sinkron_terakhir' => $periode,
                ];

                if (isset($mapPbi[$nik])) {
                    $dataRow['updated_by'] = $userId;
                    $dataRow['updated_at'] = date('Y-m-d H:i:s');
                    $dataUpdate[] = $dataRow;
                } else {
                    $dataRow['created_by'] = $userId;
                    $dataInsert[] = $dataRow;
                }
            }

            if (count($dataInsert) > 0) {
                $this->pbiModel->insertBatch($dataInsert);
            }
            if (count($dataUpdate) > 0) {
                $this->pbiModel->updateBatch($dataUpdate, 'nik');
            }

            $totalBaris = count($dataInsert) + count($dataUpdate);

            return $this->response->setJSON([
                'status'  => true,
                'message' => "Selesai! <b>{$totalBaris}</b> data sinkronisasi.<br><small>(" . count($dataInsert) . " Baru, " . count($dataUpdate) . " Diperbarui)</small>"
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Sistem gagal membaca Excel: ' . $e->getMessage()
            ]);
        }
    }

    public function cek_nik()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $nik = trim($this->request->getPost('nik'));

        $cekPbi = $this->pbiModel->where('nik', $nik)->first();
        if ($cekPbi && $cekPbi['status_kepesertaan'] == 'Aktif') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Warga ini sudah terdaftar sebagai peserta PBI Aktif!'
            ]);
        }

        $db = \Config\Database::connect();

        $artData = $db->table('dtsen_art a')
            ->select('a.id_art, a.nik, a.nama, k.no_kk, k.alamat as kampung, rt.rt, rt.rw')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->where('a.nik', $nik)
            ->where('a.deleted_at', null)
            ->get()->getRowArray();

        if ($artData) {
            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'id_art'  => $artData['id_art'],
                    'nik'     => $artData['nik'],
                    'nama'    => $artData['nama'],
                    'no_kk'   => $artData['no_kk'],
                    'kampung' => $artData['kampung'] ?? '-',
                    'rt'      => $artData['rt'] ?? '000',
                    'rw'      => $artData['rw'] ?? '000',
                    'pbi_id'  => $cekPbi ? $cekPbi['id'] : null
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'NIK tidak ditemukan di database kependudukan!'
            ]);
        }
    }

    public function simpan_manual()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $post = $this->request->getPost();
        $kodeDesa = session()->get('kode_desa') ?? '32.05.33.2006';
        $userId   = session()->get('id_user') ?? session()->get('id') ?? 1;

        // 🛡️ PENYARING ANTI-ERROR 500 (Ubah teks "null" jadi murni Null/Integer)
        $idArt = (!empty($post['id_art']) && $post['id_art'] !== 'null') ? (int) $post['id_art'] : null;
        $pbiId = (!empty($post['pbi_id']) && $post['pbi_id'] !== 'null') ? $post['pbi_id'] : null;

        $dataPbi = [
            'nik'                => $post['nik'],
            'id_art'             => $idArt,
            'no_kk'              => $post['no_kk'],
            'nama'               => $post['nama'],
            'no_kis'             => $post['no_kis'],
            'jenis_kepesertaan'  => $post['jenis_kepesertaan'] ?? 'PBI APBN', // Default jika kosong
            'faskes_tk1'         => $post['faskes_tk1'],
            'kampung'            => $post['kampung'],
            'rt'                 => str_pad($post['rt'] ?? '0', 3, '0', STR_PAD_LEFT),
            'rw'                 => str_pad($post['rw'] ?? '0', 3, '0', STR_PAD_LEFT),
            'kode_desa'          => $kodeDesa,
            'status_kepesertaan' => 'Aktif',
            'alasan_nonaktif'    => null,
            'periode_sinkron_terakhir' => date('Y-m'),
            'updated_by'         => $userId
        ];

        try {
            if ($pbiId) {
                // Jika sudah ada, update
                $this->pbiModel->update($pbiId, $dataPbi);
            } else {
                // Jika baru, insert
                $dataPbi['created_by'] = $userId;
                $this->pbiModel->insert($dataPbi);
            }
            return $this->response->setJSON(['status' => true, 'message' => 'Data PBI berhasil ditambahkan!']);
        } catch (\Exception $e) {
            // Jika masih error, pesan aslinya akan muncul ke layar (Bukan 500 lagi)
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal simpan ke database: ' . $e->getMessage()]);
        }
    }

    public function proses_nonaktif()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $id = $this->request->getPost('id');
        $alasan = $this->request->getPost('alasan');
        $userId = session()->get('id_user') ?? session()->get('id') ?? 1;

        if (empty($id) || empty($alasan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap.']);
        }

        $dataUpdate = [
            'status_kepesertaan' => 'Non-Aktif',
            'alasan_nonaktif'    => $alasan,
            'tanggal_nonaktif'   => date('Y-m-d'),
            'dinonaktifkan_oleh' => $userId
        ];

        $this->pbiModel->update($id, $dataUpdate);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Data PBI berhasil dinonaktifkan.'
        ]);
    }

    // =========================================================================
    // 📥 IMPORT EXCEL KHUSUS DATA NON-AKTIF DARI SIKS-NG
    // Format Kolom: A:No | B:NIK | C:No KK | D:Nama | E:Prov | F:Kab | G:Kec | H:Desa | I:Alamat | J:Alasan
    // =========================================================================
    public function import_nonaktif()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses tidak diizinkan']);
        }

        // 🔐 Proteksi Role & Kepemilikan Wilayah (Hanya Role 1, 2, 3 / Admin & Operator Desa)
        $userInfo = $this->AuthModel->getUserId();
        $roleId   = (int) ($userInfo['role_id'] ?? session()->get('role_id') ?? 99);
        $kodeDesa = $userInfo['kode_desa'] ?? session()->get('kode_desa');
        $userId   = $userInfo['id_user'] ?? session()->get('id_user') ?? session()->get('id') ?? 1;

        if ($roleId >= 4 || empty($kodeDesa)) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Akses ditolak! Anda tidak memiliki wewenang untuk mengimpor data.'
            ]);
        }

        // 🛡️ Validasi File Upload Ketat (Ekstensi, MIME Type, & Ukuran Maks 5MB)
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File Excel tidak valid atau rusak.']);
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format file harus .xls atau .xlsx!']);
        }

        if ($file->getSizeByUnit('mb') > 5) {
            return $this->response->setJSON(['status' => false, 'message' => 'Ukuran file maksimal 5 MB!']);
        }

        $db      = \Config\Database::connect();
        $periode = date('Y-m');
        $today   = date('Y-m-d');
        $now     = date('Y-m-d H:i:s');

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet()->toArray();

            if (count($sheet) <= 1) {
                return $this->response->setJSON(['status' => false, 'message' => 'File Excel kosong atau hanya berisi header.']);
            }

            // 1️⃣ OPTIMASI: Cache Data Warga (ART) khusus di Desa pengguna yang login
            $artData = $db->table('dtsen_art a')
                ->select('a.nik, a.id_art, k.no_kk, k.alamat as kampung_db, rt.rt, rt.rw')
                ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->where('rt.kode_desa', $kodeDesa)
                ->where('a.deleted_at', null)
                ->get()->getResultArray();

            $mapArt = [];
            foreach ($artData as $art) {
                $mapArt[$art['nik']] = $art;
            }

            // 2️⃣ OPTIMASI & KEAMANAN OBJECT-LEVEL: Cache Data PBI Existing milik Desa ini (Ambil ID, NIK, & Status)
            $pbiData = $db->table('pbi_master_data')
                ->select('id, nik, status_kepesertaan')
                ->where('kode_desa', $kodeDesa)
                ->get()->getResultArray();

            $mapPbi = [];
            foreach ($pbiData as $p) {
                $mapPbi[$p['nik']] = $p; // Simpan id dan status_kepesertaan
            }

            $dataInsert  = [];
            $dataUpdate  = [];
            $nikList     = [];
            $jmlSkip     = 0; // Penghitung data yang sudah Non-Aktif sebelumnya

            for ($i = 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];

                // Bersihkan NIK & No KK dari karakter non-angka (antisipasi tanda petik/spasi dari Excel)
                $nik  = preg_replace('/\D/', '', trim((string)($row[1] ?? '')));
                $noKk = preg_replace('/\D/', '', trim((string)($row[2] ?? '')));
                $nama = strtoupper(trim(strip_tags((string)($row[3] ?? ''))));

                // Validasi NIK wajib 16 digit dan belum diproses di baris sebelumnya
                if (strlen($nik) !== 16 || empty($nama) || in_array($nik, $nikList)) {
                    continue;
                }
                $nikList[] = $nik;

                // 🧠 AMBIL DATA DARI CACHE DATABASE DTSEN
                $dbArt = $mapArt[$nik] ?? null;
                $idArt = $dbArt['id_art'] ?? null;

                // Jika No KK di Excel kosong/tidak valid, gunakan No KK dari DTSEN
                if (strlen($noKk) !== 16 && !empty($dbArt['no_kk'])) {
                    $noKk = $dbArt['no_kk'];
                }

                // Prioritaskan RT/RW dari Database SINDEN
                $rt = !empty($dbArt['rt']) ? str_pad((int)$dbArt['rt'], 3, '0', STR_PAD_LEFT) : '000';
                $rw = !empty($dbArt['rw']) ? str_pad((int)$dbArt['rw'], 3, '0', STR_PAD_LEFT) : '000';

                // 🎯 EKSTRAK ALAMAT (Kolom I / Indeks 8) & REGEX SEBAGAI CADANGAN
                $alamatMentah = trim(strip_tags((string)($row[8] ?? '')));
                $kampung      = !empty($alamatMentah) ? $alamatMentah : ($dbArt['kampung_db'] ?? '-');

                if ($rt === '000' && preg_match('/RT[\.\s\/:]*0*(\d+)/i', $alamatMentah, $matchesRt)) {
                    $rt = str_pad((int)$matchesRt[1], 3, '0', STR_PAD_LEFT);
                }
                if ($rw === '000' && preg_match('/RW[\.\s\/:]*0*(\d+)/i', $alamatMentah, $matchesRw)) {
                    $rw = str_pad((int)$matchesRw[1], 3, '0', STR_PAD_LEFT);
                }

                // 🚦 EKSTRAK ALASAN NON-AKTIF (Kolom J / Indeks 9)
                $alasanMentah   = strtoupper(trim(strip_tags((string)($row[9] ?? ''))));
                $alasanNonAktif = !empty($alasanMentah) ? $alasanMentah : 'DINONAKTIFKAN PUSAT (SIKS-NG)';

                if (isset($mapPbi[$nik])) {
                    // 🛡️ JIKA SUDAH NON-AKTIF SEBELUMNYA (MISAL DARI MANUAL), LANGSUNG SKIP!
                    if ($mapPbi[$nik]['status_kepesertaan'] === 'Non-Aktif') {
                        $jmlSkip++;
                        continue;
                    }

                    // 🔄 JIKA MASIH AKTIF -> UPDATE MENJADI NON-AKTIF BERDASARKAN ID
                    $dataUpdate[] = [
                        'id'                       => $mapPbi[$nik]['id'],
                        'id_art'                   => $idArt,
                        'no_kk'                    => $noKk,
                        'nama'                     => $nama,
                        'kampung'                  => $kampung,
                        'rt'                       => $rt,
                        'rw'                       => $rw,
                        'status_kepesertaan'       => 'Non-Aktif',
                        'alasan_nonaktif'          => $alasanNonAktif,
                        'tanggal_nonaktif'         => $today,
                        'dinonaktifkan_oleh'       => $userId,
                        'periode_sinkron_terakhir' => $periode,
                        'updated_by'               => $userId,
                        'updated_at'               => $now
                    ];
                } else {
                    // ➕ JIKA BELUM ADA DI PBI MASTER DATA -> INSERT LANGSUNG SEBAGAI ARSIP NON-AKTIF
                    $dataInsert[] = [
                        'nik'                      => $nik,
                        'id_art'                   => $idArt,
                        'no_kk'                    => $noKk,
                        'nama'                     => $nama,
                        'jenis_kepesertaan'        => 'PBI APBN',
                        'kampung'                  => $kampung,
                        'rt'                       => $rt,
                        'rw'                       => $rw,
                        'kode_desa'                => $kodeDesa,
                        'status_kepesertaan'       => 'Non-Aktif',
                        'alasan_nonaktif'          => $alasanNonAktif,
                        'tanggal_nonaktif'         => $today,
                        'dinonaktifkan_oleh'       => $userId,
                        'periode_sinkron_terakhir' => $periode,
                        'created_by'               => $userId,
                        'created_at'               => $now,
                        'updated_at'               => $now
                    ];
                }
            }

            if (empty($dataInsert) && empty($dataUpdate)) {
                $msgKosong = ($jmlSkip > 0)
                    ? "Seluruh data dalam file Excel (<b>{$jmlSkip}</b> warga) sudah berstatus Non-Aktif sebelumnya."
                    : "Tidak ada baris data valid yang dapat diimpor. Pastikan NIK terdiri dari 16 digit angka di Kolom B.";

                return $this->response->setJSON([
                    'status'  => ($jmlSkip > 0), // Tetap anggap sukses (hijau) jika memang sudah non-aktif semua
                    'message' => $msgKosong
                ]);
            }

            $db->transBegin();

            if (!empty($dataInsert)) {
                $this->pbiModel->insertBatch($dataInsert);
            }
            if (!empty($dataUpdate)) {
                // Gunakan 'id' sebagai constraint key agar update akurat pada record milik desa ini
                $this->pbiModel->updateBatch($dataUpdate, 'id');
            }

            $db->transCommit();

            $jmlInsert  = count($dataInsert);
            $jmlUpdate  = count($dataUpdate);
            $totalBaris = $jmlInsert + $jmlUpdate;
            $infoSkip   = ($jmlSkip > 0) ? ", {$jmlSkip} Dilewati/Sudah Non-Aktif" : "";

            return $this->response->setJSON([
                'status'  => true,
                'message' => "<b>{$totalBaris}</b> data Non-Aktif berhasil diproses!<br><small>({$jmlUpdate} Dinonaktifkan, {$jmlInsert} Arsip Baru{$infoSkip})</small>"
            ]);
        } catch (\Throwable $e) {
            if ($db->transStatus() === false) {
                $db->transRollback();
            }
            log_message('error', '[PBI import_nonaktif] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat memproses file Excel. Pastikan format kolom sudah sesuai standar SIKS-NG.'
            ]);
        }
    }
}
