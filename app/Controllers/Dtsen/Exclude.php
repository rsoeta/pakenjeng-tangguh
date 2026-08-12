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
        $data = [
            'title' => 'Daftar KPM Exclude (Blacklist)'
        ];

        return view('dtsen/exclude/index', $data);
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

        // 3. 🔍 PENCARIAN GLOBAL
        if (!empty($search)) {
            $builder->groupStart()
                ->like('m.nama', $search)
                ->orLike('m.nik', $search)
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
                $builder->orderBy('m.id_exclude', 'DESC');
            }
        } else {
            $builder->orderBy('m.id_exclude', 'DESC');
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

            $data[] = [
                $no++,
                $namaWilayah,
                $nikKk,
                $keterangan,
                $dataBank,
                $tgl
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
}
