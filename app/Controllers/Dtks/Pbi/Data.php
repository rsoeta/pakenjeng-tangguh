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
}
