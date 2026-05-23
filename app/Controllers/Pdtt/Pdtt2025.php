<?php

namespace App\Controllers\Pdtt;

use App\Controllers\BaseController;
use App\Models\Dtsen\Pdtt2025Model;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Pdtt2025 extends BaseController
{
    protected $pdttModel;

    public function __construct()
    {
        $this->pdttModel = new Pdtt2025Model();
    }

    // 🌐 Halaman Utama PDTT 2025
    public function index()
    {
        $roleId = session()->get('role_id') ?? 99;

        $data = [
            'title'    => 'Verivali PDTT 2025',
            'roleId'   => $roleId,
            'editable' => ($roleId <= 4)
        ];

        // Pastikan Kang Rian membuat file views/pdtt/2025/index.php setelah ini
        return view('pdtt/2025/index', $data);
    }

    // 📥 Fitur Import Excel (Hanya untuk Admin / Role <= 3)
    public function importExcel()
    {
        $roleId = session()->get('role_id');

        // 🚀 BUG FIX: Kunci rapat-rapat! Hanya Role <= 3 yang boleh eksekusi import
        if ($roleId > 3) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses Ditolak: Hanya Admin yang diizinkan untuk melakukan import data!']);
        }

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid.']);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($sheetData); // Hapus Header

            $insertData = [];
            foreach ($sheetData as $row) {
                // 🚀 SILVER BULLET: Hapus semua karakter aneh/invisible dari NIK & KK agar JOIN Sinden sukses!
                $nikBersih = preg_replace('/[^0-9]/', '', $row['A'] ?? '');
                $kkBersih  = preg_replace('/[^0-9]/', '', $row['B'] ?? '');

                if (empty($nikBersih) || empty($row['D'])) continue;

                $insertData[] = [
                    'nik'              => $nikBersih,
                    'no_kk'            => $kkBersih,
                    'no_rekening'      => trim($row['C'] ?? ''),
                    'nama_pengurus'    => trim($row['D'] ?? ''),
                    'lembaga_penyalur' => trim($row['E'] ?? ''),
                    'kode_wilayah'     => trim($row['F'] ?? ''),
                    'prov'             => trim($row['G'] ?? ''),
                    'kab'              => trim($row['H'] ?? ''),
                    'kec'              => trim($row['I'] ?? ''),
                    'kel'              => trim($row['J'] ?? ''),
                    'alamat'           => trim($row['K'] ?? ''),
                    'rt'               => str_pad(trim($row['L'] ?? ''), 3, '0', STR_PAD_LEFT),
                    'rw'               => str_pad(trim($row['M'] ?? ''), 3, '0', STR_PAD_LEFT),
                    'keterangan'       => trim($row['N'] ?? ''),
                ];
            }

            if (!empty($insertData)) {
                $this->pdttModel->insertBatch($insertData);
                return $this->response->setJSON(['status' => 'success', 'message' => count($insertData) . ' Data diimport!']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Excel kosong/tidak valid.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function datatable()
    {
        $request = \Config\Services::request();
        $session = session();

        $filters = [
            'rw'                => $request->getPost('filter_rw'),
            'rt'                => $request->getPost('filter_rt'),
            'status_verifikasi' => $request->getPost('filter_status'),
            'search'            => $request->getPost('search')['value'] ?? '',
            'role_id'           => $session->get('role_id'),
            'wilayah_tugas'     => $session->get('wilayah_tugas'),
            'kode_desa'         => $session->get('kode_desa'),
            'order'             => $request->getPost('order')
        ];

        // 1. Ambil Builder dari Model
        $builder = $this->pdttModel->getDatatablesQuery($filters);

        // 2. Clone untuk menghitung total tanpa limit/offset
        $countBuilder = clone $builder;

        // Menggunakan count() langsung pada hasil query yang di-grouping
        // Ini cara paling aman di CI4 untuk query kompleks
        $totalRecords = count($countBuilder->get()->getResultArray());

        // 3. Terapkan limit/offset untuk pagination di builder utama
        $start  = $request->getPost('start');
        $length = $request->getPost('length');

        if ($length != -1) {
            $builder->limit($length, $start);
        }

        // 4. Eksekusi Query utama
        $query = $builder->get()->getResultArray();

        // ... (sisanya isi $data[] tetap sama)
        $data = [];
        $no = $start + 1;

        // 🛡️ Fungsi Sensor Masking
        $maskNumber = function ($number, $type) {
            $number = trim($number ?? '');
            if (empty($number) || $number === '-') return esc($number);
            $full = esc($number);
            $len = strlen($full);
            $btnClass = ($type === 'nik') ? 'btnCopyNik' : 'btnCopyNoKK';
            $btnTitle = ($type === 'nik') ? 'Salin NIK' : 'Salin No KK';
            $masked = ($len <= 8) ? $full : substr($full, 0, 8) . str_repeat('*', $len - 8);
            $hoverAttr = ' onmouseenter="this.innerText=\'' . $full . '\'" onmouseleave="this.innerText=\'' . $masked . '\'" ';

            return '
            <div class="d-flex justify-content-between align-items-center gap-2">
                <span style="display: none;">' . $full . '</span>
                <span class="text-primary fw-bold text-nowrap" style="cursor:pointer;"' . $hoverAttr . '>' . $masked . '</span>
                <button type="button" class="btn btn-outline-secondary btn-xs ' . $btnClass . ' py-0 px-1" data-value="' . $full . '" title="' . $btnTitle . '">
                    <i class="fas fa-copy"></i>
                </button>
            </div>';
        };

        foreach ($query as $row) {
            $aset = json_decode($row['kepemilikan_aset'] ?? '{}', true);

            $badgeStatus = ($row['status_verifikasi'] === 'Selesai')
                ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-warning text-dark">Pending</span>';

            // 🚀 CEK KELENGKAPAN GROUNDCHECK (4 Pilar)
            $fotoKksVal = !empty($row['foto_kepemilikan']) ? $row['foto_kepemilikan'] : ($row['foto_kks'] ?? '');
            $hasFotoKks = (!empty($fotoKksVal) && $fotoKksVal !== '-' && strpos($fotoKksVal, 'noimage') === false);
            $fotoKks = $hasFotoKks ? '<span class="badge bg-success"><i class="fas fa-check"></i> Ada</span>' : '<span class="badge bg-danger">Kosong</span>';

            $fotoRumahVal = $row['foto_rumah'] ?? '';
            $hasFotoRumah = (!empty($fotoRumahVal) && $fotoRumahVal !== '-' && strpos($fotoRumahVal, 'noimage') === false);
            $fotoRumah = $hasFotoRumah ? '<span class="badge bg-success"><i class="fas fa-check"></i> Ada</span>' : '<span class="badge bg-danger">Kosong</span>';

            $hasKepemilikan = (!empty($row['kepemilikan_rumah']) && $row['kepemilikan_rumah'] !== '-');
            $hasKondisi = (!empty($row['kondisi_rumah']) && $row['kondisi_rumah'] !== '-');

            $isLengkap = $hasFotoKks && $hasFotoRumah && $hasKepemilikan && $hasKondisi;

            // 🚀 TOMBOL DOWNLOAD HANYA MUNCUL JIKA KEDUA FOTO 'ADA'
            $downloadBtn = '';
            if ($hasFotoKks && $hasFotoRumah) {
                $downloadBtn = '<a href="' . base_url('pdtt/2025/download-images/' . $row['id']) . '" class="btn btn-sm btn-warning text-dark me-1" title="Download Foto"><i class="fas fa-download"></i></a>';
            }

            // 🚀 LOGIKA TOMBOL AKSI BERDASARKAN ROLE & KELENGKAPAN
            if ($session->get('role_id') == 5) {
                $btnAction = $downloadBtn . '<span class="badge bg-secondary"><i class="fas fa-eye"></i> Pantau</span>';
            } else {
                if ($row['status_verifikasi'] === 'Selesai') {
                    $btnAction = $downloadBtn . '<button class="btn btn-sm btn-success btn-verifikasi text-nowrap" data-id="' . $row['id'] . '"><i class="fas fa-edit"></i> Edit Verivali</button>';
                } else if (!$isLengkap) {
                    $btnAction = '<button type="button" class="btn btn-sm btn-secondary btn-locked text-nowrap" title="Groundcheck Belum Lengkap!"><i class="fas fa-lock"></i> Terkunci</button>';
                } else {
                    $btnAction = $downloadBtn . '<button class="btn btn-sm btn-primary btn-verifikasi text-nowrap" data-id="' . $row['id'] . '"><i class="fas fa-search"></i> Verifikasi</button>';
                }
            }

            $data[] = [
                $no++,
                esc($row['nama_pengurus']),
                $maskNumber($row['nik'], 'nik'),
                $maskNumber($row['no_kk'], 'nokk'),
                esc($row['alamat']) . ' RT ' . esc($row['rt']) . ' RW ' . esc($row['rw']),
                esc($row['keterangan']),
                $fotoKks,
                esc($row['kepemilikan_rumah'] ?? '-'),
                esc($row['kondisi_rumah'] ?? '-'),
                $fotoRumah,
                esc($aset['mobil'] ?? 0),
                esc($aset['sepeda_motor'] ?? 0),
                esc($row['disabilitas_keluarga'] ?? '-'),
                $badgeStatus,
                $btnAction
            ];
        }

        return $this->response->setJSON([
            'draw'            => $request->getPost('draw'),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data'            => $data
        ]);
    }

    // 🚀 EKSPOR EXCEL (Tugas Role 5)
    public function exportExcel()
    {
        $session = session();
        $roleId = $session->get('role_id');

        // Hanya Admin (1,2,3) dan Auditor (5) yang boleh ekspor
        if ($roleId == 4) {
            return redirect()->back()->with('error', 'Petugas Entri tidak diizinkan mengekspor data.');
        }

        // Ambil data sesuai wilayah tugas / kode desa
        $filters = [
            'role_id'       => $roleId,
            'wilayah_tugas' => $session->get('wilayah_tugas'),
            'kode_desa'     => $session->get('kode_desa'),
        ];

        // Tarik semua data tanpa limit DataTables
        $builder = $this->pdttModel->getDatatablesQuery($filters);
        $dataPdtt = $builder->get()->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header sesuai format pusat
        $headers = [
            'A' => 'NIK_MASK',
            'B' => 'NOKK_MASK',
            'C' => 'NOREKENING_MASK',
            'D' => 'Nama_Pengurus',
            'E' => 'Lembaga_Penyalur',
            'F' => 'KODE_WILAYAH',
            'G' => 'PROV',
            'H' => 'KAB',
            'I' => 'KEC',
            'J' => 'KEL',
            'K' => 'ALAMAT',
            'L' => 'RT',
            'M' => 'RW',
            'N' => 'KETERANGAN',
            'O' => 'Kesesuaian',
            'P' => 'Penjelasan',
            'Q' => 'Foto Listrik',
            'R' => 'Foto KKS',
            'S' => 'Kepemilikan Rumah',
            'T' => 'Kondisi Rumah',
            'U' => 'Foto Rumah',
            'V' => 'Pekerjaan',
            'W' => 'Jenis Usaha',
            'X' => 'Jumlah Penghasilan',
            'Y' => 'Foto Slip Gaji',
            'Z' => 'Jumlah Mobil',
            'AA' => 'Jumlah Motor',
            'AB' => 'Jenis Disabilitas'
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
        }

        $rowNum = 2;
        foreach ($dataPdtt as $row) {
            $aset = json_decode($row['kepemilikan_aset'] ?? '{}', true);
            $fotoKksVal = !empty($row['foto_kepemilikan']) ? $row['foto_kepemilikan'] : ($row['foto_kks'] ?? '');

            // 🚀 ALGORITMA MASKING NIK & NO. KK PUSAT
            $nikAsli = trim($row['nik'] ?? '');
            $nikMasked = (strlen($nikAsli) === 16)
                ? substr($nikAsli, 0, 6) . '******' . substr($nikAsli, 12, 4)
                : $nikAsli; // Jika tidak 16 digit, biarkan apa adanya

            $kkAsli = trim($row['no_kk'] ?? '');
            $kkMasked = (strlen($kkAsli) === 16)
                ? substr($kkAsli, 0, 10) . '******'
                : $kkAsli;

            // 🚀 Set hasil masking ke dalam sel Excel
            $sheet->setCellValueExplicit('A' . $rowNum, $nikMasked, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('B' . $rowNum, $kkMasked, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            // Kolom sisanya...
            $sheet->setCellValueExplicit('C' . $rowNum, trim($row['no_rekening']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $rowNum, trim($row['nama_pengurus']));
            $sheet->setCellValue('E' . $rowNum, trim($row['lembaga_penyalur']));
            $sheet->setCellValueExplicit('F' . $rowNum, trim($row['kode_wilayah']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('G' . $rowNum, trim($row['prov']));
            $sheet->setCellValue('H' . $rowNum, trim($row['kab']));
            $sheet->setCellValue('I' . $rowNum, trim($row['kec']));
            $sheet->setCellValue('J' . $rowNum, trim($row['kel']));
            $sheet->setCellValue('K' . $rowNum, trim($row['alamat']));
            $sheet->setCellValueExplicit('L' . $rowNum, trim($row['rt']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('M' . $rowNum, trim($row['rw']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('N' . $rowNum, trim($row['keterangan']));
            $sheet->setCellValue('O' . $rowNum, trim($row['kesesuaian']));
            $sheet->setCellValue('P' . $rowNum, trim($row['penjelasan']));
            $sheet->setCellValue('Q' . $rowNum, !empty($row['foto_listrik']) ? 'Ada' : 'Kosong');
            $sheet->setCellValue('R' . $rowNum, (!empty($fotoKksVal) && strpos($fotoKksVal, 'noimage') === false) ? 'Ada' : 'Kosong');
            $sheet->setCellValue('S' . $rowNum, trim($row['kepemilikan_rumah'] ?? '-'));
            $sheet->setCellValue('T' . $rowNum, trim($row['kondisi_rumah'] ?? '-'));
            $sheet->setCellValue('U' . $rowNum, (!empty($row['foto_rumah']) && strpos($row['foto_rumah'], 'noimage') === false) ? 'Ada' : 'Kosong');
            $sheet->setCellValue('V' . $rowNum, trim($row['pekerjaan'] ?? '-'));
            $sheet->setCellValue('W' . $rowNum, trim($row['jenis_usaha'] ?? '-'));
            $sheet->setCellValue('X' . $rowNum, $row['jumlah_penghasilan'] > 0 ? $row['jumlah_penghasilan'] : '-');
            $sheet->setCellValue('Y' . $rowNum, !empty($row['foto_slip_gaji']) ? 'Ada' : 'Kosong');
            $sheet->setCellValue('Z' . $rowNum, $aset['mobil'] ?? 0);
            $sheet->setCellValue('AA' . $rowNum, $aset['sepeda_motor'] ?? 0);
            $sheet->setCellValue('AB' . $rowNum, trim($row['disabilitas_keluarga'] ?? '-'));

            $rowNum++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Hasil_Verivali_PDTT_2025_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // 🚀 API Ambil Filter RW Bertingkat & Parsing Wilayah Tugas
    public function getFilterRw()
    {
        $db = \Config\Database::connect();
        $kodeDesa = session()->get('kode_desa');
        $roleId   = session()->get('role_id');
        $wilayahTugas = session()->get('wilayah_tugas') ?? '';

        // 🚀 BUG FIX: isNotNull() diganti dengan where('rw IS NOT NULL')
        $builder = $db->table('dtsen_rt')
            ->select('rw')
            ->distinct()
            ->where('kode_desa', $kodeDesa)
            ->where('rw !=', '')
            ->where('rw IS NOT NULL');

        if ($roleId == 4 && !empty($wilayahTugas)) {
            $wilayah = trim(str_replace('RW:', '', $wilayahTugas));
            $blokRW = preg_split('/[|;]/', $wilayah);
            $allowedRWs = [];
            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if ($blok === '') continue;
                [$rw,] = array_pad(explode(':', $blok), 2, '');

                $baseRw = (string)(int)$rw;
                $allowedRWs[] = $baseRw;
                $allowedRWs[] = str_pad($baseRw, 2, '0', STR_PAD_LEFT);
                $allowedRWs[] = str_pad($baseRw, 3, '0', STR_PAD_LEFT);
            }
            if (!empty($allowedRWs)) $builder->whereIn('rw', $allowedRWs);
        }

        $rwList = $builder->orderBy('rw', 'ASC')->get()->getResultArray();

        // Bersihkan Duplikasi karena Spasi
        $cleanRw = [];
        $seen = [];
        foreach ($rwList as $r) {
            $val = trim($r['rw']);
            if (!in_array($val, $seen) && $val !== '') {
                $seen[] = $val;
                $cleanRw[] = ['rw' => str_pad($val, 3, '0', STR_PAD_LEFT)];
            }
        }
        return $this->response->setJSON(['rw' => $cleanRw]);
    }

    // 🚀 API Ambil Filter RT Bertingkat & Parsing Wilayah Tugas
    public function getFilterRt($rw = null)
    {
        if (!$rw) return $this->response->setJSON(['rt' => []]);

        $db = \Config\Database::connect();
        $kodeDesa = session()->get('kode_desa');
        $roleId   = session()->get('role_id');
        $wilayahTugas = session()->get('wilayah_tugas') ?? '';

        $baseRwSelect = (string)(int)$rw;

        // 🚀 BUG FIX: isNotNull() diganti dengan where('rt IS NOT NULL')
        $builder = $db->table('dtsen_rt')
            ->select('rt')
            ->distinct()
            ->where('kode_desa', $kodeDesa)
            ->where('rt !=', '')
            ->where('rt IS NOT NULL');

        $builder->groupStart()
            ->where('rw', $baseRwSelect)
            ->orWhere('rw', str_pad($baseRwSelect, 2, '0', STR_PAD_LEFT))
            ->orWhere('rw', str_pad($baseRwSelect, 3, '0', STR_PAD_LEFT))
            ->groupEnd();

        if ($roleId == 4 && !empty($wilayahTugas)) {
            $wilayah = trim(str_replace('RW:', '', $wilayahTugas));
            $blokRW = preg_split('/[|;]/', $wilayah);
            $allowedRTs = [];

            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if ($blok === '') continue;
                [$rwTugas, $rtCSV] = array_pad(explode(':', $blok), 2, '');

                if ((string)(int)$rwTugas === $baseRwSelect) {
                    if ($rtCSV) {
                        foreach (explode(',', $rtCSV) as $r) {
                            $baseRt = (string)(int)trim($r);
                            $allowedRTs[] = $baseRt;
                            $allowedRTs[] = str_pad($baseRt, 2, '0', STR_PAD_LEFT);
                            $allowedRTs[] = str_pad($baseRt, 3, '0', STR_PAD_LEFT);
                        }
                    }
                }
            }
            if (!empty($allowedRTs)) $builder->whereIn('rt', $allowedRTs);
        }

        $rtList = $builder->orderBy('rt', 'ASC')->get()->getResultArray();

        $cleanRt = [];
        $seen = [];
        foreach ($rtList as $r) {
            $val = trim($r['rt']);
            if (!in_array($val, $seen) && $val !== '') {
                $seen[] = $val;
                $cleanRt[] = ['rt' => str_pad($val, 3, '0', STR_PAD_LEFT)];
            }
        }
        return $this->response->setJSON(['rt' => $cleanRt]);
    }

    // 🔍 Ambil Detail untuk Modal Verifikasi
    public function getDetail($id)
    {
        $data = $this->pdttModel->find($id);
        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    }

    // 💾 Simpan Hasil Verifikasi PDTT
    public function saveVerifikasi()
    {
        try {
            $id = $this->request->getPost('id');
            if (!$id) throw new \Exception("ID PDTT tidak valid.");

            $penghasilan = $this->request->getPost('jumlah_penghasilan');
            $penghasilan = str_replace(['.', ','], '', $penghasilan ?? '0');

            $dataUpdate = [
                'kesesuaian'         => $this->request->getPost('kesesuaian'),
                'penjelasan'         => $this->request->getPost('penjelasan'),
                'pekerjaan'          => $this->request->getPost('pekerjaan'),
                'jenis_usaha'        => $this->request->getPost('jenis_usaha'),
                'jumlah_penghasilan' => (float) $penghasilan,
                'status_verifikasi'  => 'Selesai',
                'verified_by'        => session()->get('id') ?? session()->get('id_user'),
                'verified_at'        => date('Y-m-d H:i:s')
            ];

            // 📸 Handle Upload Foto Listrik
            $fotoListrik = $this->request->getFile('foto_listrik');
            if ($fotoListrik && $fotoListrik->isValid() && !$fotoListrik->hasMoved()) {
                $newName = $fotoListrik->getRandomName();
                $fotoListrik->move('data/pdtt/2025', $newName);
                $dataUpdate['foto_listrik'] = 'data/pdtt/2025/' . $newName;
            }

            // 📸 Handle Upload Foto Slip Gaji
            $fotoSlip = $this->request->getFile('foto_slip_gaji');
            if ($fotoSlip && $fotoSlip->isValid() && !$fotoSlip->hasMoved()) {
                $newName = $fotoSlip->getRandomName();
                $fotoSlip->move('data/pdtt/2025', $newName);
                $dataUpdate['foto_slip_gaji'] = 'data/pdtt/2025/' . $newName;
            }

            $this->pdttModel->update($id, $dataUpdate);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Hasil verifikasi berhasil disimpan!']);
        } catch (\Throwable $e) {
            log_message('error', '[PDTT Save] ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function exportImages()
    {
        $roleId = session()->get('role_id');
        if ($roleId > 5) return redirect()->back()->with('error', 'Akses ditolak.');

        $dataPdtt = $this->pdttModel->getDatatablesQuery([
            'role_id' => $roleId,
            'wilayah_tugas' => session()->get('wilayah_tugas')
        ])->get()->getResultArray();

        // 🚀 BUG FIX: Pastikan folder writable/temp ada
        $tempDir = WRITEPATH . 'temp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $zip = new \ZipArchive();
        $zipName = 'Foto_PDTT_' . date('Ymd_His') . '.zip';
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipName;

        // 🚀 BUG FIX: Tambahkan flag OVERWRITE agar tidak error jika file sudah ada
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            log_message('error', 'Gagal membuka/membuat file ZIP di: ' . $zipPath);
            return "Gagal membuat ZIP. Pastikan folder writable/temp ada dan bisa ditulis (writable).";
        }

        foreach ($dataPdtt as $row) {
            // Bersihkan nama untuk nama file agar tidak error di Windows
            $nama = preg_replace('/[^a-zA-Z0-9]/', '_', $row['nama_pengurus']);
            $nik  = trim($row['nik']);

            // 1. Foto KKS
            $pathKks = !empty($row['foto_kepemilikan']) ? $row['foto_kepemilikan'] : ($row['foto_kks'] ?? '');
            $fullPathKks = ROOTPATH . 'public/' . ltrim($pathKks, '/'); // ltrim agar path tidak double slash

            if (!empty($pathKks) && file_exists($fullPathKks)) {
                $zip->addFile($fullPathKks, "KKS/{$nik}_{$nama}_KKS.jpg");
            }

            // 2. Foto Rumah
            $pathRumah = $row['foto_rumah'] ?? '';
            $fullPathRumah = ROOTPATH . 'public/' . ltrim($pathRumah, '/');

            if (!empty($pathRumah) && file_exists($fullPathRumah)) {
                $zip->addFile($fullPathRumah, "RUMAH/{$nik}_{$nama}_RUMAH.jpg");
            }
        }
        $zip->close();

        // 🚀 Cek file ada sebelum download
        if (file_exists($zipPath)) {
            return $this->response->download($zipPath, null)->setFileName($zipName);
        } else {
            return "Gagal membuat ZIP: Tidak ada data/foto yang ditemukan.";
        }
    }

    public function downloadImagesPerKpm($id)
    {
        $session = session();

        // 🚀 BUG FIX: Gunakan getDatatablesQuery agar join (foto_rumah, foto_kks) ikut terbawa!
        $filters = [
            'role_id'       => $session->get('role_id'),
            'wilayah_tugas' => $session->get('wilayah_tugas'),
            'kode_desa'     => $session->get('kode_desa')
        ];

        // Ambil data menggunakan Builder yang sudah ada Join-nya
        $row = $this->pdttModel->getDatatablesQuery($filters)->where('p.id', $id)->get()->getRowArray();

        if (!$row) return "Data tidak ditemukan atau Anda tidak memiliki akses ke data ini.";

        $nama = preg_replace('/[^a-zA-Z0-9]/', '_', $row['nama_pengurus']);
        $nik  = trim($row['nik']);

        $tempDir = WRITEPATH . 'temp' . DIRECTORY_SEPARATOR;
        if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);

        $zipName = "Foto_{$nik}_{$nama}.zip";
        $zipPath = $tempDir . $zipName;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return "Gagal membuat antrian ZIP.";
        }

        $fileTersimpan = 0;

        // 1. Ambil Foto KKS (Lokal atau Eksternal/Google Drive)
        $kksPath = !empty($row['foto_kepemilikan']) ? $row['foto_kepemilikan'] : ($row['foto_kks'] ?? '');

        if (!empty($kksPath) && $kksPath !== '-' && strpos($kksPath, 'noimage') === false) {
            if (filter_var($kksPath, FILTER_VALIDATE_URL)) {

                // 🚀 Trik Cerdas: Convert Link G-Drive Viewer menjadi Link Direct Download
                if (strpos($kksPath, 'drive.google.com') !== false) {
                    preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $kksPath, $matches);
                    if (!empty($matches[1])) {
                        $kksPath = 'https://drive.google.com/uc?id=' . $matches[1] . '&export=download';
                    }
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $kksPath);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $imgData = curl_exec($ch);
                curl_close($ch);

                if ($imgData) {
                    $zip->addFromString("KKS_{$nik}.jpg", $imgData);
                    $fileTersimpan++;
                }
            } else {
                // 🚀 FCPATH lebih aman untuk mendeteksi folder public di CI4
                $fullPathKks = FCPATH . ltrim($kksPath, '/');
                if (!file_exists($fullPathKks)) $fullPathKks = ROOTPATH . 'public/' . ltrim($kksPath, '/');

                if (file_exists($fullPathKks)) {
                    $zip->addFile($fullPathKks, "KKS_{$nik}.jpg");
                    $fileTersimpan++;
                }
            }
        }

        // 2. Ambil Foto Rumah (Lokal)
        $rumahPath = $row['foto_rumah'] ?? '';
        if (!empty($rumahPath) && $rumahPath !== '-' && strpos($rumahPath, 'noimage') === false) {
            $fullPathRumah = FCPATH . ltrim($rumahPath, '/');
            if (!file_exists($fullPathRumah)) $fullPathRumah = ROOTPATH . 'public/' . ltrim($rumahPath, '/');

            if (file_exists($fullPathRumah)) {
                $zip->addFile($fullPathRumah, "RUMAH_{$nik}.jpg");
                $fileTersimpan++;
            }
        }

        $zip->close();

        // 🚀 Proteksi: Pastikan ZIP terbuat
        if ($fileTersimpan > 0 && file_exists($zipPath)) {
            return $this->response->download($zipPath, null)->setFileName($zipName);
        } else {
            return "Gagal mengunduh: File foto fisik tidak ditemukan di Server maupun Google Drive.";
        }
    }
}
