<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;
use App\Models\DesaModel;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\JandaModel;
use App\Traits\WilayahFilterTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Janda extends BaseController
{
    use WilayahFilterTrait;

    protected $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        helper(['form', 'url']);
    }

    private function getUserContext(): array
    {
        $session  = session();
        $userInfo = $this->authModel->getUserId();

        return [
            'role_id'       => (int)($session->get('role_id') ?? ($userInfo['role_id'] ?? 4)),
            'kode_desa'     => $session->get('kode_desa') ?? ($userInfo['kode_desa'] ?? '32.05.33.2006'),
            'level'         => $session->get('level') ?? ($userInfo['level'] ?? ''),
            'wilayah_tugas' => $session->get('wilayah_tugas') ?? ($userInfo['wilayah_tugas'] ?? ''),
            'fullname'      => $session->get('fullname') ?? ($userInfo['fullname'] ?? 'Petugas'),
        ];
    }

    public function index()
    {
        return redirect()->to(site_url('lansia-jompo'));
    }

    public function janda_data()
    {
        $user        = $this->getUserContext();
        $roleId      = $user['role_id'];
        $sessionDesa = $user['kode_desa'];

        // Role 1 & 2 (Super Admin / Admin) boleh memilih desa lain lewat filter jika diisi
        $postDesa = $this->request->getPost('desa');
        if ($roleId < 3 && !empty($postDesa)) {
            $kodeDesa = $postDesa;
        } else {
            $kodeDesa = !empty($sessionDesa) ? $sessionDesa : '32.05.33.2006';
        }

        $filter = [
            'kode_desa'     => $kodeDesa,
            'rw'            => $this->request->getPost('rw'),
            'kategori'      => $this->request->getPost('kategori'),
            'search'        => trim((string)($this->request->getPost('search')['value'] ?? '')),
            'wilayah_tugas' => $user['wilayah_tugas'],
            'level'         => $user['level'],
            'role_id'       => $roleId
        ];

        $model = new JandaModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $listing       = $model->get_datatables($filter, $roleId);
        $jumlah_semua  = $model->jumlah_semua($filter, $roleId);
        $jumlah_filter = $model->jumlah_filter($filter, $roleId);

        $totalRecords    = is_object($jumlah_semua) ? ($jumlah_semua->jml ?? 0) : (int)$jumlah_semua;
        $filteredRecords = is_object($jumlah_filter) ? ($jumlah_filter->jml ?? 0) : (int)$jumlah_filter;

        $data = [];
        $no   = (int)($this->request->getPost('start') ?? 0);
        foreach ($listing as $key) {
            $no++;
            $row = [];
            $row[] = $no;

            // Nama & NIK
            $namaCol = '<strong>' . esc($key->nama) . '</strong>';
            if (!empty($key->nik)) {
                $namaCol .= '<br><small class="text-muted"><i class="far fa-id-card mr-1"></i>' . esc($key->nik) . '</small>';
            }
            $row[] = $namaCol;

            // Alamat
            $row[] = esc($key->alamat ?? '-');

            // RT / RW
            $rtRw = esc($key->rt ?? '000') . ' / ' . esc($key->rw ?? '000');
            $row[] = '<span class="badge badge-light border">' . $rtRw . '</span>';

            // Usia
            $row[] = '<span class="badge badge-info">' . esc($key->usia) . ' Thn</span>';

            // Jenis Kelamin
            if ($key->jenis_kelamin === 'L') {
                $row[] = '<span class="badge badge-primary" title="Laki-laki">L</span>';
            } elseif ($key->jenis_kelamin === 'P') {
                $row[] = '<span class="badge badge-danger" title="Perempuan">P</span>';
            } else {
                $row[] = '-';
            }

            // Tempat Tinggal / Keadaan Rumah
            $statusRumah   = (!empty($key->status_kepemilikan) && $key->status_kepemilikan !== '-') ? $key->status_kepemilikan : '';
            $kategoriRumah = (!empty($key->kategori_rumah) && $key->kategori_rumah !== '-') ? $key->kategori_rumah : '';
            if ($statusRumah && $kategoriRumah) {
                $rumahText = $statusRumah . ' / ' . $kategoriRumah;
            } elseif ($statusRumah) {
                $rumahText = $statusRumah;
            } elseif ($kategoriRumah) {
                $rumahText = $kategoriRumah;
            } else {
                $rumahText = '-';
            }
            $row[] = esc($rumahText);

            // Pendidikan Terakhir
            $row[] = esc($key->pendidikan ?? '-');

            // Kategori & Keterangan
            $kat = $key->kategori_lansia === 'Jompo' ? 'Jompo' : 'Lansia';
            if ($kat === 'Jompo') {
                $katBadge = '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-wheelchair mr-1"></i>Jompo</span>';
            } else {
                $katBadge = '<span class="badge badge-success px-2 py-1"><i class="fas fa-user-check mr-1"></i>Lansia</span>';
            }
            if (!empty($key->keterangan_lansia)) {
                $katBadge .= '<div class="small text-muted font-italic mt-1"><i class="fas fa-info-circle mr-1 text-xs"></i>' . esc($key->keterangan_lansia) . '</div>';
            }
            $row[] = $katBadge;

            // Aksi
            $btnAksi = '<button type="button" class="btn btn-xs btn-outline-primary btnPilihKategori" ' .
                'data-id="' . $key->id_art . '" ' .
                'data-nama="' . esc($key->nama, 'attr') . '" ' .
                'data-nik="' . esc($key->nik ?? '', 'attr') . '" ' .
                'data-usia="' . esc($key->usia, 'attr') . '" ' .
                'data-kategori="' . esc($kat, 'attr') . '" ' .
                'data-keterangan="' . esc($key->keterangan_lansia ?? '', 'attr') . '" ' .
                'title="Pilih Kategori Status">' .
                '<i class="fas fa-user-edit mr-1"></i>Pilih Status</button>';
            $row[] = $btnAksi;

            $data[] = $row;
        }

        $output = [
            "draw"            => intval($this->request->getPost('draw') ?? 1),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data"            => $data,
        ];
        $output[$csrfName] = $csrfHash;

        return $this->response->setJSON($output);
    }

    public function save_status()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $id_art     = (int)$this->request->getPost('id_art');
        $kategori   = $this->request->getPost('kategori');
        $keterangan = trim((string)$this->request->getPost('keterangan'));

        if (!$id_art || !in_array($kategori, ['Lansia', 'Jompo'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data tidak valid. Kategori harus Lansia atau Jompo.'
            ]);
        }

        $user     = $this->getUserContext();
        $username = $user['fullname'];

        $model = new JandaModel();
        $saved = $model->save_status_lansia($id_art, $kategori, $keterangan, $username);

        if ($saved) {
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => "Status berhasil diperbarui menjadi {$kategori}.",
                'csrf_name' => csrf_token(),
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menyimpan status kategori.'
        ]);
    }

    public function export_excel()
    {
        $user        = $this->getUserContext();
        $roleId      = $user['role_id'];
        $sessionDesa = $user['kode_desa'];

        $getDesa = $this->request->getGet('desa');
        if ($roleId < 3 && !empty($getDesa)) {
            $kodeDesa = $getDesa;
        } else {
            $kodeDesa = !empty($sessionDesa) ? $sessionDesa : '32.05.33.2006';
        }

        $filter = [
            'kode_desa'     => $kodeDesa,
            'rw'            => $this->request->getGet('rw'),
            'kategori'      => $this->request->getGet('kategori'),
            'search'        => trim((string)($this->request->getGet('search') ?? '')),
            'wilayah_tugas' => $user['wilayah_tugas'],
            'level'         => $user['level'],
            'role_id'       => $roleId
        ];

        $model = new JandaModel();
        $data  = $model->get_export_data($filter, $roleId);

        // Inisialisasi PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lansia Jompo');

        // KOP Sesuai Permintaan
        $sheet->setCellValue('A1', 'DATA LANSIA/JOMPO');
        $sheet->setCellValue('A2', 'DESA PASIRLANGU KECAMATAN PAKENJENG KABUPATEN GARUT');
        $sheet->setCellValue('A3', 'TAHUN 2026');

        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);

        // Header Tabel Kolom (Baris 5)
        $headers = [
            'A5' => 'No',
            'B5' => 'Nama',
            'C5' => 'Alamat',
            'D5' => 'Rt/Rw',
            'E5' => 'Usia (Tahun)',
            'F5' => 'Jenis Kelamin',
            'G5' => 'Tempat Tinggal/Keadaan Rumah',
            'H5' => 'Pendidikan Terakhir',
            'I5' => 'Keterangan'
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E3E5']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A5:I5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(26);

        // Isi Data (Mulai Baris 6)
        $rowNum = 6;
        $no = 1;
        foreach ($data as $item) {
            // Tempat tinggal / keadaan rumah
            $statusRumah   = (!empty($item['status_kepemilikan']) && $item['status_kepemilikan'] !== '-') ? $item['status_kepemilikan'] : '';
            $kategoriRumah = (!empty($item['kategori_rumah']) && $item['kategori_rumah'] !== '-') ? $item['kategori_rumah'] : '';
            if ($statusRumah && $kategoriRumah) {
                $rumahText = $statusRumah . ' / ' . $kategoriRumah;
            } elseif ($statusRumah) {
                $rumahText = $statusRumah;
            } elseif ($kategoriRumah) {
                $rumahText = $kategoriRumah;
            } else {
                $rumahText = '-';
            }

            // Jenis Kelamin
            $jk = ($item['jenis_kelamin'] === 'L') ? 'Laki-laki' : (($item['jenis_kelamin'] === 'P') ? 'Perempuan' : '-');

            // Keterangan
            $kat = $item['kategori_lansia'] ?? 'Lansia';
            $ket = $kat;
            if (!empty($item['keterangan_lansia'])) {
                $ket .= ' (' . $item['keterangan_lansia'] . ')';
            }

            $rtRw = ($item['rt'] ?? '000') . '/' . ($item['rw'] ?? '000');

            $sheet->setCellValue('A' . $rowNum, $no);
            $sheet->setCellValue('B' . $rowNum, strtoupper($item['nama']));
            $sheet->setCellValue('C' . $rowNum, strtoupper($item['alamat']));
            $sheet->setCellValueExplicit('D' . $rowNum, $rtRw, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $rowNum, (int)$item['usia']);
            $sheet->setCellValue('F' . $rowNum, $jk);
            $sheet->setCellValue('G' . $rowNum, $rumahText);
            $sheet->setCellValue('H' . $rowNum, $item['pendidikan']);
            $sheet->setCellValue('I' . $rowNum, $ket);

            $rowNum++;
            $no++;
        }

        // Terapkan border dan format pada isi tabel jika ada data
        $lastRow = max(5, $rowNum - 1);
        if ($lastRow >= 6) {
            $dataStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];
            $sheet->getStyle('A6:I' . $lastRow)->applyFromArray($dataStyle);

            // Alignment spesifik
            $sheet->getStyle('A6:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D6:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Auto size columns A to I
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'DATA_LANSIA_JOMPO_PASIRLANGU_2026.xlsx';

        // Bersihkan output buffer sebelum kirim file
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
