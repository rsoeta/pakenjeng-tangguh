<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtsen\MonevModel;
use App\Models\Dtks\AuthModel;

class Monev extends BaseController
{
    protected $monevModel;
    protected $authModel;

    public function __construct()
    {
        $this->monevModel = new MonevModel();
        $this->authModel  = new AuthModel();
        // Pastikan PhpSpreadsheet sudah terinstall via Composer
        // composer require phpoffice/phpspreadsheet
    }

    public function index()
    {
        $userInfo = $this->authModel->getUserId();

        $data = [
            'title'      => 'Monitoring dan Evaluasi (MONEV) PKH',
            'user_login' => $userInfo
        ];

        return view('dtsen/monev/index', $data); // Tampilan view-nya nanti kita buat
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 FUNGSI DATATABLES SERVER-SIDE
    |--------------------------------------------------------------------------
    */
    public function datatable()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $post = $this->request->getPost();

        $draw   = (int) ($post['draw'] ?? 1);
        $start  = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $search = $post['search']['value'] ?? '';
        $filterKelengkapan = $post['filter_kelengkapan'] ?? '';
        $filterStatus = $post['filter_status'] ?? '';

        $nikPetugas = session()->get('nik');
        $roleId     = session()->get('role_id');

        // 🚀 THE MAGIC: Gunakan db_connect() agar 100% aman dari Fatal Error CI4
        $db = \Config\Database::connect();

        // 1. Susun Base Builder (Join & Group By)
        $builder = $db->table('dtsen_monev m')
            ->select("
                m.id_monev, 
                m.nama_target, 
                m.nik, 
                m.alamat, 
                m.rt, 
                m.rw, 
                m.status_monev,
                COALESCE(a.nama, m.nama_target) as nama_sinden,
                COALESCE(k.alamat, m.alamat) as alamat_sinden,
                COALESCE(rt.rt, m.rt) as rt_sinden,
                COALESCE(rt.rw, m.rw) as rw_sinden,
                COALESCE(k.foto_rumah, rt.foto_rumah) as foto_rumah,
                COALESCE(k.foto_rumah_dalam, rt.foto_rumah_dalam) as foto_rumah_dalam,
                bk.foto_kpm_kks,
                CASE 
                    WHEN mk.foto_kks IS NOT NULL AND mk.foto_kks != '' THEN mk.foto_kks 
                    ELSE mk.foto_kepemilikan 
                END as foto_kks_final
            ")
            ->join('dtsen_art a', 'a.nik = m.nik AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->join('dtsen_bansos_kks bk', 'bk.nik_kpm = m.nik', 'left')
            ->join('dtsen_master_kks mk', 'mk.nik = m.nik', 'left')
            ->groupBy('m.id_monev'); // Kunci agar tidak double

        // 🚀 PANGGIL VARIABEL USER INFO & WILAYAH
        $userInfo     = $this->authModel->getUserId();
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');

        // 🛡️ GEMBOK WILAYAH KERJA 
        // Hanya eksekusi filter JIKA user memiliki wilayah_tugas spesifik (misal Role 4).
        // Role 3 & 5 yang wilayah_tugas-nya KOSONG akan ter-bypass dan melihat FULL 799 data!
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
                                $builder->orWhere("(CAST(m.rw AS UNSIGNED) = {$rwInt} AND CAST(m.rt AS UNSIGNED) = {$rtInt})");
                            }
                        }
                    } else {
                        $builder->orWhere("CAST(m.rw AS UNSIGNED) = {$rwInt}");
                    }
                }
            }
            $builder->groupEnd();
        }

        // 🔍 FITUR FILTER DROPDOWN
        if ($filterStatus !== '') {
            $builder->where('m.status_monev', $filterStatus);
        }

        if ($filterKelengkapan !== '') {
            // Rumus kelengkapan murni di level database SQL
            $kondisiLengkap = "(bk.foto_kpm_kks IS NOT NULL AND bk.foto_kpm_kks != '') AND 
                               ((mk.foto_kks IS NOT NULL AND mk.foto_kks != '') OR (mk.foto_kepemilikan IS NOT NULL AND mk.foto_kepemilikan != '')) AND 
                               (COALESCE(k.foto_rumah, rt.foto_rumah) IS NOT NULL AND COALESCE(k.foto_rumah, rt.foto_rumah) != '') AND 
                               (COALESCE(k.foto_rumah_dalam, rt.foto_rumah_dalam) IS NOT NULL AND COALESCE(k.foto_rumah_dalam, rt.foto_rumah_dalam) != '')";

            if ($filterKelengkapan === '1') {
                $builder->where($kondisiLengkap, null, false);
            } else if ($filterKelengkapan === '0') {
                $builder->where("NOT ($kondisiLengkap)", null, false);
            }
        }

        // 4. 🔍 FITUR PENCARIAN GLOBAL (Ditaruh SEBELUM clone & counting)
        if (!empty($search)) {
            $builder->groupStart()
                ->like('m.nama_target', $search)
                ->orLike('m.nik', $search)
                ->groupEnd();
        }

        // 🚀 5. CLONE BUILDER UNTUK MENGHITUNG DATA TERFILTER (Wajib setelah pencarian & filter wilayah)
        $builderClone = clone $builder;
        $recordsFiltered = $builderClone->countAllResults();

        // 6. FITUR PENGURUTAN (SORTING)
        $columnOrder = [
            null,               // 0: No
            'm.nama_target',    // 1: Nama Target
            'm.alamat',         // 2: Alamat
            null,               // 3: Kelengkapan
            'm.status_monev',   // 4: Status
            null                // 5: Aksi
        ];

        if (isset($post['order'])) {
            $colIndex = (int) $post['order'][0]['column'];
            $dir      = $post['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
            if (isset($columnOrder[$colIndex]) && $columnOrder[$colIndex] !== null) {
                $builder->orderBy($columnOrder[$colIndex], $dir);
            } else {
                $builder->orderBy('m.id_monev', 'ASC');
            }
        } else {
            $builder->orderBy('m.id_monev', 'ASC');
        }

        // 7. LIMIT & OFFSET UNTUK PAGINATION
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $results = $builder->get()->getResultArray();

        // 8. HITUNG TOTAL MURNI (Sesuai hak akses wilayah tugas)
        $totalBuilder = $db->table('dtsen_monev m');

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
                                $totalBuilder->orWhere("(CAST(m.rw AS UNSIGNED) = {$rwInt} AND CAST(m.rt AS UNSIGNED) = {$rtInt})");
                            }
                        }
                    } else {
                        $totalBuilder->orWhere("CAST(m.rw AS UNSIGNED) = {$rwInt}");
                    }
                }
            }
            $totalBuilder->groupEnd();
        }

        $recordsTotal = $totalBuilder->countAllResults();

        $data = [];
        $no = $start + 1;

        foreach ($results as $row) {
            // 🔍 DEBUGGING: Cek isi masing-masing foto di log error Laragon (writable/logs)
            log_message('error', 'CEK FOTO NIK ' . $row['nik'] .
                ' | KPM: ' . var_export($row['foto_kpm_kks'], true) .
                ' | KKS: ' . var_export($row['foto_kks_final'], true) .
                ' | R_Depan: ' . var_export($row['foto_rumah'], true) .
                ' | R_Dalam: ' . var_export($row['foto_rumah_dalam'], true));

            // 🧠 Logika Kelengkapan yang lebih toleran (membuang spasi kosong)
            $fotoKpm = trim($row['foto_kpm_kks'] ?? '');
            $fotoKks = trim($row['foto_kks_final'] ?? '');
            $fotoRmh = trim($row['foto_rumah'] ?? '');
            $fotoDlm = trim($row['foto_rumah_dalam'] ?? '');

            $isLengkap = (!empty($fotoKpm) && !empty($fotoKks) && !empty($fotoRmh) && !empty($fotoDlm));

            // 🎯 Render Badge Kelengkapan & Daftar Kekurangan
            if ($isLengkap) {
                $badgeKelengkapan = '<span class="badge bg-primary"><i class="fas fa-check"></i> Lengkap</span>';
            } else {
                $badgeKelengkapan = '<span class="badge bg-danger"><i class="fas fa-times"></i> Belum Lengkap</span>';
                $badgeKelengkapan .= '<div class="mt-1 text-start" style="font-size: 0.75rem; line-height: 1.3;">';

                if (empty($fotoKpm)) {
                    $badgeKelengkapan .= '<span class="text-danger d-block"><i class="fas fa-exclamation-circle me-1"></i>Foto KPM KKS</span>';
                }
                if (empty($fotoKks)) {
                    $badgeKelengkapan .= '<span class="text-danger d-block"><i class="fas fa-exclamation-circle me-1"></i>Fisik KKS</span>';
                }
                if (empty($fotoRmh)) {
                    $badgeKelengkapan .= '<span class="text-danger d-block"><i class="fas fa-exclamation-circle me-1"></i>Rumah Depan</span>';
                }
                if (empty($fotoDlm)) {
                    $badgeKelengkapan .= '<span class="text-danger d-block"><i class="fas fa-exclamation-circle me-1"></i>Rumah Dalam</span>';
                }

                $badgeKelengkapan .= '</div>';
            }

            $badgeStatus = ($row['status_monev'] == 'Selesai')
                ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Selesai</span>'
                : '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu</span>';

            // ... (kode Alamat dan Masking NIK tetap di bawahnya)

            // 🧠 2. Definisi Alamat (Menggabungkan data SINDEN dan Excel)
            $alamatFinal = !empty($row['alamat_sinden']) ? $row['alamat_sinden'] : $row['alamat'];
            $rtFinal = !empty($row['rt_sinden']) ? str_pad($row['rt_sinden'], 3, '0', STR_PAD_LEFT) : $row['rt'];
            $rwFinal = !empty($row['rw_sinden']) ? str_pad($row['rw_sinden'], 3, '0', STR_PAD_LEFT) : $row['rw'];

            $alamatStr = $alamatFinal . '<br><small class="text-muted">RT ' . $rtFinal . ' / RW ' . $rwFinal . '</small>';

            // 🛡️ MASKING NIK (Interaktif: Tampil asli saat di-hover)
            $realNik = esc($row['nik']);
            $maskedNik = substr($realNik, 0, 6) . '******' . substr($realNik, -4);

            // HTML Nama Target, Masked NIK interaktif, dan Tombol Copy
            $namaDanNik = '
                <b>' . esc($row['nama_target']) . '</b><br>
                <div class="d-flex align-items-center mt-1">
                    <small class="text-muted mb-0 me-2" 
                           style="cursor: pointer; transition: all 0.2s ease-in-out;" 
                           data-masked="NIK: ' . $maskedNik . '" 
                           data-real="NIK: ' . $realNik . '"
                           onmouseover="this.innerText = this.getAttribute(\'data-real\'); this.classList.remove(\'text-muted\'); this.classList.add(\'text-primary\', \'fw-bold\');"
                           onmouseout="this.innerText = this.getAttribute(\'data-masked\'); this.classList.remove(\'text-primary\', \'fw-bold\'); this.classList.add(\'text-muted\');">
                        NIK: ' . $maskedNik . '
                    </small>
                    <button class="btn btn-sm btn-light border py-0 px-1 shadow-sm" onclick="salinNIK(\'' . $realNik . '\')" title="Salin NIK Asli">
                        <i class="fas fa-copy text-primary" style="font-size: 0.85rem;"></i>
                    </button>
                </div>
            ';

            // 🚀 TOMBOL AKSI UTAMA
            $statusBtn = $isLengkap ? '' : 'disabled';
            $btnAksi = '
                <button class="btn btn-sm btn-primary shadow-sm" 
                        onclick="lihatDetailMonev(' . $row['id_monev'] . ')" 
                        title="' . ($isLengkap ? 'Eksekusi Monev' : 'Data belum lengkap') . '"
                        ' . $statusBtn . '>
                    <i class="fas fa-camera"></i> ' . ($isLengkap ? 'Eksekusi' : 'Tunggu') . '
                </button>
            ';

            // 🎯 TOMBOL UBAH NIK (Hanya muncul jika yang login adalah Operator Desa / role_id = 3)
            if ($roleId == 3) {
                $btnAksi .= '
                <button class="btn btn-sm btn-warning shadow-sm ms-1" 
                        onclick="bukaModalUbahNik(' . $row['id_monev'] . ', \'' . $realNik . '\', \'' . esc($row['nama_target']) . '\')" 
                        title="Ubah NIK">
                    <i class="fas fa-edit"></i> Edit
                </button>';
            }

            // 📊 Susunan Kolom DataTables
            $data[] = [
                $no++,
                $namaDanNik,
                $alamatStr,
                $badgeKelengkapan,
                $badgeStatus,
                $btnAksi // Pastikan variabel $btnAksi masuk ke kolom terakhir ini
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 FUNGSI IMPORT EXCEL MONEV PKH
    |--------------------------------------------------------------------------
    */
    public function import_excel()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        // 🔒 Gembok: Blokir role_id >= 4 melakukan import
        if (session()->get('role_id') >= 4) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Akses ditolak! Anda tidak memiliki wewenang untuk mengunggah data Excel.'
            ]);
        }

        $file = $this->request->getFile('file_excel');
        $periode = $this->request->getPost('periode') ?? 'Triwulan 2 ' . date('Y');

        // Validasi File
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File Excel tidak ditemukan atau tidak valid.']);
        }

        $extension = $file->getClientExtension();
        if (!in_array($extension, ['xls', 'xlsx'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format file harus .xls atau .xlsx']);
        }

        try {
            // Load file Excel menggunakan IOFactory
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $sukses = 0;
            $gagal  = 0;
            $nikPetugas = session()->get('nik') ?? 'system';

            $dataInsert = [];

            // Looping data Excel (Mulai dari indeks 1 untuk melewati baris Header/Judul Kolom)
            for ($i = 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];

                // Pemetaan Kolom Excel ke Database
                // Format Excel: No | Nama Target | Provinsi | Kabupaten | Kecamatan | Kelurahan | Alamat | RT | RW | Nama SDM | Status | NIK

                $nik = trim($row[11] ?? ''); // Indeks 11 adalah Kolom NIK (Kolom L)

                // Lewati jika NIK kosong (baris kosong di akhir excel)
                if (empty($nik)) {
                    $gagal++;
                    continue;
                }

                $dataInsert[] = [
                    'nama_target'  => trim($row[1] ?? ''),
                    'provinsi'     => trim($row[2] ?? ''),
                    'kabupaten'    => trim($row[3] ?? ''),
                    'kecamatan'    => trim($row[4] ?? ''),
                    'kelurahan'    => trim($row[5] ?? ''),
                    'alamat'       => trim($row[6] ?? ''),
                    'rt'           => trim($row[7] ?? ''),
                    'rw'           => trim($row[8] ?? ''),
                    'nama_sdm'     => trim($row[9] ?? ''),
                    'status_kpm'   => trim($row[10] ?? ''), // Kolom Status
                    'nik'          => $nik,
                    'periode'      => $periode,
                    'status_monev' => 'Menunggu',
                    'created_by'   => $nikPetugas
                ];

                $sukses++;
            }

            // Eksekusi Insert Batch ke Database jika ada data yang valid
            if (!empty($dataInsert)) {
                // Untuk mencegah duplikasi saat import ulang di periode yang sama,
                // idealnya kita kosongkan dulu data Monev periode tersebut untuk NIK petugas terkait (opsional)
                // $this->monevModel->where('periode', $periode)->where('created_by', $nikPetugas)->delete();

                $this->monevModel->insertBatch($dataInsert);
            }

            return $this->response->setJSON([
                'status'  => true,
                'message' => "Import selesai! <b>{$sukses}</b> KPM berhasil ditambahkan untuk periode {$periode}."
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem saat membaca Excel: ' . $e->getMessage()
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 FUNGSI AMBIL DETAIL DATA & FOTO
    |--------------------------------------------------------------------------
    */
    public function get_detail($id)
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $db = \Config\Database::connect();

        $data = $db->table('dtsen_monev m')
            ->select("
                m.id_monev, 
                m.nama_target, 
                m.nik, 
                m.status_monev,
                a.nama as nama_sinden,
                COALESCE(k.foto_rumah, rt.foto_rumah) as foto_rumah,
                COALESCE(k.foto_rumah_dalam, rt.foto_rumah_dalam) as foto_rumah_dalam,
                bk.foto_kpm_kks,
                CASE 
                    WHEN mk.foto_kks IS NOT NULL AND mk.foto_kks != '' THEN mk.foto_kks 
                    ELSE mk.foto_kepemilikan 
                END as foto_kks_final
            ")

            ->join('dtsen_art a', 'a.nik = m.nik AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->join('dtsen_bansos_kks bk', 'bk.nik_kpm = m.nik', 'left')
            ->join('dtsen_master_kks mk', 'mk.nik = m.nik', 'left')
            ->where('m.id_monev', $id)
            ->get()->getRowArray();

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan di database.']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 FUNGSI TANDAI MONEV SELESAI
    |--------------------------------------------------------------------------
    */
    public function tandai_selesai()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        // 🔒 Gembok Keamanan: Cegah role_id = 4 mengubah status
        if (session()->get('role_id') == 4) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Akses ditolak! Anda tidak memiliki wewenang untuk menandai selesai.'
            ]);
        }

        $id = $this->request->getPost('id_monev');
        $catatan = $this->request->getPost('catatan_pendamping');

        $this->monevModel->update($id, [
            'status_monev'       => 'Selesai',
            'catatan_pendamping' => $catatan,
            'updated_by'         => session()->get('nik')
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Monev berhasil ditandai selesai!']);
    }

    public function search_nik_art()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $term = trim($this->request->getGet('q'));
        $userInfo = $this->authModel->getUserId();
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');
        $db = \Config\Database::connect();

        $builder = $db->table('dtsen_art')
            ->select("dtsen_art.nik, dtsen_art.nama, dtsen_art.hubungan_keluarga AS shdk, dtsen_rt.rw, dtsen_rt.rt")
            ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk AND dtsen_kk.deleted_at IS NULL', 'left')
            ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')
            ->where('dtsen_art.deleted_at IS NULL')
            ->groupStart()
            ->like('dtsen_art.nik', $term)
            ->orLike('dtsen_art.nama', $term)
            ->groupEnd()
            ->groupBy('dtsen_art.nik')
            ->limit(10);

        if (!empty($wilayahTugas)) {
            $wilayahPairs = [];
            $blocks = explode('|', $wilayahTugas);
            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);
                foreach (explode(',', $rtList) as $rt) {
                    $rt = trim($rt);
                    if ($rw !== '' && $rt !== '') {
                        $wilayahPairs[] = ['rw' => $rw, 'rt' => $rt];
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
            return [
                'id'    => $row['nik'],
                'text'  => $row['nik'] . ' - ' . strtoupper($row['nama']),
                'nama'  => $row['nama']
            ];
        }, $rows);

        return $this->response->setJSON(['results' => $results]);
    }

    public function update_nik()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        // 🔒 Gembok Hak Akses (Hanya Admin Desa)
        if (session()->get('role_id') != 3) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses ditolak!']);
        }

        $id = $this->request->getPost('id_monev');
        $nikBaru = $this->request->getPost('nik_baru');

        // 🛡️ VALIDASI ANTI GANDA: Cek apakah NIK baru sudah ada di tabel Monev
        $cekDataGanda = $this->monevModel->where('nik', $nikBaru)->first();

        if ($cekDataGanda) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal! NIK tersebut sudah terdaftar di daftar Monev saat ini.'
            ]);
        }

        // 🚀 Eksekusi Update jika aman
        $this->monevModel->update($id, ['nik' => $nikBaru]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'NIK berhasil diperbarui tanpa bentrok data!'
        ]);
    }

    public function tambah_monev()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        // 🔒 Gembok Hak Akses (Hanya untuk Admin/Operator Desa dengan role < 4)
        if (session()->get('role_id') >= 4) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses ditolak!']);
        }

        $nik = $this->request->getPost('nik');
        $namaTarget = $this->request->getPost('nama_target');
        $periode = 'Triwulan 2 ' . date('Y'); // Sesuaikan periode defaultnya

        if (empty($nik)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data NIK tidak valid!']);
        }

        // 🛡️ VALIDASI ANTI GANDA
        $cekDataGanda = $this->monevModel->where('nik', $nik)->first();
        if ($cekDataGanda) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal! KPM tersebut sudah terdaftar di Monev saat ini.'
            ]);
        }

        // 🚀 Eksekusi Insert Data
        // Catatan: Karena di fungsi datatable() kita sudah memakai COALESCE(k.alamat, m.alamat), 
        // kita tidak perlu mengisi kolom alamat/rt/rw di tabel dtsen_monev.
        // Sistem otomatis akan menarik alamat/RT/RW terbaru dari dtsen_art (master).
        $dataInsert = [
            'nik'          => $nik,
            'nama_target'  => $namaTarget,
            'periode'      => $periode,
            'status_monev' => 'Menunggu',
            'created_by'   => session()->get('nik') ?? 'system'
        ];

        $this->monevModel->insert($dataInsert);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data target Monev berhasil ditambahkan!'
        ]);
    }
}
