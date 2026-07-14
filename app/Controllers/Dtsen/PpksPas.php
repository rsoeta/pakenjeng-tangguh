<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtsen\PpksPasModel;
use App\Models\Dtks\AuthModel;
use App\Models\GenModel; // 🚀 Panggil GenModel

class PpksPas extends BaseController
{
    protected $db;
    protected $ppksPasModel;
    protected $authModel;
    protected $genModel; // 🚀 Deklarasikan variabel

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->ppksPasModel = new PpksPasModel();
        $this->authModel = new AuthModel();
        $this->genModel = new GenModel(); // 🚀 Inisialisasi GenModel
    }

    public function index()
    {
        $kategoriPemkab = [
            ['id' => 8,  'nama_gform' => 'Lansia Terlantar'],
            ['id' => 9,  'nama_gform' => 'Disabilitas Terlantar'],
            ['id' => 2,  'nama_gform' => 'Anak Terlantar'],
            ['id' => 12, 'nama_gform' => 'Pengemis dan Gelandangan'],
            ['id' => 21, 'nama_gform' => 'Korban Bencana']
        ];

        $data = [
            'title'      => 'Pendataan PPKS 5 PAS',
            'user'       => session()->get(),
            'kategori_5' => $kategoriPemkab,
        ];

        return view('dtsen/ppks_pas/v_ppks_pas', $data);
    }

    public function datatable()
    {
        try {
            $roleId = session()->get('role_id');

            $builder = $this->db->table('dtsen_ppks_pas p')
                ->select('p.*, a.nama, a.jenis_kelamin, a.tempat_lahir, a.tanggal_lahir, k.no_kk, k.alamat, rt.rt, rt.rw')
                // 🚀 PERBAIKAN: Pasang pelindung data terhapus (Soft Delete)
                ->join('dtsen_art a', 'a.nik = p.nik_kpm AND a.deleted_at IS NULL', 'left')
                ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                // 🚀 PERBAIKAN: Kunci data berdasarkan ID usulan agar tidak ada duplikasi baris
                ->groupBy('p.id');

            // Filter khusus PENTRI
            if ($roleId >= 4) {
                // Gunakan fallback user_id atau id
                $userId = session()->get('user_id') ?? session()->get('id') ?? 0;
                $builder->where('p.created_by', (int)$userId);
            }

            $query = $builder->orderBy('p.created_at', 'ASC')->get()->getResultArray();
            $data = [];
            $no = 1;

            foreach ($query as $row) {
                $rt_str = str_pad((string)($row['rt'] ?? '0'), 3, '0', STR_PAD_LEFT);
                $rw_str = str_pad((string)($row['rw'] ?? '0'), 3, '0', STR_PAD_LEFT);
                $alamatLengkap = ($row['alamat'] ?? 'Pasirlangu') . ' RT ' . $rt_str . ' RW ' . $rw_str;

                // 🚀 PERBAIKAN: Ubah Jenis Kelamin ke UPPERCASE
                $jk = ($row['jenis_kelamin'] == 'L') ? 'LAKI-LAKI' : 'PEREMPUAN';

                $tglLahir = !empty($row['tanggal_lahir']) ? date('d-m-Y', strtotime($row['tanggal_lahir'])) : '-';

                // 🚀 PERBAIKAN: Masukkan ID dan pastikan data dalam format UPPERCASE
                $copyData = [
                    'id'           => $row['id'], // Lempar ID untuk dieksekusi di dalam Popup
                    'nama'         => strtoupper($row['nama'] ?? '-'),
                    'no_kk'        => $row['no_kk'] ?? '-',
                    'nik'          => $row['nik_kpm'],
                    'jk'           => $jk,
                    'tempat_lahir' => strtoupper($row['tempat_lahir'] ?? '-'),
                    'tgl_lahir'    => $tglLahir,
                    'jenis_ppks'   => strtoupper($row['jenis_ppks_gform']),
                    'alamat'       => strtoupper($alamatLengkap),
                    'desa'         => 'PASIRLANGU', // 🚀 UPPERCASE
                    'kecamatan'    => 'PAKENJENG'   // 🚀 UPPERCASE
                ];

                $jsonEncoded = json_encode($copyData, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
                $jsonCopyData = htmlspecialchars($jsonEncoded ?: '{}', ENT_QUOTES, 'UTF-8');

                $statusBadge = ($row['status_gform'] == 1)
                    ? '<span class="badge bg-success"><i class="fas fa-check-double"></i> Selesai (GForm)</span>'
                    : '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu Operator</span>';

                $btnAksi = '';

                if ($roleId <= 3) {
                    // 🚀 PERBAIKAN: Tombol Tandai Selesai di tabel dihapus, cukup Salin Data saja
                    $btnAksi = '<button class="btn btn-sm btn-info btn-copy w-100 shadow-sm" data-clipboard="' . $jsonCopyData . '"><i class="fas fa-copy"></i> Salin Data</button>';
                } else {
                    if ($row['status_gform'] == 0) {
                        $btnAksi = '<button class="btn btn-sm btn-danger btn-delete shadow-sm" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i> Batal</button>';
                    } else {
                        $btnAksi = '<span class="text-muted small">Terkunci</span>';
                    }
                }

                $detailWarga = '
                    <b>' . esc(strtoupper($row['nama'] ?? 'Tanpa Nama')) . '</b><br>
                    <small class="text-muted">NIK: ' . esc($row['nik_kpm']) . ' | KK: ' . esc($row['no_kk'] ?? '-') . '</small><br>
                    <span class="badge bg-primary mt-1">' . esc($row['jenis_ppks_gform']) . '</span>
                ';

                $data[] = [
                    $no++,
                    $detailWarga,
                    strtoupper($alamatLengkap),
                    $statusBadge,
                    $btnAksi
                ];
            }

            return $this->response->setJSON(['data' => $data]);
        } catch (\Throwable $th) {
            // 🛡️ Jika masih ada error, Datatables akan menangkap pesan JSON ini tanpa merusak HTML
            return $this->response->setJSON(['error' => 'Gagal memuat data: ' . $th->getMessage()]);
        }
    }

    /**
     * 🔍 Cari individu berdasarkan NIK/Nama
     * 🔐 Dibatasi wilayah_tugas user login (multi RW–RT)
     * 🛡️ Anti Data Ganda & Bypass Soft Delete
     */
    public function searchNik()
    {
        $term = trim($this->request->getGet('q'));
        $user = $this->authModel->getUserId();
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');

        $builder = $this->db->table('dtsen_art')
            ->select("
                dtsen_art.nik,
                dtsen_art.nama,
                dtsen_rt.rw,
                dtsen_rt.rt
            ")
            // 🚀 PERBAIKAN 1: Pastikan KK yang ditarik adalah KK yang masih aktif
            ->join('dtsen_kk', 'dtsen_kk.id_kk = dtsen_art.id_kk AND dtsen_kk.deleted_at IS NULL', 'left')
            ->join('dtsen_rt', 'dtsen_rt.id_rt = dtsen_kk.id_rt', 'left')

            // 🚀 PERBAIKAN 2: Gunakan sintaks string statis agar Query Builder mutlak membaca IS NULL
            ->where('dtsen_art.deleted_at IS NULL')
            ->groupStart()
            ->like('dtsen_art.nik', $term)
            ->orLike('dtsen_art.nama', $term)
            ->groupEnd()

            // 🚀 PERBAIKAN 3: Kunci mutlak NIK agar tidak ada nama yang sama muncul dua kali
            ->groupBy('dtsen_art.nik')
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
                'id'   => $row['nik'],
                'text' => $row['nik'] . ' - ' . strtoupper($row['nama'])
            ];
        }, $rows);

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    public function simpan()
    {
        try {
            $post = $this->request->getPost();

            // Validasi Data Kosong
            if (empty($post['nik']) || empty($post['jenis_ppks'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Lengkapi form sebelum menyimpan!']);
            }

            $cek = $this->ppksPasModel->where('nik_kpm', $post['nik'])->first();
            if ($cek) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'NIK ini sudah diusulkan sebelumnya!']);
            }

            $pecahKategori = explode('|', $post['jenis_ppks']);

            // 🚀 PERBAIKAN 4: Paksa konversi ke Integer (Int) agar MySQL tidak error
            $userId = session()->get('user_id') ?? session()->get('id') ?? 0;

            $this->ppksPasModel->insert([
                'nik_kpm'          => $post['nik'],
                'kategori_id'      => (int) $pecahKategori[0],
                'jenis_ppks_gform' => $pecahKategori[1],
                'status_gform'     => 0,
                'created_by'       => (int) $userId
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil masuk antrean Operator!']);
        } catch (\Throwable $th) {
            // 🛡️ Mengembalikan format JSON jika terjadi Fatal Error saat menyimpan
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error Server: ' . $th->getMessage()]);
        }
    }

    public function tandaiSelesai()
    {
        $id = $this->request->getPost('id');
        $this->ppksPasModel->update($id, ['status_gform' => 1]);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Data ditandai selesai!']);
    }

    public function hapus()
    {
        $id = $this->request->getPost('id');
        $this->ppksPasModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Usulan dibatalkan.']);
    }

    public function exportExcel()
    {
        $roleId = session()->get('role_id');

        $builder = $this->db->table('dtsen_ppks_pas p')
            ->select('p.*, a.nama, a.jenis_kelamin, a.tempat_lahir, a.tanggal_lahir, k.no_kk, k.alamat, rt.rt, rt.rw')
            ->join('dtsen_art a', 'a.nik = p.nik_kpm AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->groupBy('p.id')
            ->orderBy('p.created_at', 'ASC');

        // Jika PENTRI, hanya boleh unduh datanya sendiri
        if ($roleId >= 4) {
            $userId = session()->get('user_id') ?? session()->get('id') ?? 0;
            $builder->where('p.created_by', (int)$userId);
        }

        $query = $builder->get()->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Form Pendataan');

        // ==========================================
        // 🚀 HEADER & JUDUL
        // ==========================================
        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'FORMULIR PENDATAAN PPKS 5 PAS KAB. GARUT');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header Kolom (Baris 4)
        $headers = ['A4' => 'No', 'B4' => 'Nama PPKS', 'C4' => 'No. KK', 'D4' => 'No. NIK', 'E4' => 'JK', 'F4' => 'Tempat Lahir', 'G4' => 'Tanggal Lahir', 'H4' => 'Jenis PPKS', 'I4' => 'Alamat sesuai KTP', 'J4' => 'Desa', 'K4' => 'Kecamatan'];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $sheet->getStyle('A4:K4')->getFont()->setBold(true);
        $sheet->getStyle('A4:K4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:K4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // ==========================================
        // 🚀 ISI DATA
        // ==========================================
        $rowNum = 5;
        $no = 1;
        foreach ($query as $row) {
            $rt_str = str_pad((string)($row['rt'] ?? '0'), 3, '0', STR_PAD_LEFT);
            $rw_str = str_pad((string)($row['rw'] ?? '0'), 3, '0', STR_PAD_LEFT);
            $alamatLengkap = ($row['alamat'] ?? 'Pasirlangu') . ' RT ' . $rt_str . ' RW ' . $rw_str;
            $jk = ($row['jenis_kelamin'] == 'L') ? 'L' : 'P'; // Di excel biasanya disingkat L/P
            $tglLahir = !empty($row['tanggal_lahir']) ? date('d-m-Y', strtotime($row['tanggal_lahir'])) : '-';

            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, strtoupper($row['nama'] ?? '-'));
            $sheet->setCellValueExplicit('C' . $rowNum, $row['no_kk'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $rowNum, $row['nik_kpm'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $rowNum, $jk);
            $sheet->setCellValue('F' . $rowNum, strtoupper($row['tempat_lahir'] ?? '-'));
            $sheet->setCellValue('G' . $rowNum, $tglLahir);
            $sheet->setCellValue('H' . $rowNum, strtoupper($row['jenis_ppks_gform']));
            $sheet->setCellValue('I' . $rowNum, strtoupper($alamatLengkap));
            $sheet->setCellValue('J' . $rowNum, 'PASIRLANGU');
            $sheet->setCellValue('K' . $rowNum, 'PAKENJENG');

            $rowNum++;
        }

        // ==========================================
        // 🚀 BORDER TABEL
        // ==========================================
        $borderStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A4:K' . ($rowNum - 1))->applyFromArray($borderStyle);
        $sheet->getStyle('A5:A' . ($rowNum - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E5:E' . ($rowNum - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G5:G' . ($rowNum - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ==========================================
        // 🚀 CATATAN KAKI (FOOTER)
        // ==========================================
        $footerRow = $rowNum + 1; // Beri jarak 1 baris kosong

        $sheet->setCellValue("B{$footerRow}", "Data PPKS yang di input merupakan PPKS desil 1-5 yang tidak pernah menerima bansos PKH, BPNT, PBI-JKN/APBD, BALEBAT dan BLT-DD.");

        $footerRow++;
        // Meracik teks merah tebal miring (Rich Text)
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText->createText('Data jenis PPKS adalah ( ');

        $redItalic = $richText->createTextRun('Lansia terlantar, Disabilitas terlantar, Anak Terlantar, Pengemis/Gelandangan dan Korban Bencana');
        $redItalic->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
        $redItalic->getFont()->setItalic(true);
        $redItalic->getFont()->setBold(true);

        $richText->createText(' .)');
        $sheet->setCellValue("B{$footerRow}", $richText);

        $footerRow++;
        $sheet->setCellValue("B{$footerRow}", "Catatan :");

        $footerRow++;
        $sheet->setCellValue("B{$footerRow}", "1. Data selain di input dalam form excel juga di input pada link Google Form");

        $footerRow++;
        $sheet->setCellValue("B{$footerRow}", "2. Setelah data di input dibuatkan BA oleh kepala desa");

        $footerRow++;
        $sheet->setCellValue("B{$footerRow}", "3. Batas Akhir input data tgl 28 Juli 2026");

        // Resize Kolom Otomatis
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ==========================================
        // 🚀 OUTPUT EXCEL
        // ==========================================
        $fileName = 'Form_Pendataan_PPKS_5PAS_Pasirlangu_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    // ==========================================
    // ⏳ FUNGSI CHECK DEADLINE UNTUK COUNTDOWN
    // ==========================================
    public function checkDeadline()
    {
        try {
            // Ambil data deadline terbaru dari tabel ppks_deadline
            $deadlineData = $this->db->table('ppks_deadline')
                ->orderBy('dd_id', 'DESC')
                ->get()
                ->getRowArray();

            return $this->response->setJSON([
                'status'   => 'success',
                'deadline' => $deadlineData
            ]);
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
