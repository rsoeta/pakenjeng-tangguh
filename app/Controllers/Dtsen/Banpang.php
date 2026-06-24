<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel; // Pastikan Model Auth dipanggil
use App\Traits\WilayahFilterTrait; // 🚀 Panggil Trait Jagoan Kita
use App\Models\Dtsen\BanpangRejectModel;
use PhpOffice\PhpSpreadsheet\IOFactory; // Pastikan library ini sudah terinstal via composer

class Banpang extends BaseController
{
    use WilayahFilterTrait; // 🚀 Gunakan Trait di dalam class

    protected $db;
    protected $authModel;
    protected $banpangRejectModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->authModel = new AuthModel(); // Inisialisasi AuthModel
        $this->banpangRejectModel = new BanpangRejectModel(); // Inisialisasi BanpangRejectModel
    }

    // ========================================================
    // 📊 HALAMAN UTAMA REKAP BANPANG
    // ========================================================
    public function index()
    {
        $data = [
            'title' => 'Rekapitulasi Bantuan Pangan (QR)'
        ];
        return view('dtsen/banpang/v_rekap', $data);
    }

    // ========================================================
    // 🚀 DATATABLES REKAP BANPANG (ANTI-GANDA SUBQUERY)
    // ========================================================
    public function datatable()
    {
        $request = \Config\Services::request();

        if (!$request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        $draw   = $request->getPost('draw');
        $start  = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';

        $filter_rw = $request->getPost('filter_rw');
        $filter_rt = $request->getPost('filter_rt');

        $user   = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? $user['role_id'] ?? 4;
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        // 🚀 KUNCI ANTI GANDA: Subquery untuk mengambil 1 riwayat KK terbaru per NIK
        $subqueryART = '(SELECT nik, MAX(id_kk) as id_kk FROM dtsen_art WHERE deleted_at IS NULL GROUP BY nik)';

        $builder = $this->db->table('dtsen_banpang b')
            ->select('b.*, rt.rt, rt.rw, k.alamat')
            ->join($subqueryART . ' a', 'a.nik = b.nik_kpm', 'left') // <- JOIN dari Subquery
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left');

        // 🔐 TERAPKAN TRAIT WILAYAH FILTER (DENGAN LOGIKA PINTAR/BYPASS)
        if ($roleId > 3 || !empty($filter_rw) || !empty($filter_rt)) {
            $filterData = [
                'kode_desa'     => $kodeDesa,
                'wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')
            ];
            $this->applyWilayahFilter($builder, $filterData, $roleId);
        }

        // Hitung Total Data (Sudah akurat karena tidak ada groupBy)
        $totalRecords = $builder->countAllResults(false);

        // 🔍 FILTER DINAMIS DARI FRONTEND
        if (!empty($filter_rw)) {
            $builder->where('rt.rw', str_pad($filter_rw, 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filter_rt)) {
            $builder->where('rt.rt', str_pad($filter_rt, 3, '0', STR_PAD_LEFT));
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('b.nik_kpm', $search)
                ->orLike('b.nama_kpm', $search)
                ->orLike('b.no_pbp', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        // Urutkan yang terbaru discan berada di paling atas
        $builder->orderBy('b.waktu_scan', 'DESC');

        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $query = $builder->get()->getResultArray();

        $data = [];
        $no = $start + 1;

        foreach ($query as $row) {
            $nikLengkap = esc($row['nik_kpm']);
            $nikMasked = strlen($nikLengkap) >= 16 ? substr($nikLengkap, 0, 8) . '********' : $nikLengkap;

            $teksAlamat = !empty($row['alamat']) ? esc($row['alamat']) . '<br>' : '';
            $alamat = (!empty($row['rt']) && !empty($row['rw']))
                ? $teksAlamat . '<span class="badge badge-light border mt-1">RT ' . esc($row['rt']) . ' / RW ' . esc($row['rw']) . '</span>'
                : $teksAlamat . '<span class="badge badge-warning mt-1">Luar Wilayah / Tidak Ditemukan</span>';

            $status = ($row['status_kelengkapan'] == 1)
                ? '<span class="badge badge-success"><i class="fas fa-check"></i> Selesai</span>'
                : '<span class="badge badge-info"><i class="fas fa-qrcode"></i> Ter-Scan</span>';

            $waktuScan = date('d/m/Y H:i:s', strtotime($row['waktu_scan']));

            $data[] = [
                $no++,
                esc($row['no_pbp']),
                '<span class="font-weight-bold text-dark">' . esc($row['nama_kpm']) . '</span><br><small class="text-muted">' . $nikMasked . '</small>',
                $alamat,
                $waktuScan,
                $status,
                '<button class="btn btn-xs btn-outline-primary" title="Detail"><i class="fas fa-search"></i></button>'
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data
        ]);
    }

    // ========================================================
    // 🟢 EXPORT EXCEL NATIVE (USING PHPSPREADSHEET)
    // ========================================================
    public function exportExcel()
    {
        $filter_rw = $this->request->getGet('filter_rw');
        $filter_rt = $this->request->getGet('filter_rt');

        $user   = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? $user['role_id'] ?? 4;
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        // 🚀 TAMBAH k.alamat
        $builder = $this->db->table('dtsen_banpang b')
            ->select('b.*, rt.rt, rt.rw, k.alamat')
            ->join('dtsen_art a', 'a.nik = b.nik_kpm AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left');

        // Bypass Trait jika Admin ingin export semua data tanpa filter
        if ($roleId > 3 || !empty($filter_rw) || !empty($filter_rt)) {
            $filterData = [
                'kode_desa'     => $kodeDesa,
                'wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')
            ];
            $this->applyWilayahFilter($builder, $filterData, $roleId);
        }

        if (!empty($filter_rw)) {
            $builder->where('rt.rw', str_pad($filter_rw, 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filter_rt)) {
            $builder->where('rt.rt', str_pad($filter_rt, 3, '0', STR_PAD_LEFT));
        }

        // 🚀 KUNCI ANTI GANDA
        $builder->groupBy('b.id');

        $builder->orderBy('rt.rw', 'ASC')->orderBy('rt.rt', 'ASC')->orderBy('b.nama_kpm', 'ASC');
        $query = $builder->get()->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 🚀 UPGRADE: Lebar Judul disesuaikan sampai kolom I (karena tambah 1 kolom alamat)
        $sheet->setCellValue('A1', 'DAFTAR REKAPITULASI PENYALURAN BANTUAN PANGAN (BANPANG)');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $subtitle = "Pemerintah Desa/Kelurahan: " . (!empty($kodeDesa) ? $kodeDesa : '-');
        if (!empty($filter_rw)) $subtitle .= " | RW: " . $filter_rw;
        if (!empty($filter_rt)) $subtitle .= " | RT: " . $filter_rt;
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // 🚀 UPGRADE: Sisipkan Header 'Alamat', lalu RT dan RW terpisah
        $headers = ['No', 'No. PBP (Undangan)', 'No. BAST', 'NIK KPM', 'Nama Penerima Manfaat', 'Alamat', 'RT', 'RW', 'Waktu Pengambilan'];
        $sheet->fromArray($headers, NULL, 'A4');
        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('EAEAEA');

        $rowNum = 5;
        $no = 1;
        foreach ($query as $row) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValueExplicit('B' . $rowNum, $row['no_pbp'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $rowNum, $row['no_bast'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $rowNum, $row['nik_kpm'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $rowNum, $row['nama_kpm']);

            // 🚀 Kolom F untuk Alamat, Kolom G untuk RT, Kolom H untuk RW
            $sheet->setCellValue('F' . $rowNum, !empty($row['alamat']) ? $row['alamat'] : '-');
            $sheet->setCellValueExplicit('G' . $rowNum, !empty($row['rt']) ? $row['rt'] : '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H' . $rowNum, !empty($row['rw']) ? $row['rw'] : '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            $sheet->setCellValue('I' . $rowNum, date('d-m-Y H:i:s', strtotime($row['waktu_scan'])));
            $rowNum++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Rekap_Scan_Banpang_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // ========================================================
    // 🔴 CETAK PDF REPORT (OFFICIAL VILLAGE STANDARDIZED)
    // ========================================================
    public function exportPdf()
    {
        $filter_rw = $this->request->getGet('filter_rw');
        $filter_rt = $this->request->getGet('filter_rt');

        $user   = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? $user['role_id'] ?? 4;
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        // 🚀 TAMBAH k.alamat
        $builder = $this->db->table('dtsen_banpang b')
            ->select('b.*, rt.rt, rt.rw, k.alamat')
            ->join('dtsen_art a', 'a.nik = b.nik_kpm AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left');

        // Bypass Trait jika tidak ada filter
        if ($roleId > 3 || !empty($filter_rw) || !empty($filter_rt)) {
            $filterData = [
                'kode_desa'     => $kodeDesa,
                'wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')
            ];
            $this->applyWilayahFilter($builder, $filterData, $roleId);
        }

        if (!empty($filter_rw)) {
            $builder->where('rt.rw', str_pad($filter_rw, 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filter_rt)) {
            $builder->where('rt.rt', str_pad($filter_rt, 3, '0', STR_PAD_LEFT));
        }

        // 🚀 KUNCI ANTI GANDA
        $builder->groupBy('b.id');

        $builder->orderBy('rt.rw', 'ASC')->orderBy('rt.rt', 'ASC')->orderBy('b.nama_kpm', 'ASC');
        $query = $builder->get()->getResultArray();

        $data = [
            'title'     => 'Laporan Penyaluran Bantuan Pangan',
            'query'     => $query,
            'filter_rw' => $filter_rw,
            'filter_rt' => $filter_rt,
            'desa'      => $kodeDesa,
            'nama_petugas' => $user['nama'] ?? session()->get('nama') ?? 'Operator Desa'
        ];

        return view('dtsen/banpang/v_cetak_pdf', $data);
    }

    // ... (Fungsi scanner(), simpanScan(), dan getLatestScans() biarkan tetap ada di bawah sini) ...
    // Tampilkan Halaman Scanner
    public function scanner()
    {
        $data = [
            'title' => 'Scanner Bantuan Pangan (Mode Kasir)'
        ];
        return view('dtsen/banpang/v_scanner', $data);
    }

    // Tangkap Data dari AJAX Scanner
    public function simpanScan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $no_pbp  = trim($this->request->getPost('no_pbp'));
        $no_bast = trim($this->request->getPost('no_bast'));
        $nik     = trim($this->request->getPost('nik'));
        $nama    = trim($this->request->getPost('nama'));

        if (empty($no_pbp) || empty($nik)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format QR tidak valid!']);
        }

        // 🛡️ RADAR ANTI-DOUBLE: Cegah No PBP discan dua kali
        $cek = $this->db->table('dtsen_banpang')->where('no_pbp', $no_pbp)->countAllResults();
        if ($cek > 0) {
            return $this->response->setJSON([
                'status'  => 'warning',
                'nama'    => $nama,
                'message' => 'Sudah pernah di-scan sebelumnya!'
            ]);
        }

        // 📥 SIMPAN DATA
        $insertData = [
            'no_pbp'     => $no_pbp,
            'no_bast'    => $no_bast,
            'nik_kpm'    => $nik,
            'nama_kpm'   => $nama,
            'waktu_scan' => date('Y-m-d H:i:s'),
            'id_petugas' => session()->get('id') ?? 0
        ];

        try {
            $this->db->table('dtsen_banpang')->insert($insertData);
            return $this->response->setJSON([
                'status'  => 'success',
                'nama'    => $nama,
                'message' => 'Berhasil direkap!'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan database!']);
        }
    }

    // ========================================================
    // 🔄 AMBIL 5 DATA SCAN TERAKHIR (LIVE FEEDBACK)
    // ========================================================
    public function getLatestScans()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        // Tarik 5 data paling baru berdasarkan ID
        $latest = $this->db->table('dtsen_banpang')
            ->select('nama_kpm, waktu_scan')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Format waktu agar lebih enak dibaca (H:i:s)
        foreach ($latest as &$row) {
            $row['waktu'] = date('H:i:s', strtotime($row['waktu_scan']));
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $latest]);
    }

    // ========================================================
    // 📥 FITUR: IMPORT DATA REJECT BULOG (EXCEL)
    // ========================================================
    public function importExcelReject()
    {
        $fileExcel = $this->request->getFile('file_excel');

        if (!$fileExcel || !$fileExcel->isValid() || $fileExcel->hasMoved()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'File Excel gagal diunggah atau tidak valid.'
            ]);
        }

        // Ambil ekstensi file
        $ext = $fileExcel->getClientExtension();
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Format file harus berupa .xls atau .xlsx'
            ]);
        }

        try {
            // 🚀 PERBAIKAN: Baca file Excel dengan mode RAW (Abaikan Formula & Styling)
            $reader = IOFactory::createReaderForFile($fileExcel->getTempName());
            $reader->setReadDataOnly(true); // Abaikan warna, border, dan styling lainnya
            $spreadsheet = $reader->load($fileExcel->getTempName());

            $sheet = $spreadsheet->getActiveSheet();

            // 🚀 PERBAIKAN: Parameter kedua diset `false` agar PhpSpreadsheet TIDAK menghitung rumus
            // Signature: toArray($nullValue, $calculateFormulas, $formatData, $returnCellRef)
            $rows  = $sheet->toArray(null, false, false, false);

            // Pastikan data tidak kosong (minimal ada 1 baris data di bawah header)
            if (count($rows) <= 1) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'File Excel kosong atau tidak ada data di bawah header.'
                ]);
            }

            // ... (lanjutkan dengan kode looping $banpangRejectModel->insertBatch yang sama seperti sebelumnya) ...

            $banpangRejectModel = new BanpangRejectModel();
            $dataInsert = [];

            // Looping data mulai dari baris kedua (index 1) karena baris pertama adalah Header
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Skip jika NIK atau No PBP kosong
                if (empty($row[0]) || empty($row[6])) {
                    continue;
                }

                $dataInsert[] = [
                    'nik'                 => trim($row[0] ?? ''),
                    'no_kk'               => trim($row[1] ?? ''),
                    'nama'                => trim($row[2] ?? ''),
                    'foto_ktp'            => trim($row[3] ?? ''),
                    'foto_pbp'            => trim($row[4] ?? ''),
                    'transporter_name'    => trim($row[5] ?? ''),
                    'no_pbp'              => trim($row[6] ?? ''),
                    'alamat_pbp'          => trim($row[7] ?? ''),
                    'lat_penyaluran'      => trim($row[8] ?? ''),
                    'long_penyaluran'     => trim($row[9] ?? ''),
                    'status_pbp'          => trim($row[10] ?? ''),
                    'nik_pengganti'       => trim($row[11] ?? ''),
                    'no_kk_pengganti'     => trim($row[12] ?? ''),
                    'nama_pengganti'      => trim($row[13] ?? ''),
                    'notes'               => trim($row[14] ?? ''),
                    'alamat_pengganti'    => trim($row[15] ?? ''),
                    'verification_status' => trim($row[16] ?? ''),
                    'status_serah'        => trim($row[17] ?? ''),
                    'no_bast'             => trim($row[18] ?? ''),
                    'alokasi_bulan'       => trim($row[19] ?? ''),
                    'alokasi_tahun'       => trim($row[20] ?? ''),
                    'entitas'             => trim($row[21] ?? ''),
                    'provinsi'            => trim($row[22] ?? ''),
                    'kabupaten'           => trim($row[23] ?? ''),
                    'kecamatan'           => trim($row[24] ?? ''),
                    'kelurahan'           => trim($row[25] ?? ''),
                    'is_redocumented'     => 0 // Default belum difoto ulang
                ];
            }

            // Insert massal ke database
            if (!empty($dataInsert)) {
                $banpangRejectModel->insertBatch($dataInsert);
                $jumlah = count($dataInsert);

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => "Berhasil mengimpor {$jumlah} data KPM reject."
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Tidak ada data valid yang bisa dimasukkan.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    // ========================================================
    // 📸 FITUR: SIMPAN DOKUMENTASI DARI LAPANGAN & TRIGGER WA
    // ========================================================
    public function simpanDokumentasiReject()
    {
        // 1. Proteksi Akses AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        $idKpm = $this->request->getPost('id');

        if (empty($idKpm)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ID KPM tidak valid atau tidak ditemukan.'
            ]);
        }

        $banpangRejectModel = new BanpangRejectModel();
        $kpm = $banpangRejectModel->find($idKpm);

        if (!$kpm) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data KPM tidak ditemukan di database.'
            ]);
        }

        // 2. Validasi Berkas Unggahan (Foto KTP & Foto PBP Swafoto)
        $validationRule = [
            'foto_ktp_sinden' => [
                'label' => 'Foto KTP',
                'rules' => 'uploaded[foto_ktp_sinden]|is_image[foto_ktp_sinden]|mime_in[foto_ktp_sinden,image/jpg,image/jpeg,image/png]'
            ],
            'foto_pbp_sinden' => [
                'label' => 'Foto Swafoto KPM',
                'rules' => 'uploaded[foto_pbp_sinden]|is_image[foto_pbp_sinden]|mime_in[foto_pbp_sinden,image/jpg,image/jpeg,image/png]'
            ]
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(' ', $this->validator->getErrors())
            ]);
        }

        // 3. Persiapan Folder Penyimpanan (Mencegah eror akibat git clean)
        $uploadDir = ROOTPATH . 'public/uploads/banpang_reject/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        try {
            $fileKtp = $this->request->getFile('foto_ktp_sinden');
            $filePbp = $this->request->getFile('foto_pbp_sinden');

            // 🚀 PERBAIKAN 1: Format Penamaan File Standar BULOG
            // Konversi nama bulan ke angka jika format datanya berupa teks
            $blnMap = ['januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04', 'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08', 'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12'];
            $alokasiBulan = strtolower(trim($kpm['alokasi_bulan']));
            $bulanNum = isset($blnMap[$alokasiBulan]) ? $blnMap[$alokasiBulan] : str_pad((int)$kpm['alokasi_bulan'], 2, '0', STR_PAD_LEFT);
            if ($bulanNum == '00') $bulanNum = date('m'); // Fallback jika kosong

            $tahun = !empty($kpm['alokasi_tahun']) ? $kpm['alokasi_tahun'] : date('Y');
            $kelurahanClean = str_replace('.', '', $kpm['kelurahan']); // Buang titik 32.05...
            $timestamp = (new \DateTime())->format('Y-m-d\TH_i_s.u'); // Format waktu presisi

            // Format: cacheBAST-2026033205332006-320533200600793-2026-06-20T17_46_50.066187
            $prefix = "cacheBAST-{$tahun}{$bulanNum}{$kelurahanClean}-{$kpm['no_pbp']}-{$timestamp}";

            $newKtpName = $prefix . '-identitas.' . $fileKtp->getClientExtension();
            $newPbpName = $prefix . '-pbp.' . $filePbp->getClientExtension();

            // Pindahkan file asli ke server
            $fileKtp->move($uploadDir, $newKtpName);
            $filePbp->move($uploadDir, $newPbpName);

            // 🚀 JURUS ANTI-MIRING: Perbaiki orientasi foto dari metadata EXIF
            $this->fixExifOrientation($uploadDir . $newKtpName);
            $this->fixExifOrientation($uploadDir . $newPbpName);

            // 🚀 JURUS KOMPRESI FOTO: Mengurangi beban server
            $imageService = \Config\Services::image();
            // ... (lanjutkan script resize kompresi Kang Rian yang lama)

            // Kompresi Foto KTP
            $imageService->withFile($uploadDir . $newKtpName)
                ->resize(1024, 768, true, 'height')
                ->save($uploadDir . $newKtpName, 70);

            // Kompresi Foto Swafoto (PBP)
            $imageService->withFile($uploadDir . $newPbpName)
                ->resize(1024, 768, true, 'height')
                ->save($uploadDir . $newPbpName, 70);

            // 4. Perbarui Data ke Database
            // Ambil ID PENTRI untuk disimpan ke database, dan Nama untuk Notif WA Admin
            $idPentri   = session()->get('id'); // Pastikan key session ID-nya sesuai dengan sistem Kang Rian (misal 'id' atau 'id_user')
            $namaPentri = session()->get('fullname') ?? session()->get('username') ?? 'Petugas Lapangan';

            $dataUpdate = [
                'foto_ktp_sinden'   => 'uploads/banpang_reject/' . $newKtpName,
                'foto_pbp_sinden'   => 'uploads/banpang_reject/' . $newPbpName,
                'is_redocumented'   => 1,
                // 🚀 PERBAIKAN: Simpan ID Petugas, bukan namanya
                'updated_by_pentri' => $idPentri
            ];

            $banpangRejectModel->update($idKpm, $dataUpdate);

            // 🚀 PELATUK (TRIGGER) WHATSAPP NOTIFIKASI
            // Pesan WA ke Admin tetap menggunakan $namaPentri agar human-readable
            $this->kirimNotifikasiWaAdmin($kpm, $namaPentri);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Dokumentasi perbaikan KPM berhasil disimpan dan dilaporkan.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memproses dokumentasi: ' . $e->getMessage()
            ]);
        }
    }

    // ========================================================
    // 📲 PRIVATE HELPER: ENGINE PENGIRIM PESAN WHATSAPP
    // ========================================================
    private function kirimNotifikasiWaAdmin($kpm, $namaPentri)
    {
        $db = \Config\Database::connect();

        // 1. Ambil kode_desa petugas (login)
        $kodeDesaPetugas = session()->get('kode_desa');

        // 2. Ambil semua admin (role_id=3) dalam desa yang sama
        $admins = $db->table('dtks_users')
            ->select('fullname, nope')
            ->where('role_id', 3)
            ->where('kode_desa', $kodeDesaPetugas)
            ->get()
            ->getResultArray();

        // Jika tidak ada admin → log dan skip
        if (empty($admins)) {
            log_message('warning', "[WA Banpang Reject] Tidak ditemukan admin role_id=3 pada desa {$kodeDesaPetugas}");
            return false;
        }

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

        $now      = date('Y-m-d H:i:s');
        $hariIndo = $hari[date('l', strtotime($now))];
        $tgl      = date('d', strtotime($now));
        $bln      = $bulan[intval(date('m', strtotime($now)))];
        $thn      = date('Y', strtotime($now));
        $jam      = date('H:i', strtotime($now)) . " WIB";

        // ... (kode penentuan tanggal & waktu sebelumnya tetap sama) ...
        $tanggalLengkap = "{$hariIndo}, {$tgl} {$bln} {$thn}, {$jam}";

        // 🚀 PERBAIKAN: Ambil Nama Kelurahan dari tb_villages
        $village = $db->table('tb_villages')
            ->select('name')
            ->where('id', $kpm['kelurahan'])
            ->get()
            ->getRowArray();

        // Ubah jadi huruf kapital di awal kata (Title Case) agar rapi, fallback ke kode jika tidak ketemu
        $namaKelurahan = $village ? ucwords(strtolower($village['name'])) : $kpm['kelurahan'];

        // === Format Pesan WA Final ===
        $pesan = "📢 *NOTIFIKASI SISTEM SINDEN*\n"
            . "*Verifikasi & Dokumentasi Ulang Bantuan Pangan (BULOG)*\n\n"
            . "Yth. Admin SINDEN,\n"
            . "Telah dilakukan pendokumentasian ulang (KTP & Swafoto) untuk Keluarga Penerima Manfaat (KPM) oleh Petugas Entri (PENTRI) di lapangan:\n\n"
            . "📄 No. PBP: *{$kpm['no_pbp']}*\n"
            . "👤 Nama KPM: *{$kpm['nama']}*\n"
            . "💳 NIK: *{$kpm['nik']}*\n"
            . "📍 Alamat: {$kpm['alamat_pbp']} / Kel. {$namaKelurahan}\n"
            . "👨‍💼 Petugas: *{$namaPentri}*\n"
            . "⌚ Waktu: {$tanggalLengkap}\n\n"
            . "✔ Seluruh kelengkapan berkas KTP & Swafoto berhasil tersimpan di server. Silakan login ke Dashboard SINDEN untuk mengunduh data.";

        // === Kirim WA ke setiap admin (role_id = 3) ===
        $wa = new \App\Libraries\WaService();
        // ... (kode foreach pengiriman WA ke admin di bawahnya tetap sama) ...

        foreach ($admins as $admin) {

            if (empty($admin['nope'])) {
                log_message('warning', "[WA Banpang Reject] Admin {$admin['fullname']} tidak memiliki nomor WhatsApp");
                continue;
            }

            // Normalisasi nomor WA
            $nomorWA = preg_replace('/[^0-9]/', '', $admin['nope']);
            if (str_starts_with($nomorWA, '0')) {
                $nomorWA = '62' . substr($nomorWA, 1);
            }

            try {
                $send = $wa->sendText($nomorWA, $pesan);
                log_message('info', "[WA Banpang Reject] Pesan dikirim ke admin {$admin['fullname']} ({$nomorWA}) | " . json_encode($send));
            } catch (\Throwable $e) {
                log_message('error', "[WA Banpang Reject] ERROR kirim WA ke {$nomorWA}: " . $e->getMessage());
            }
        }

        return true;
    }

    // ========================================================
    // 📊 HALAMAN UTAMA REKAP BANPANG
    // ========================================================
    public function indexReject()
    {
        $data = [
            'title' => 'Rekapitulasi Bantuan Pangan (Reject Bulog)'
        ];
        return view('dtsen/banpang/v_index_reject', $data);
    }

    // ========================================================
    // 📊 FITUR: DATATABLES DATA REJECT BULOG DENGAN KARANTINA WILAYAH
    // ========================================================
    public function datatableReject()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        $db = \Config\Database::connect();

        // 1. Inisialisasi Builder & Kaitan Tabel (JOIN)
        $builder = $db->table('dtsen_banpang_reject br');

        $builder->select('br.*, rt.rt, rt.rw');
        $builder->join('dtsen_kk kk', 'kk.no_kk = br.no_kk AND kk.deleted_at IS NULL', 'left');
        $builder->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');

        // 🚀 PERBAIKAN 1: Kunci Anti-Duplikasi (GROUP BY)
        // Memaksa MySQL hanya menampilkan 1 baris untuk setiap ID KPM Reject, 
        // mengabaikan jika ada lebih dari 1 kecocokan no_kk di tabel dtsen_kk
        $builder->groupBy('br.id');

        // 2. 🔐 IMPLEMENTASI KARANTINA WILAYAH
        $roleId   = session()->get('role_id');
        $kodeDesa = session()->get('kode_desa');
        $wilTugas = session()->get('wilayah_tugas');

        // Filter mutlak desa menggunakan kolom kelurahan (bawaan BULOG)
        $builder->where('br.kelurahan', $kodeDesa);

        // Kosongkan 'kode_desa' agar Trait tidak menggunakan rt.kode_desa
        $filterData = [
            'kode_desa'     => '',
            'wilayah_tugas' => trim($wilTugas ?? '')
        ];

        // Trait otomatis membaca alias 'rt'
        $this->applyWilayahFilter($builder, $filterData, $roleId);

        // 🚀 PERBAIKAN 3: Tangkap & Eksekusi Filter Status
        $filterStatus = $this->request->getPost('filter_status');
        if ($filterStatus !== 'all' && $filterStatus !== null && $filterStatus !== '') {
            $builder->where('br.is_redocumented', $filterStatus);
        }

        // 3. Logika Pencarian DataTables (Search Box)
        // ...
        $searchValue = $this->request->getPost('search')['value'] ?? '';
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('br.nik', $searchValue)
                ->orLike('br.nama', $searchValue)
                ->orLike('br.no_pbp', $searchValue)
                ->groupEnd();
        }

        // 4. Hitung Total & Terapkan Paginasi
        $start  = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;

        // Hitung total data setelah difilter (dan di-group)
        $builderCount = clone $builder;
        $totalRecord  = $builderCount->countAllResults(false);

        // Urutkan: Prioritaskan yang belum difoto (is_redocumented = 0) agar tampil di atas
        $builder->orderBy('br.is_redocumented', 'ASC');
        $builder->orderBy('br.id', 'DESC');
        $builder->limit($length, $start);

        $data = $builder->get()->getResultArray();

        // 5. Racik Baris Data (Formatting HTML)
        $result = [];
        $no     = $start + 1;

        foreach ($data as $row) {
            // Logika Badge Status untuk 3 Kondisi (0, 1, dan 2)
            if ($row['is_redocumented'] == 2) {
                $statusBadge = '<span class="badge bg-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> Valid / Selesai</span>';
            } elseif ($row['is_redocumented'] == 1) {
                $statusBadge = '<span class="badge bg-info text-white px-2 py-1"><i class="fas fa-clock mr-1"></i> Menunggu Verifikasi</span>';
            } else {
                $statusBadge = '<span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-camera mr-1"></i> Belum Difoto</span>';
            }

            // Tampilan Wilayah Text (Alamat PBP di atas RT/RW)
            $alamatPbp = esc($row['alamat_pbp']);
            $wilayahText = !empty($row['rt'])
                ? "{$alamatPbp}<br><small class='text-muted font-weight-bold'><i class='fas fa-map-marker-alt mr-1'></i>RT {$row['rt']} / RW {$row['rw']}</small>"
                : $alamatPbp;

            // Jika status 1 (menunggu) ATAU 2 (terverifikasi), kunci ke tombol 'Lihat'
            if ($row['is_redocumented'] == 1 || $row['is_redocumented'] == 2) {
                $btnAksi = '<button class="btn btn-sm btn-outline-success font-weight-bold" onclick="lihatFoto(' . $row['id'] . ', \'' . $row['foto_ktp_sinden'] . '\', \'' . $row['foto_pbp_sinden'] . '\', ' . $row['is_redocumented'] . ')" title="Lihat Hasil"><i class="fas fa-image mr-1"></i> Lihat</button>';
            } else {
                // Tombol Kamera untuk PENTRI jika status masih 0
                $urlKamera = base_url('banpang/reject/kamera/' . $row['id']);
                $btnAksi   = '<a href="' . $urlKamera . '" class="btn btn-sm btn-primary shadow-sm font-weight-bold" title="Ambil Foto"><i class="fas fa-camera mr-1"></i> Foto</a>';
            }

            // 🚀 PERBAIKAN: Masking + Fitur Hover + Tombol Salin
            $nikAsli = trim($row['nik']);
            $maskedNik = $nikAsli;
            if (strlen($nikAsli) > 8) {
                $sensorNik = substr($nikAsli, 0, 8) . str_repeat('*', strlen($nikAsli) - 8);
                $maskedNik = '<span title="' . esc($nikAsli) . '" style="cursor: help; border-bottom: 1px dotted #888;">' . $sensorNik . '</span>' .
                    ' <button class="btn btn-xs btn-light border-0 p-0 ml-1" onclick="copyToClipboard(\'' . $nikAsli . '\')" title="Salin NIK"><i class="fas fa-copy text-secondary"></i></button>';
            }

            $pbpAsli = trim($row['no_pbp']);
            $maskedPbp = $pbpAsli;
            if (strlen($pbpAsli) > 8) {
                $sensorPbp = substr($pbpAsli, 0, 8) . str_repeat('*', strlen($pbpAsli) - 8);
                $maskedPbp = '<span title="' . esc($pbpAsli) . '" style="cursor: help; border-bottom: 1px dotted #888;">' . $sensorPbp . '</span>' .
                    ' <button class="btn btn-xs btn-light border-0 p-0 ml-1" onclick="copyToClipboard(\'' . $pbpAsli . '\')" title="Salin No. PBP"><i class="fas fa-copy text-secondary"></i></button>';
            }

            $result[] = [
                'no'           => $no++,
                'no_pbp'       => $maskedPbp,
                'nik'          => $maskedNik,
                'nama'         => $row['nama'],
                'wilayah'      => $wilayahText,
                'status_badge' => $statusBadge,
                'aksi'         => $btnAksi
            ];
        }

        // 6. Kembalikan ke format JSON DataTables
        return $this->response->setJSON([
            'draw'            => intval($this->request->getPost('draw')),
            'recordsTotal'    => $totalRecord,
            'recordsFiltered' => $totalRecord,
            'data'            => $result
        ]);
    }

    // ========================================================
    // 🖥️ FITUR: HALAMAN UI KAMERA PENTRI
    // ========================================================
    public function kameraReject($id)
    {
        $banpangRejectModel = new BanpangRejectModel();
        $kpm = $banpangRejectModel->find($id);

        if (!$kpm) {
            return redirect()->to(base_url('banpang/reject'))->with('error', 'Data KPM tidak ditemukan.');
        }

        $data = [
            'title' => 'Kamera Dokumentasi Banpang',
            'kpm'   => $kpm
        ];

        return view('dtsen/banpang/v_kamera_reject', $data);
    }

    // ========================================================
    // 📸 HELPER: MEMPERBAIKI ORIENTASI FOTO DARI HP (EXIF)
    // ========================================================
    private function fixExifOrientation($filePath)
    {
        $exif = @exif_read_data($filePath);
        if (!empty($exif['Orientation'])) {
            $image = \Config\Services::image()->withFile($filePath);
            switch ($exif['Orientation']) {
                case 3:
                    $image->rotate(180)->save($filePath);
                    break;
                case 6:
                    $image->rotate(270)->save($filePath); // Putar searah jarum jam
                    break;
                case 8:
                    $image->rotate(90)->save($filePath); // Putar berlawanan arah
                    break;
            }
        }
    }

    // ========================================================
    // ✅ FITUR: VERIFIKASI ADMIN & TRIGGER WA PENTRI
    // ========================================================
    public function aksiVerifikasiReject()
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);

        $id     = $this->request->getPost('id');
        $aksi   = $this->request->getPost('aksi'); // 'verify' atau 'reject'
        $alasan = $this->request->getPost('alasan');

        $banpangRejectModel = new BanpangRejectModel();
        $kpm = $banpangRejectModel->find($id);

        if (!$kpm) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data KPM tidak ditemukan.']);
        }

        if ($aksi === 'verify') {
            // Jika Verified: Status berubah jadi 2 (Selesai Sepenuhnya)
            $banpangRejectModel->update($id, [
                'is_redocumented' => 2,
                'verification_status' => 'VERIFIED SINDEN'
            ]);
            $pesanAksi = "Data KPM berhasil divalidasi.";
        } else {
            // Jika Reject: Kembalikan status ke 0 (Belum difoto), hapus file lama, update notes
            $banpangRejectModel->update($id, [
                'is_redocumented' => 0,
                'notes' => 'REJECT ADMIN DESA: ' . $alasan,
                'foto_ktp_sinden' => null,
                'foto_pbp_sinden' => null
            ]);

            // Hapus file fisik agar server tidak bengkak
            if (file_exists(ROOTPATH . 'public/' . $kpm['foto_ktp_sinden'])) unlink(ROOTPATH . 'public/' . $kpm['foto_ktp_sinden']);
            if (file_exists(ROOTPATH . 'public/' . $kpm['foto_pbp_sinden'])) unlink(ROOTPATH . 'public/' . $kpm['foto_pbp_sinden']);

            $pesanAksi = "Data KPM ditolak dan PENTRI diminta foto ulang.";
        }

        // 🚀 TRIGGER WA KE PENTRI
        $this->kirimWaKePentri($kpm, $aksi, $alasan);

        return $this->response->setJSON(['status' => 'success', 'message' => $pesanAksi]);
    }

    // ========================================================
    // 📲 HELPER: TRIGGER WA KE PENTRI (APRESIASI / REVISI)
    // ========================================================
    private function kirimWaKePentri($kpm, $aksi, $alasan)
    {
        $db = \Config\Database::connect();

        // 🚀 PERBAIKAN: Cari berdasarkan ID (Primary Key)
        $idPentri = $kpm['updated_by_pentri'];

        $pentri = $db->table('dtks_users')
            ->select('nope, fullname')
            ->where('id', $idPentri)
            ->get()
            ->getRowArray();

        // Batalkan jika data petugas tidak ditemukan atau nomor WA kosong
        if (!$pentri || empty($pentri['nope'])) return false;

        $nomorWA = preg_replace('/[^0-9]/', '', $pentri['nope']);
        if (str_starts_with($nomorWA, '0')) $nomorWA = '62' . substr($nomorWA, 1);

        $now = date('d-m-Y H:i');

        if ($aksi === 'verify') {
            $pesan = "📢 *NOTIFIKASI SISTEM SINDEN*\n"
                . "Halo, {$pentri['fullname']}! 👋\n\n"
                . "Admin Pemerintah Desa menyampaikan *TERIMA KASIH* dan apresiasi atas kerja keras Anda. Dokumentasi perbaikan KPM berikut telah *DIVERIFIKASI* dan dinyatakan *LAYAK*:\n\n"
                . "👤 Nama KPM: *{$kpm['nama']}*\n"
                . "💳 NIK: {$kpm['nik']}\n"
                . "⌚ Waktu Validasi: {$now}\n\n"
                . "Tetap semangat bertugas di lapangan! 🚀";
        } else {
            $pesan = "📢 *NOTIFIKASI SISTEM SINDEN (REVISI)*\n"
                . "Halo, {$pentri['fullname']}.\n\n"
                . "Mohon maaf, dokumentasi perbaikan KPM berikut *DITOLAK* oleh Admin karena belum memenuhi standar BULOG:\n\n"
                . "👤 Nama KPM: *{$kpm['nama']}*\n"
                . "💳 NIK: {$kpm['nik']}\n"
                . "❌ *ALASAN PENOLAKAN:* {$alasan}\n\n"
                . "Mohon segera kunjungi kembali KPM tersebut dan lakukan *PEMOTRETAN ULANG* melalui Aplikasi SINDEN. Terima kasih atas kerja samanya! 🙏";
        }

        $wa = new \App\Libraries\WaService();
        try {
            $send = $wa->sendText($nomorWA, $pesan);
            log_message('info', "[WA Pentri Banpang] Sukses kirim ke {$pentri['fullname']} ({$nomorWA}) | " . json_encode($send));
        } catch (\Throwable $e) {
            log_message('error', "[WA Pentri Banpang] Gagal kirim ke {$nomorWA}: " . $e->getMessage());
        }
    }

    // Fungsi 1: Pencarian AJAX
    public function searchKpmAjax()
    {
        $search = $this->request->getGet('q');
        $db = \Config\Database::connect();

        // 🚀 Tambahkan a.no_bast di bagian select
        $builder = $db->table('dtsen_banpang a')
            ->select('a.id, a.no_pbp, a.no_bast, a.nik_kpm, a.nama_kpm, c.no_kk')
            ->join('dtsen_art b', 'b.nik = a.nik_kpm', 'left')
            ->join('dtsen_kk c', 'c.id_kk = b.id_kk', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('a.no_pbp', $search)
                ->orLike('a.nik_kpm', $search)
                ->orLike('a.nama_kpm', $search)
                ->groupEnd();
        }

        $query = $builder->limit(10)->get()->getResultArray();
        $data = [];

        foreach ($query as $row) {
            $no_kk_aman = !empty($row['no_kk']) ? $row['no_kk'] : '-';

            $data[] = [
                'id'      => $row['id'],
                'text'    => $row['no_pbp'] . ' - ' . $row['nama_kpm'],
                'no_pbp'  => $row['no_pbp'],
                'no_bast' => $row['no_bast'], // 🚀 Kirim no_bast ke form
                'nik'     => $row['nik_kpm'],
                'nama'    => $row['nama_kpm'],
                'no_kk'   => $no_kk_aman
            ];
        }

        return $this->response->setJSON(['results' => $data]);
    }

    // Fungsi 2: Menyimpan data manual ke tabel Reject
    public function simpanRejectManual()
    {
        $rejectModel = new \App\Models\Dtsen\BanpangRejectModel();

        // Tangkap data dari form
        $no_pbp        = $this->request->getPost('no_pbp');
        $no_bast       = $this->request->getPost('no_bast');
        $no_kk         = $this->request->getPost('no_kk');
        $nik           = $this->request->getPost('nik');
        $nama          = $this->request->getPost('nama');
        $catatan       = $this->request->getPost('catatan');
        $alokasi_bulan = $this->request->getPost('alokasi_bulan');
        $alokasi_tahun = $this->request->getPost('alokasi_tahun');

        $cekData = $rejectModel->where('no_pbp', $no_pbp)->first();
        if ($cekData) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No. PBP ini sudah ada di dalam daftar Reject.'
            ]);
        }

        $rejectModel->insert([
            'no_pbp'          => $no_pbp,
            'no_bast'         => $no_bast, // 🚀 Masukkan No. BAST
            'no_kk'           => $no_kk,
            'nik'             => $nik,
            'nama'            => $nama,
            'alamat_pbp'      => $this->request->getPost('alamat_pbp') ?: '-',
            'alokasi_bulan'   => $alokasi_bulan, // 🚀 Masukkan Bulan
            'alokasi_tahun'   => $alokasi_tahun, // 🚀 Masukkan Tahun
            // 🚀 Ambil wilayah otomatis dari Session Admin
            'provinsi'        => session()->get('kode_prov'),
            'kabupaten'       => session()->get('kode_kab'),
            'kecamatan'       => session()->get('kode_kec'),
            'kelurahan'       => session()->get('kode_desa'),
            'catatan'         => $catatan,
            'is_redocumented' => 0
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data KPM berhasil dimasukkan ke daftar Reject.'
        ]);
    }
}
