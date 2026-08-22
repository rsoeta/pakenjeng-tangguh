<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtsen\KpmExcludeModel;
use App\Models\Dtks\AuthModel;

class Exclude extends BaseController
{
    protected $excludeModel;
    protected $authModel;

    public function __construct()
    {
        $this->excludeModel = new KpmExcludeModel();
        $this->authModel  = new AuthModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $kodeDesa = session()->get('kode_desa'); // 🛡️ Ambil kode desa dari sesi

        // Ambil daftar RW unik untuk dropdown filter (Dibatasi per Desa)
        $rwList = $db->table('dtsen_rt')
            ->select('rw')
            ->where('kode_desa', $kodeDesa)
            ->distinct()
            ->orderBy('CAST(rw AS UNSIGNED)', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title'  => 'Daftar KPM Exclude',
            'rwList' => $rwList
        ];

        return view('dtsen/exclude/index', $data);
    }

    public function get_rt_by_rw()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $rw = $this->request->getPost('rw');
        $kodeDesa = session()->get('kode_desa'); // 🛡️ Ambil kode desa dari sesi

        $db = \Config\Database::connect();

        // Ambil daftar RT berdasarkan RW (Dibatasi per Desa)
        $rtList = $db->table('dtsen_rt')
            ->select('rt')
            ->where('kode_desa', $kodeDesa)
            ->where("CAST(rw AS UNSIGNED) = ", (int)$rw)
            ->orderBy('CAST(rt AS UNSIGNED)', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON($rtList);
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 FUNGSI DATATABLES SERVER-SIDE (Dengan Gembok Wilayah & Join Master)
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

        $roleId       = session()->get('role_id');
        $userInfo     = $this->authModel->getUserId();
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');

        $db = \Config\Database::connect();

        // 1. Susun Base Builder (Join ke dtsen_art, dtsen_kk, dan dtsen_rt)
        $builder = $db->table('dtsen_kpm_exclude m')
            ->select("
                m.id_exclude, 
                m.nama, 
                m.nik, 
                m.no_kk,
                m.tgl_nonaktif,
                m.keterangan,
                m.bank,
                m.no_rek,
                m.bukti_penutupan,
                a.nama as nama_master,
                rt.rt,
                rt.rw
            ")
            ->join('dtsen_art a', 'a.nik = m.nik AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left');

        // 2. 🛡️ GEMBOK WILAYAH KERJA 
        // Menggunakan kolom rt.rw dan rt.rt karena tabel dtsen_kpm_exclude tidak punya RT/RW sendiri
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
                                $builder->orWhere("(CAST(rt.rw AS UNSIGNED) = {$rwInt} AND CAST(rt.rt AS UNSIGNED) = {$rtInt})");
                            }
                        }
                    } else {
                        $builder->orWhere("CAST(rt.rw AS UNSIGNED) = {$rwInt}");
                    }
                }
            }
            $builder->groupEnd();
        }

        // ==========================================
        // 🎯 TANGKAP FILTER DROPDOWN RW & RT
        // ==========================================
        $filterRw = $this->request->getPost('filter_rw');
        $filterRt = $this->request->getPost('filter_rt');

        if (!empty($filterRw)) {
            $builder->where("CAST(rt.rw AS UNSIGNED) = ", (int)$filterRw);
        }
        if (!empty($filterRt)) {
            $builder->where("CAST(rt.rt AS UNSIGNED) = ", (int)$filterRt);
        }

        // 3. 🔍 PENCARIAN GLOBAL
        if (!empty($search)) {
            $builder->groupStart()
                ->like('m.nama', $search)
                ->orLike('m.nik', $search)
                ->orLike('m.no_kk', $search) // 👈 TAMBAHKAN BARIS INI
                ->orLike('m.keterangan', $search)
                ->groupEnd();
        }

        // 4. CLONE UNTUK MENGHITUNG DATA TERFILTER
        $builderClone = clone $builder;
        $recordsFiltered = $builderClone->countAllResults();

        // 5. PENGURUTAN (SORTING)
        $columnOrder = [null, 'm.nama', 'm.nik', 'm.keterangan', 'm.bank', 'm.tgl_nonaktif'];
        if (isset($post['order'])) {
            $colIndex = (int) $post['order'][0]['column'];
            $dir      = $post['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
            if (isset($columnOrder[$colIndex]) && $columnOrder[$colIndex] !== null) {
                $builder->orderBy($columnOrder[$colIndex], $dir);
            } else {
                $builder->orderBy('m.id_exclude', 'DESC'); // 👈 Kembalikan ke id_exclude
            }
        } else {
            $builder->orderBy('m.id_exclude', 'DESC'); // 👈 Kembalikan ke id_exclude
        }

        // 6. LIMIT (PAGINATION)
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $results = $builder->get()->getResultArray();

        // 7. HITUNG TOTAL MURNI (Tetap wajib di-JOIN agar filter wilayah berfungsi saat dihitung totalnya)
        $totalBuilder = $db->table('dtsen_kpm_exclude m')
            ->join('dtsen_art a', 'a.nik = m.nik AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left');

        if ($roleId <= 5 && !empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $totalBuilder->groupStart();
            foreach ($blocks as $block) {
                // (Logika filter wilayah sama persis dengan di atas)
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rwInt = (int) trim($rw);
                if ($rwInt > 0) {
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        foreach ($rts as $rt) {
                            $rtInt = (int) trim($rt);
                            if ($rtInt > 0) {
                                $totalBuilder->orWhere("(CAST(rt.rw AS UNSIGNED) = {$rwInt} AND CAST(rt.rt AS UNSIGNED) = {$rtInt})");
                            }
                        }
                    } else {
                        $totalBuilder->orWhere("CAST(rt.rw AS UNSIGNED) = {$rwInt}");
                    }
                }
            }
            $totalBuilder->groupEnd();
        }

        $recordsTotal = $totalBuilder->countAllResults();

        // 8. 🎨 RENDER HTML ROW DENGAN EFEK ANTI-LEAK
        $data = [];
        $no = $start + 1;

        foreach ($results as $row) {
            // Jika ada di SINDEN ambil nama master, jika tidak pakai dari Excel
            $namaFinal = !empty($row['nama_master']) ? $row['nama_master'] : $row['nama'];
            $rtStr = !empty($row['rt']) ? str_pad($row['rt'], 3, '0', STR_PAD_LEFT) : '<i class="text-danger">Diluar Desa</i>';
            $rwStr = !empty($row['rw']) ? str_pad($row['rw'], 3, '0', STR_PAD_LEFT) : '<i class="text-danger">Diluar Desa</i>';

            $namaWilayah = '<b>' . esc($namaFinal) . '</b><br>';
            $namaWilayah .= '<small class="text-muted">RT ' . $rtStr . ' / RW ' . $rwStr . '</small>';

            // 🛡️ BUNGKUS DENGAN CLASS "data-rahasia" AGAR NGE-BLUR (Dilengkapi Tombol Salin)
            $realNik = esc($row['nik']);
            $realKk  = esc($row['no_kk'] ?? '-');

            $nikKk = '<div class="data-rahasia">';

            // Baris NIK + Tombol Copy
            $nikKk .= '<div class="d-flex align-items-center mb-1">';
            $nikKk .= '<strong>NIK:</strong> <span class="ms-2 me-2">' . $realNik . '</span>';
            $nikKk .= '<button class="btn btn-sm btn-light border py-0 px-1 shadow-sm" onclick="salinTeksRahasia(\'' . $realNik . '\', \'NIK\')" title="Salin NIK"><i class="fas fa-copy text-primary" style="font-size: 0.85rem;"></i></button>';
            $nikKk .= '</div>';

            // Baris KK + Tombol Copy (Hanya jika KK tidak kosong)
            $nikKk .= '<div class="d-flex align-items-center">';
            if ($realKk !== '-' && !empty($realKk)) {
                $nikKk .= '<strong>KK:</strong> <span class="ms-2 me-2">' . $realKk . '</span>';
                $nikKk .= '<button class="btn btn-sm btn-light border py-0 px-1 shadow-sm" onclick="salinTeksRahasia(\'' . $realKk . '\', \'No. KK\')" title="Salin KK"><i class="fas fa-copy text-success" style="font-size: 0.85rem;"></i></button>';
            } else {
                $nikKk .= '<strong>KK:</strong> <span class="ms-2">-</span>';
            }
            $nikKk .= '</div>';

            $nikKk .= '</div>';

            $keterangan = '<div class="data-rahasia text-danger fw-bold">';
            $keterangan .= esc($row['keterangan'] ?? '-');
            $keterangan .= '</div>';

            // 🔄 MANIPULASI STRING MULTI-BANK & REKENING
            $arrBank = array_map('trim', explode(';', $row['bank'] ?? ''));
            $arrRek  = array_map('trim', explode(';', $row['no_rek'] ?? ''));

            $listBank = [];
            $limit = max(count($arrBank), count($arrRek)); // Ambil jumlah array terbanyak untuk loop

            for ($i = 0; $i < $limit; $i++) {
                $namaBank = !empty($arrBank[$i]) ? esc($arrBank[$i]) : 'Bank ?';
                $noRek    = !empty($arrRek[$i]) ? esc($arrRek[$i]) : '-';

                if ($namaBank !== 'Bank ?' || $noRek !== '-') {
                    $listBank[] = "<strong>{$namaBank}:</strong> {$noRek}";
                }
            }

            // Gabungkan array dengan tag <br> agar rapi ke bawah (bisa ganti dengan ', ' jika ingin menyamping)
            $stringBankFinal = !empty($listBank) ? implode('<br>', $listBank) : '-';

            // 🛡️ BUNGKUS DENGAN CLASS "data-rahasia" AGAR NGE-BLUR
            $dataBank = '<div class="data-rahasia" style="line-height: 1.4; font-size: 0.85rem;">';
            $dataBank .= $stringBankFinal;
            $dataBank .= '</div>';

            $tgl = !empty($row['tgl_nonaktif']) ? date('d/m/Y', strtotime($row['tgl_nonaktif'])) : '-';

            $btnAksi = '-';

            // 🔐 Buka akses untuk Pentri (4) dan Petugas Entri (5)
            if ($roleId <= 5) {
                $btnAksi = '<div class="d-flex gap-1 justify-content-center">';

                // 1. Tombol PROSES (Gambar Gear/Setting)
                $btnAksi .= '
                    <button type="button" class="btn btn-sm btn-outline-warning shadow-sm px-2" onclick="cetakSuratJudol(\'' . $row['id_exclude'] . '\', \'' . $row['nik'] . '\', \'' . esc($row['nama']) . '\')" title="Proses Surat & Upload Bukti">
                        <i class="fas fa-cogs"></i>
                    </button>
                ';

                // 2. DETEKSI & TAMPILKAN TOMBOL DOWNLOAD SURAT WORD
                $filePernyataan = 'uploads/surat_judol/Pernyataan_Judol_' . $row['nik'] . '.docx';
                $fileBA         = 'uploads/surat_judol/BA_Klarifikasi_' . $row['nik'] . '.docx';

                if (file_exists(FCPATH . $filePernyataan)) {
                    $btnAksi .= '<a href="' . base_url($filePernyataan) . '" target="_blank" class="btn btn-sm btn-primary shadow-sm px-2" title="Unduh Surat Pernyataan"><i class="fas fa-file-word"></i></a>';
                } elseif (file_exists(FCPATH . $fileBA)) {
                    $btnAksi .= '<a href="' . base_url($fileBA) . '" target="_blank" class="btn btn-sm btn-primary shadow-sm px-2" title="Unduh Surat BA"><i class="fas fa-file-word"></i></a>';
                }

                // 3. TAMPILKAN TOMBOL DOWNLOAD BUKTI ASLI (Bisa Lebih dari 1)
                $fileBukti = $row['bukti_penutupan'] ?? null;
                if (!empty($fileBukti)) {
                    $arrBukti = explode(',', $fileBukti);
                    foreach ($arrBukti as $idx => $fb) {
                        $btnAksi .= '<a href="' . base_url('uploads/bukti_judol/' . trim($fb)) . '" target="_blank" class="btn btn-sm btn-success shadow-sm px-2" title="Lihat Foto Bukti ' . ($idx + 1) . '"><i class="fas fa-file-image"></i></a>';
                    }
                }

                // 4. 🚀 TOMBOL HAPUS (Eksklusif Khusus Role < 4 / Operator ke atas)
                if ($roleId < 4) {
                    $btnAksi .= '
                        <button type="button" class="btn btn-sm btn-danger shadow-sm px-2" onclick="hapusExclude(\'' . $row['id_exclude'] . '\', \'' . esc($row['nama']) . '\')" title="Hapus Data KPM">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    ';
                }

                $btnAksi .= '</div>';
            }

            $data[] = [
                $no++,
                $namaWilayah,
                $nikKk,
                $keterangan,
                $dataBank,
                $tgl,
                $btnAksi
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
    | 🚀 FUNGSI IMPORT EXCEL DATA EXCLUDE (SIKS-NG)
    |--------------------------------------------------------------------------
    */
    public function import_excel()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        // 🔒 Gembok Hak Akses (Hanya Admin / Operator Desa)
        if (session()->get('role_id') >= 4) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Akses ditolak! Anda tidak memiliki wewenang.'
            ]);
        }

        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File Excel tidak valid.']);
        }

        $extension = $file->getClientExtension();
        if (!in_array($extension, ['xls', 'xlsx'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format file wajib .xls atau .xlsx']);
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $sukses = 0;
            $gagal  = 0;
            $nikPetugas = session()->get('nik') ?? 'system';
            $dataInsert = [];
            $nikList = []; // Array sementara untuk mencegah duplikat di dalam file Excel yang sama

            // Looping baris (Mulai dari indeks 1 untuk melewati Header)
            for ($i = 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];

                // Urutan: 0:NIK | 1:Nama | 2:NO KK | 3:Tgl Nonaktif | 4:Keterangan | 5:Bank | 6:No Rek | 7:Desil
                $nik = trim($row[0] ?? '');

                // Lewati jika NIK kosong atau sudah terdeteksi di baris sebelumnya
                if (empty($nik) || in_array($nik, $nikList)) {
                    $gagal++;
                    continue;
                }

                // Validasi Anti-Ganda ke Database
                $cekDataGanda = $this->excludeModel->where('nik', $nik)->first();
                if ($cekDataGanda) {
                    $gagal++;
                    continue; // Skip jika sudah ada di SINDEN
                }

                $nikList[] = $nik;

                // 📅 KONVERSI TANGGAL ANTI-HALU (Mendeteksi format Indonesia & Amerika secara otomatis)
                $tglMentah = trim($row[3] ?? '');
                $tglNonaktif = null;

                if (!empty($tglMentah)) {
                    if (is_numeric($tglMentah)) {
                        // Jika terbaca sebagai Serial Number bawaan Excel (misal: 46161)
                        $tglNonaktif = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglMentah)->format('Y-m-d');
                    } else {
                        // Pisahkan angka berdasarkan slash (/) atau dash (-)
                        $separator = strpos($tglMentah, '/') !== false ? '/' : '-';
                        $parts = explode($separator, $tglMentah);

                        if (count($parts) === 3) {
                            $p1 = (int) $parts[0];
                            $p2 = (int) $parts[1];
                            $p3 = (int) $parts[2];

                            $thn = $p3;
                            $bln = 0;
                            $tgl = 0;

                            // 1. Cek pola YYYY-MM-DD
                            if ($p1 > 1000) {
                                $thn = $p1;
                                $bln = $p2;
                                $tgl = $p3;
                            }
                            // 2. Cek pola MM/DD/YYYY (Akibat konversi otomatis PhpSpreadsheet gaya Amerika)
                            elseif ($p1 <= 12 && $p2 > 12) {
                                $bln = $p1;
                                $tgl = $p2;
                            }
                            // 3. Cek pola DD/MM/YYYY (Teks murni gaya Indonesia dari Excel)
                            elseif ($p1 > 12 && $p2 <= 12) {
                                $tgl = $p1;
                                $bln = $p2;
                            }
                            // 4. Jika ambigu (misal 05/07/2026), prioritaskan gaya Indonesia (DD/MM/YYYY)
                            else {
                                $tgl = $p1;
                                $bln = $p2;
                            }

                            // Cegah penulisan tahun 2 digit (misal 26 menjadi 2026)
                            if ($thn < 100) {
                                $thn += 2000;
                            }

                            // Susun ulang ke format baku Database (YYYY-MM-DD)
                            if ($tgl > 0 && $bln > 0 && $thn > 1000) {
                                $tglNonaktif = sprintf('%04d-%02d-%02d', $thn, $bln, $tgl);
                            }
                        }

                        // Jurus pamungkas jika teks benar-benar di luar nalar
                        if (empty($tglNonaktif)) {
                            $time = strtotime(str_replace('/', '-', $tglMentah));
                            if ($time) {
                                $tglNonaktif = date('Y-m-d', $time);
                            }
                        }
                    }
                }

                $dataInsert[] = [
                    'nik'          => $nik,
                    'nama'         => trim($row[1] ?? ''),
                    'no_kk'        => trim($row[2] ?? ''),
                    'tgl_nonaktif' => $tglNonaktif,
                    'keterangan'   => trim($row[4] ?? ''),
                    'bank'         => trim($row[5] ?? ''),
                    'no_rek'       => trim($row[6] ?? ''),
                    'desil'        => (int) trim($row[7] ?? 0),
                    'created_by'   => $nikPetugas
                ];

                $sukses++;
            }

            // Eksekusi Insert
            if (!empty($dataInsert)) {
                $this->excludeModel->insertBatch($dataInsert);
            }

            return $this->response->setJSON([
                'status'  => true,
                'message' => "Selesai! <b>{$sukses}</b> data berhasil diunggah, {$gagal} data dilewati (ganda/kosong)."
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Sistem gagal membaca Excel: ' . $e->getMessage()
            ]);
        }
    }

    // 🔍 FUNGSI PENCARIAN NIK/NAMA UNTUK SELECT2 (Plus Ambil Data Desil)
    public function search_nik_art()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $q = $this->request->getGet('q');
        $kodeDesa = session()->get('kode_desa');
        $db = \Config\Database::connect();

        $builder = $db->table('dtsen_art a')
            ->select('a.nik, a.nama, k.no_kk, se.kategori_desil') // 👈 Tambahkan se.kategori_desil
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = k.id_kk', 'left') // 👈 Join ke Sosial Ekonomi
            ->where('rt.kode_desa', $kodeDesa)
            ->where('a.deleted_at', null)
            ->groupStart()
            ->like('a.nik', $q)
            ->orLike('a.nama', $q)
            ->groupEnd()
            ->limit(20);

        $results = $builder->get()->getResultArray();

        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id'    => $row['nik'],
                'text'  => $row['nik'] . ' - ' . $row['nama'],
                'nama'  => $row['nama'],
                'no_kk' => $row['no_kk'],
                'desil' => $row['kategori_desil'] // 👈 Selipkan data desil ke JSON
            ];
        }

        return $this->response->setJSON(['results' => $data]);
    }

    // 🚀 FUNGSI INSERT TAMBAH MANUAL
    public function tambah_exclude()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        if (session()->get('role_id') >= 4) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses ditolak!']);
        }

        $nik = $this->request->getPost('nik');
        if (empty($nik)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data NIK tidak valid!']);
        }

        // 🛡️ Validasi Anti Ganda
        $cekData = $this->excludeModel->where('nik', $nik)->first();
        if ($cekData) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal! KPM tersebut sudah ada di daftar Exclude.'
            ]);
        }

        $dataInsert = [
            'nik'          => $nik,
            'nama'         => $this->request->getPost('nama'),
            'no_kk'        => $this->request->getPost('no_kk'),
            'tgl_nonaktif' => $this->request->getPost('tgl_nonaktif'),
            'keterangan'   => $this->request->getPost('keterangan'),
            'bank'         => $this->request->getPost('bank'),
            'no_rek'       => $this->request->getPost('no_rek'),
            'desil'        => (int) $this->request->getPost('desil'),
            'created_by'   => session()->get('nik') ?? 'system'
        ];

        $this->excludeModel->insert($dataInsert);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data target Exclude berhasil ditambahkan secara manual!'
        ]);
    }

    // 📅 Helper Tanggal Gaya Birokrasi (Contoh: "Senin tanggal 8 bulan Agustus tahun 2026")
    private function format_tanggal_ba()
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return $hari[date('w')] . ' tanggal ' . date('j') . ' bulan ' . $bulan[date('n')] . ' tahun ' . date('Y');
    }

    // 🚀 SUPER FUNGSI: PROSES UPLOAD BUKTI & GENERATE WORD (DI SIMPAN KE SERVER)
    public function proses_surat()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $id = $this->request->getPost('id');
        $jenis = $this->request->getPost('jenis');
        $db = \Config\Database::connect();

        $kpm = $db->table('dtsen_kpm_exclude e')
            ->select('e.*, rt.rt, rt.rw, k.alamat as kampung')
            ->join('dtsen_art a', 'a.nik = e.nik', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->where('e.id_exclude', $id)
            ->get()->getRowArray();

        if (!$kpm) return $this->response->setJSON(['status' => false, 'message' => 'Data KPM tidak ditemukan.']);

        $alamat = ($kpm['kampung'] ?? '-') . ' RT ' . str_pad($kpm['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($kpm['rw'] ?? '0', 3, '0', STR_PAD_LEFT);

        $templatePath = WRITEPATH . 'uploads/templates/';
        $suratDir = FCPATH . 'uploads/surat_judol/';
        if (!is_dir($suratDir)) mkdir($suratDir, 0777, true); // Folder untuk menyimpan hasil Word

        $namaFile = ($jenis === 'ba') ? 'BA_Klarifikasi_' . $kpm['nik'] . '.docx' : 'Pernyataan_Judol_' . $kpm['nik'] . '.docx';
        $fileTemplate = ($jenis === 'ba') ? $templatePath . 'ba_klarifikasi.docx' : $templatePath . 'surat_pernyataan.docx';

        if (!file_exists($fileTemplate)) {
            return $this->response->setJSON(['status' => false, 'message' => 'File Template Word belum diunggah di sistem.']);
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($fileTemplate);

        $templateProcessor->setValue('tgl_birokrasi', $this->format_tanggal_ba());
        $templateProcessor->setValue('tgl_sekarang', date('d-m-Y'));
        $templateProcessor->setValue('nama_kpm', $kpm['nama']);
        $templateProcessor->setValue('nik_kpm', $kpm['nik']);
        $templateProcessor->setValue('no_kk', $kpm['no_kk'] ?? '-');
        $templateProcessor->setValue('alamat', $alamat);

        $newName = $kpm['bukti_penutupan'] ?? '';

        if ($jenis === 'pernyataan') {
            // 📸 1. PROSES UPLOAD FOTO BUKTI (BISA LEBIH DARI 1)
            $files = $this->request->getFileMultiple('file_bukti');
            $uploadedFiles = [];

            if ($files) {
                $pathBukti = FCPATH . 'uploads/bukti_judol/';
                if (!is_dir($pathBukti)) mkdir($pathBukti, 0777, true);

                foreach ($files as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        if ($file->getSizeByUnit('mb') > 5) return $this->response->setJSON(['status' => false, 'message' => 'Ukuran salah satu file melebih 5MB.']);

                        $namaRandom = $file->getRandomName();
                        $file->move($pathBukti, $namaRandom);
                        $uploadedFiles[] = $namaRandom; // Simpan nama file ke array
                    }
                }

                // Gabungkan semua nama file dengan koma
                if (!empty($uploadedFiles)) {
                    $newName = implode(',', $uploadedFiles);
                    $db->table('dtsen_kpm_exclude')->where('id_exclude', $id)->update([
                        'bukti_penutupan' => $newName
                    ]);
                }
            }

            // (Kode Injeksi Variabel Pernyataan & Tabel Rekening tetap sama...)
            $templateProcessor->setValue('nama_pelaku', $this->request->getPost('nama_pelaku'));
            $templateProcessor->setValue('nik_pelaku', $this->request->getPost('nik_pelaku'));

            $arrBank = array_map('trim', array_filter(preg_split('/[,;]+/', $kpm['bank'] ?? '')));
            $arrRek  = array_map('trim', array_filter(preg_split('/[,;]+/', $kpm['no_rek'] ?? '')));
            if (empty($arrBank)) {
                $arrBank = ['-'];
                $arrRek = ['-'];
            }

            $jmlData = max(count($arrBank), count($arrRek));
            $dataRekening = [];
            for ($i = 0; $i < $jmlData; $i++) {
                $dataRekening[] = ['nourut' => $i + 1, 'namabank' => $arrBank[$i] ?? '-', 'norek' => $arrRek[$i] ?? '-'];
            }
            $templateProcessor->cloneRowAndSetValues('nourut', $dataRekening);

            // 🖼️ 3. TEMPEL GAMBAR BUKTI KE LAMPIRAN (Halaman 3) - DINAMIS
            $fileBukti = $newName ?? '';
            $arrBukti  = array_filter(explode(',', $fileBukti)); // Pecah pakai koma

            if (!empty($arrBukti)) {
                // Gandakan blok di Word sesuai jumlah foto
                $templateProcessor->cloneBlock('block_bukti', count($arrBukti), true, true);

                $i = 1;
                foreach ($arrBukti as $bkt) {
                    $pathBuktiFull = FCPATH . 'uploads/bukti_judol/' . trim($bkt);
                    if (file_exists($pathBuktiFull)) {
                        $ext = strtolower(pathinfo($pathBuktiFull, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                            $templateProcessor->setImageValue("bukti_foto#" . $i, [
                                'path'   => $pathBuktiFull,
                                'width'  => 500,
                                'height' => 700,
                                'ratio'  => true
                            ]);
                        } else {
                            $templateProcessor->setValue("bukti_foto#" . $i, '(Bukti ke-' . $i . ' berupa file PDF, silakan periksa berkas asli)');
                        }
                    } else {
                        $templateProcessor->setValue("bukti_foto#" . $i, '(File bukti tidak ditemukan)');
                    }
                    $i++;
                }
            } else {
                // Jika tidak ada bukti sama sekali
                $templateProcessor->cloneBlock('block_bukti', 1, true, true);
                $templateProcessor->setValue('bukti_foto#1', '(Belum ada bukti yang diunggah)');
            }
        }

        // 💾 SIMPAN WORD LANGSUNG KE SERVER (Tidak download otomatis)
        $templateProcessor->saveAs($suratDir . $namaFile);

        return $this->response->setJSON(['status' => true, 'message' => 'Surat dan Bukti telah berhasil diracik dan disimpan di Server.']);
    }

    // 🗑️ SUPER FUNGSI HAPUS: Hapus Data + Bersihkan File Server
    public function delete()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $id = $this->request->getPost('id');
        $db = \Config\Database::connect();

        // Cari data yang akan dihapus
        $kpm = $db->table('dtsen_kpm_exclude')->where('id_exclude', $id)->get()->getRowArray();
        if (!$kpm) return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan!']);

        // 🧹 1. HAPUS FILE FOTO BUKTI (Semua File Sekaligus)
        $fileBukti = $kpm['bukti_penutupan'] ?? '';
        if (!empty($fileBukti)) {
            $arrBukti = explode(',', $fileBukti);
            foreach ($arrBukti as $fb) {
                $path = FCPATH . 'uploads/bukti_judol/' . trim($fb);
                if (file_exists($path)) unlink($path);
            }
        }

        // 🧹 2. HAPUS FILE WORD SURAT PERNYATAAN / BA (Jika Ada)
        $filePernyataan = FCPATH . 'uploads/surat_judol/Pernyataan_Judol_' . $kpm['nik'] . '.docx';
        $fileBA         = FCPATH . 'uploads/surat_judol/BA_Klarifikasi_' . $kpm['nik'] . '.docx';

        if (file_exists($filePernyataan)) unlink($filePernyataan);
        if (file_exists($fileBA)) unlink($fileBA);

        // 💥 3. HAPUS DATA DARI DATABASE
        $hapus = $db->table('dtsen_kpm_exclude')->where('id_exclude', $id)->delete();

        if ($hapus) {
            return $this->response->setJSON(['status' => true, 'message' => 'Data KPM beserta file lampirannya berhasil dibumihanguskan.']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus data dari server.']);
        }
    }
}
