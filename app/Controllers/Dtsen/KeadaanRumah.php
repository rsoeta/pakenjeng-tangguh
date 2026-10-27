<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Models\Dtsen\KeadaanRumahModel;
use App\Models\GenModel;
use App\Traits\WilayahFilterTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KeadaanRumah extends BaseController
{
    use WilayahFilterTrait;

    protected $db;
    protected $authModel;
    protected $keadaanModel;
    protected $genModel;

    public function __construct()
    {
        $this->db           = \Config\Database::connect();
        $this->authModel    = new AuthModel();
        $this->keadaanModel = new KeadaanRumahModel();
        $this->genModel     = new GenModel();

        helper(['opdtks_helper', 'opdtsen_helper', 'dtsen_helper']);
    }

    /**
     * 📋 HALAMAN UTAMA: TABEL DATA KEADAAN RUMAH
     */
    public function index()
    {
        $session  = session();
        $userInfo = $this->authModel->getUserId();

        $userData = array_merge([
            'nama'          => $session->get('fullname') ?? '-',
            'username'      => $session->get('username') ?? '-',
            'kode_desa'     => $session->get('kode_desa') ?? '',
            'role_id'       => (int)($session->get('role_id') ?? 99),
            'level'         => $session->get('level') ?? '',
            'wilayah_tugas' => $session->get('wilayah_tugas') ?? '',
            'nik'           => $session->get('nik') ?? '',
        ], is_array($userInfo) ? $userInfo : []);

        $kodeDesa     = $userData['kode_desa'];
        $roleId       = (int)$userData['role_id'];
        $rwUser       = $userData['level'];
        $wilayahTugas = $userData['wilayah_tugas'];

        // Karantina RW untuk Petugas (role_id >= 4)
        if ($roleId >= 4) {
            $listRw = self::getAllowedRwList($wilayahTugas, $rwUser);
        } else {
            $rwBuilder = $this->db->table('dtsen_rt')
                ->select('rw')
                ->where('rw IS NOT NULL')
                ->where("rw != ''");

            if (!empty($kodeDesa)) {
                $rwBuilder->where('kode_desa', $kodeDesa);
            }

            $rawRw = $rwBuilder->groupBy('rw')->orderBy('rw', 'ASC')->get()->getResultArray();
            $listRw = array_column($rawRw, 'rw');
        }

        // Ringkasan Statistik Awal dengan Karantina Wilayah
        $filterSummary = [
            'kode_desa'     => $kodeDesa,
            'wilayah_tugas' => $wilayahTugas,
            'level'         => $rwUser,
            'role_id'       => $roleId
        ];
        $summary = $this->keadaanModel->getGrafikSummary($filterSummary, $roleId);

        $initKategori = trim((string)($this->request->getGet('kategori') ?? ''));
        $initRw       = trim((string)($this->request->getGet('rw') ?? ''));
        $initRt       = trim((string)($this->request->getGet('rt') ?? ''));

        $data = [
            'title'         => 'Tempat Tinggal / Keadaan Rumah',
            'namaApp'       => nameApp(),
            'user_login'    => $userData,
            'list_rw'       => $listRw,
            'summary'       => $summary,
            'init_kategori' => $initKategori,
            'init_rw'       => $initRw,
            'init_rt'       => $initRt,
            'segment'       => 'tabel'
        ];

        return view('dtsen/keadaan_rumah/index', $data);
    }

    /**
     * 📊 DATA AJAX SERVER-SIDE UNTUK DATATABLES
     */
    public function getData()
    {
        try {
            $session  = session();
            $userInfo = $this->authModel->getUserId();

            $kodeDesa     = $session->get('kode_desa') ?? ($userInfo['kode_desa'] ?? '');
            $roleId       = (int)($session->get('role_id') ?? ($userInfo['role_id'] ?? 99));
            $rwUser       = $session->get('level') ?? ($userInfo['level'] ?? '');
            $wilayahTugas = $session->get('wilayah_tugas') ?? ($userInfo['wilayah_tugas'] ?? '');

            $draw    = intval($this->request->getPost('draw') ?? 1);
            $start   = intval($this->request->getPost('start') ?? 0);
            $length  = intval($this->request->getPost('length') ?? 10);
            $search  = trim((string)($this->request->getPost('search')['value'] ?? ''));

            // 🔐 Kunci wilayah untuk role kewilayahan (RT/RW) via WilayahFilterTrait
            $filter = [
                'kode_desa'     => $kodeDesa,
                'rw'            => $this->request->getPost('filter_rw'),
                'rt'            => $this->request->getPost('filter_rt'),
                'kategori'      => $this->request->getPost('filter_kategori'),
                'sumber'        => $this->request->getPost('filter_sumber'),
                'search'        => $search,
                'wilayah_tugas' => $wilayahTugas,
                'level'         => $rwUser,
                'role_id'       => $roleId
            ];

            // Order by column
            $orderParams = $this->request->getPost('order')[0] ?? [];
            $orderColIdx = intval($orderParams['column'] ?? 0);
            $orderDir    = $orderParams['dir'] ?? 'DESC';

            $columnsMap = [
                0 => 'kk.id_kk',
                1 => 'kk.kepala_keluarga',
                2 => 'kk.no_kk',
                3 => 'kk.alamat',
                4 => 'rt.rw',
                5 => 'rt.rt',
                6 => 'kr.status_kepemilikan',
                7 => 'kr.kategori_rumah',
                8 => 'kr.sumber_penilaian',
                9 => 'kr.updated_at'
            ];
            $orderCol = $columnsMap[$orderColIdx] ?? 'kk.id_kk';

            $totalRecords    = $this->keadaanModel->countTotal($filter, $roleId);
            $filteredRecords = $this->keadaanModel->countFiltered($filter, $roleId);
            $rows            = $this->keadaanModel->getDatatableData($filter, $start, $length, $orderCol, $orderDir, $roleId);

            $data = [];
            $no   = $start + 1;

            foreach ($rows as $r) {
                // Badge Kategori Keadaan Rumah
                $kategori = $r['kategori_rumah'];
                $badgeKategori = '';

                switch ($kategori) {
                    case 'Permanen':
                        $badgeKategori = '<span class="badge bg-teal text-white px-2 py-1" style="background-color: #169B88 !important;"><i class="fas fa-building me-1"></i> Permanen</span>';
                        break;
                    case 'Semi Permanen':
                        $badgeKategori = '<span class="badge bg-info text-white px-2 py-1" style="background-color: #3D82A9 !important;"><i class="fas fa-home me-1"></i> Semi Permanen</span>';
                        break;
                    case 'Panggung':
                        $badgeKategori = '<span class="badge bg-olive text-white px-2 py-1" style="background-color: #9EAF22 !important;"><i class="fas fa-campground me-1"></i> Panggung</span>';
                        break;
                    case 'Numpang':
                        $badgeKategori = '<span class="badge bg-danger text-white px-2 py-1" style="background-color: #D84F2A !important;"><i class="fas fa-users me-1"></i> Numpang</span>';
                        break;
                    default:
                        $badgeKategori = '<span class="badge bg-warning text-dark px-2 py-1" style="background-color: #F59100 !important; color: #fff !important;"><i class="fas fa-question-circle me-1"></i> Belum Dinilai</span>';
                        break;
                }

                // Badge Sumber Penilaian
                $sumber = $r['sumber_penilaian'];
                $badgeSumber = '';
                if ($sumber === 'manual') {
                    $badgeSumber = '<span class="badge bg-secondary" title="Dinilai Manual"><i class="fas fa-user-edit"></i> Manual</span>';
                } elseif ($sumber === 'otomatis') {
                    $badgeSumber = '<span class="badge bg-light text-dark border" title="Auto-Detect Sistem"><i class="fas fa-robot text-primary"></i> Otomatis</span>';
                } else {
                    $badgeSumber = '<span class="text-muted small">-</span>';
                }

                // Rangkuman Fisik
                $fisikArr = [];
                if (!empty($r['jenis_lantai']))  $fisikArr[] = 'L: ' . esc($r['jenis_lantai']);
                if (!empty($r['jenis_dinding'])) $fisikArr[] = 'D: ' . esc($r['jenis_dinding']);
                if (!empty($r['jenis_atap']))    $fisikArr[] = 'A: ' . esc($r['jenis_atap']);
                $ringkasanFisik = !empty($fisikArr) ? implode(' | ', $fisikArr) : '<span class="text-muted fst-italic">Belum diisi</span>';

                // Tombol Aksi
                $btnEdit = '<button class="btn btn-xs btn-outline-primary btn-edit-keadaan" 
                                data-id="' . $r['id_kk'] . '" 
                                data-nama="' . esc($r['kepala_keluarga']) . '" 
                                data-kk="' . esc($r['no_kk']) . '" 
                                data-kategori="' . esc($kategori ?? '') . '" 
                                data-pemilikan="' . esc($r['status_kepemilikan'] ?? '') . '" 
                                data-lantai="' . esc($r['jenis_lantai'] ?? '') . '" 
                                data-dinding="' . esc($r['jenis_dinding'] ?? '') . '" 
                                data-atap="' . esc($r['jenis_atap'] ?? '') . '" 
                                data-catatan="' . esc($r['catatan'] ?? '') . '" 
                                title="Ubah / Sesuaikan Penilaian">
                                <i class="fas fa-edit"></i>
                            </button>';

                $btnDetail = '<a href="' . base_url('pembaruan-keluarga/detail/' . encode_id($r['id_kk'])) . '" target="_blank" class="btn btn-xs btn-outline-secondary" title="Lihat Detail Tab Rumah">
                                <i class="fas fa-external-link-alt"></i>
                              </a>';

                $data[] = [
                    'no'                 => $no++,
                    'id_kk'              => $r['id_kk'],
                    'kepala_keluarga'    => '<strong>' . esc($r['kepala_keluarga']) . '</strong>',
                    'no_kk'              => esc($r['no_kk']),
                    'alamat'             => esc($r['alamat'] ?: '-'),
                    'wilayah'            => 'RW ' . esc($r['rw'] ?: '00') . ' / RT ' . esc($r['rt'] ?: '00'),
                    'status_kepemilikan' => esc($r['status_kepemilikan'] ?: '-'),
                    'fisik'              => '<small>' . $ringkasanFisik . '</small>',
                    'kategori_rumah'     => $badgeKategori,
                    'sumber_penilaian'   => $badgeSumber,
                    'aksi'               => '<div class="btn-group">' . $btnEdit . $btnDetail . '</div>'
                ];
            }

            return $this->response->setJSON([
                'draw'            => $draw,
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $data
            ]);
        } catch (\Throwable $e) {
            log_message('error', '❌ getData KeadaanRumah error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw'            => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage()
            ]);
        }
    }

    /**
     * 🥧 HALAMAN PIE GRAPH IDENTIK DENGAN TEMPLATE
     */
    public function grafik()
    {
        $session  = session();
        $userInfo = $this->authModel->getUserId();

        $userData = array_merge([
            'nama'          => $session->get('fullname') ?? '-',
            'username'      => $session->get('username') ?? '-',
            'kode_desa'     => $session->get('kode_desa') ?? '',
            'role_id'       => (int)($session->get('role_id') ?? 99),
            'level'         => $session->get('level') ?? '',
            'wilayah_tugas' => $session->get('wilayah_tugas') ?? '',
            'nik'           => $session->get('nik') ?? '',
        ], is_array($userInfo) ? $userInfo : []);

        $kodeDesa     = $userData['kode_desa'];
        $roleId       = (int)$userData['role_id'];
        $rwUser       = $userData['level'];
        $wilayahTugas = $userData['wilayah_tugas'];

        // Karantina RW untuk Petugas (role_id >= 4)
        if ($roleId >= 4) {
            $listRw = self::getAllowedRwList($wilayahTugas, $rwUser);
        } else {
            $rwBuilder = $this->db->table('dtsen_rt')
                ->select('rw')
                ->where('rw IS NOT NULL')
                ->where("rw != ''");

            if (!empty($kodeDesa)) {
                $rwBuilder->where('kode_desa', $kodeDesa);
            }

            $rawRw = $rwBuilder->groupBy('rw')->orderBy('rw', 'ASC')->get()->getResultArray();
            $listRw = array_column($rawRw, 'rw');
        }

        // Data Grafik Awal dengan Karantina Wilayah Petugas
        $filterStats = [
            'kode_desa'     => $kodeDesa,
            'wilayah_tugas' => $wilayahTugas,
            'level'         => $rwUser,
            'role_id'       => $roleId
        ];
        $stats = $this->keadaanModel->getGrafikSummary($filterStats, $roleId);

        // Ambil Nama Desa
        $namaDesa = '';
        if (!empty($kodeDesa)) {
            $village = $this->db->table('tb_villages')
                ->select('name')
                ->where('id', $kodeDesa)
                ->get()
                ->getRowArray();
            if (!empty($village['name'])) {
                $namaDesa = 'Desa ' . ucwords(strtolower(trim($village['name'])));
            }
        }

        // Susun Keterangan Wilayah Kerja sesuai Role Pengguna
        $keteranganWilayah = '';
        if ($roleId >= 4) {
            // role_id = 4: Keterangan lengkap Wilayah Kerja mencakup RT, RW, dan Desa
            $wilayahHumanis = tampilWilayahHumanis($wilayahTugas);
            if ($wilayahHumanis === '-' && !empty($rwUser)) {
                $wilayahHumanis = 'RW ' . $rwUser;
            }
            $partWilayah = ($wilayahHumanis !== '-') ? $wilayahHumanis . ' - ' : '';
            $keteranganWilayah = $partWilayah . ($namaDesa ?: 'Desa');
        } elseif ($roleId == 3) {
            // role_id = 3: Cukup Wilayah Desa saja
            $keteranganWilayah = $namaDesa ?: 'Wilayah Desa';
        } else {
            // role lainnya (Admin Desa / Superadmin)
            $keteranganWilayah = $namaDesa ?: 'Seluruh Wilayah Desa';
        }

        $data = [
            'title'              => 'Pie Graph Keadaan Rumah',
            'namaApp'            => nameApp(),
            'user_login'         => $userData,
            'list_rw'            => $listRw,
            'stats'              => $stats,
            'keterangan_wilayah' => $keteranganWilayah,
            'nama_desa'          => $namaDesa,
            'segment'            => 'grafik'
        ];

        return view('dtsen/keadaan_rumah/grafik', $data);
    }

    /**
     * 🔄 AJAX API: DATA UNTUK PIE GRAPH REAL-TIME
     */
    public function getGrafikData()
    {
        $session  = session();
        $userInfo = $this->authModel->getUserId();

        $kodeDesa     = $session->get('kode_desa') ?? ($userInfo['kode_desa'] ?? '');
        $roleId       = (int)($session->get('role_id') ?? ($userInfo['role_id'] ?? 99));
        $rwUser       = $session->get('level') ?? ($userInfo['level'] ?? '');
        $wilayahTugas = $session->get('wilayah_tugas') ?? ($userInfo['wilayah_tugas'] ?? '');

        // 🔐 Kunci wilayah untuk role kewilayahan (RT/RW)
        $filter = [
            'kode_desa'     => $kodeDesa,
            'rw'            => $this->request->getGet('rw'),
            'rt'            => $this->request->getGet('rt'),
            'wilayah_tugas' => $wilayahTugas,
            'level'         => $rwUser,
            'role_id'       => $roleId
        ];

        $summary = $this->keadaanModel->getGrafikSummary($filter, $roleId);

        return $this->response->setJSON($summary);
    }

    /**
     * 💾 SIMPAN / EDIT MANUAL KEADAAN RUMAH
     */
    public function save()
    {
        try {
            $idKk      = (int)$this->request->getPost('id_kk');
            $kategori  = $this->request->getPost('kategori_rumah');
            $pemilikan = $this->request->getPost('status_kepemilikan');
            $lantai    = $this->request->getPost('jenis_lantai');
            $dinding   = $this->request->getPost('jenis_dinding');
            $atap      = $this->request->getPost('jenis_atap');
            $catatan   = $this->request->getPost('catatan');
            $nikUser   = session()->get('nik') ?? 'admin';

            if (!$idKk) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ID Keluarga tidak valid.']);
            }

            $existing = $this->db->table('dtsen_keadaan_rumah')->where('id_kk', $idKk)->get()->getRowArray();

            $dataSave = [
                'id_kk'              => $idKk,
                'status_kepemilikan' => !empty($pemilikan) ? $pemilikan : null,
                'jenis_lantai'       => !empty($lantai) ? $lantai : null,
                'jenis_dinding'      => !empty($dinding) ? $dinding : null,
                'jenis_atap'         => !empty($atap) ? $atap : null,
                'kategori_rumah'     => !empty($kategori) ? $kategori : null,
                'sumber_penilaian'   => 'manual',
                'catatan'            => !empty($catatan) ? $catatan : null,
                'updated_by'         => $nikUser,
                'updated_at'         => date('Y-m-d H:i:s')
            ];

            if ($existing) {
                $this->db->table('dtsen_keadaan_rumah')->where('id_kk', $idKk)->update($dataSave);
            } else {
                $dataSave['created_by'] = $nikUser;
                $dataSave['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('dtsen_keadaan_rumah')->insert($dataSave);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Keadaan rumah berhasil disimpan.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    /**
     * ⚡ AUTO-DETECT MASAL
     */
    public function autoDetectAll()
    {
        try {
            $kodeDesa = session()->get('kode_desa');
            $count = $this->keadaanModel->autoDetectAll($kodeDesa);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Proses Auto-Detect berhasil memproses {$count} data keluarga."
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal Auto-Detect: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🟢 EKSPOR EXCEL (PhpSpreadsheet)
     */
    public function exportExcel()
    {
        try {
            $session  = session();
            $userInfo = $this->authModel->getUserId();

            $kodeDesa     = $session->get('kode_desa') ?? ($userInfo['kode_desa'] ?? '');
            $roleId       = (int)($session->get('role_id') ?? ($userInfo['role_id'] ?? 99));
            $rwUser       = $session->get('level') ?? ($userInfo['level'] ?? '');
            $wilayahTugas = $session->get('wilayah_tugas') ?? ($userInfo['wilayah_tugas'] ?? '');

            // 🔐 Kunci wilayah untuk role kewilayahan (RT/RW)
            $filter = [
                'kode_desa'     => $kodeDesa,
                'rw'            => $this->request->getGet('rw'),
                'rt'            => $this->request->getGet('rt'),
                'kategori'      => $this->request->getGet('kategori'),
                'sumber'        => $this->request->getGet('sumber'),
                'search'        => $this->request->getGet('search'),
                'wilayah_tugas' => $wilayahTugas,
                'level'         => $rwUser,
                'role_id'       => $roleId
            ];

            $data = $this->keadaanModel->getAllForExport($filter, $roleId);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Keadaan Rumah');

            // 1. JUDUL LAPORAN
            $sheet->mergeCells('A1:L1');
            $sheet->setCellValue('A1', 'DATA KEADAAN TEMPAT TINGGAL / RUMAH KELUARGA (SINDEN)');
            $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $subTitle = 'Desa: ' . ($kodeDesa ?: 'Semua') . ' | Tanggal Cetak: ' . date('d-m-Y H:i');
            if (!empty($filter['rw'])) $subTitle .= ' | RW: ' . $filter['rw'];
            if (!empty($filter['rt'])) $subTitle .= ' | RT: ' . $filter['rt'];
            if (!empty($filter['kategori'])) $subTitle .= ' | Kategori: ' . $filter['kategori'];

            $sheet->mergeCells('A2:L2');
            $sheet->setCellValue('A2', $subTitle);
            $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // 2. HEADER TABEL
            $headers = [
                'A4' => 'NO',
                'B4' => 'NO KK',
                'C4' => 'KEPALA KELUARGA',
                'D4' => 'NIK KEPALA KELUARGA',
                'E4' => 'ALAMAT',
                'F4' => 'RW',
                'G4' => 'RT',
                'H4' => 'STATUS KEPEMILIKAN',
                'I4' => 'JENIS LANTAI',
                'J4' => 'JENIS DINDING',
                'K4' => 'JENIS ATAP',
                'L4' => 'KATEGORI RUMAH',
                'M4' => 'SUMBER PENILAIAN',
                'N4' => 'CATATAN'
            ];

            foreach ($headers as $cell => $val) {
                $sheet->setCellValue($cell, $val);
            }

            $headerRange = 'A4:N4';
            $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F497D');
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // 3. ISI DATA
            $rowNum = 5;
            $no = 1;
            foreach ($data as $row) {
                $sheet->setCellValue('A' . $rowNum, $no++);
                $sheet->setCellValueExplicit('B' . $rowNum, $row['no_kk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowNum, $row['kepala_keluarga']);
                $sheet->setCellValueExplicit('D' . $rowNum, $row['nik_kepala_keluarga'] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('E' . $rowNum, $row['alamat'] ?? '-');
                $sheet->setCellValueExplicit('F' . $rowNum, $row['rw'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('G' . $rowNum, $row['rt'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('H' . $rowNum, $row['status_kepemilikan'] ?? '-');
                $sheet->setCellValue('I' . $rowNum, $row['jenis_lantai'] ?? '-');
                $sheet->setCellValue('J' . $rowNum, $row['jenis_dinding'] ?? '-');
                $sheet->setCellValue('K' . $rowNum, $row['jenis_atap'] ?? '-');
                $sheet->setCellValue('L' . $rowNum, $row['kategori_rumah'] ?? 'Belum Dinilai');
                $sheet->setCellValue('M' . $rowNum, ucfirst($row['sumber_penilaian'] ?? '-'));
                $sheet->setCellValue('N' . $rowNum, $row['catatan'] ?? '');

                // Alignment untuk kolom nomor, RW, RT, kategori
                $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $rowNum++;
            }

            // 4. BORDER & AUTOSIZE
            $lastRow = $rowNum - 1;
            if ($lastRow >= 4) {
                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC'],
                        ],
                    ],
                ];
                $sheet->getStyle('A4:N' . $lastRow)->applyFromArray($borderStyle);
            }

            foreach (range('A', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // 5. STREAM OUTPUT
            $fileName = 'Data_Keadaan_Rumah_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            log_message('error', '❌ exportExcel KeadaanRumah error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor Excel: ' . $e->getMessage());
        }
    }

    /**
     * 🌐 API LIST RT BERDASARKAN RW (DILENGKAPI BATASAN WILAYAH TUGAS PETUGAS)
     */
    public function getRTByRW($rw)
    {
        $session  = session();
        $userInfo = $this->authModel->getUserId();

        $kodeDesa     = $session->get('kode_desa') ?? ($userInfo['kode_desa'] ?? '');
        $roleId       = (int)($session->get('role_id') ?? ($userInfo['role_id'] ?? 99));
        $rwUser       = $session->get('level') ?? ($userInfo['level'] ?? '');
        $wilayahTugas = $session->get('wilayah_tugas') ?? ($userInfo['wilayah_tugas'] ?? '');

        $rwVariants = self::getWilayahVariants($rw);

        $query = $this->db->table('dtsen_rt')
            ->select('rt')
            ->whereIn('rw', $rwVariants)
            ->where('rt IS NOT NULL')
            ->where("rt != ''");

        if (!empty($kodeDesa)) {
            $query->where('kode_desa', $kodeDesa);
        }

        // 🔐 Kunci RT untuk Role Kewilayahan (role_id >= 4)
        if ($roleId >= 4 && !empty($wilayahTugas)) {
            $allowedRts = self::getAllowedRtList($wilayahTugas, $rw, $rwUser);
            if (!empty($allowedRts)) {
                $allRtVariants = [];
                foreach ($allowedRts as $art) {
                    $allRtVariants = array_merge($allRtVariants, self::getWilayahVariants($art));
                }
                $query->whereIn('rt', array_values(array_unique($allRtVariants)));
            } else {
                // Petugas tidak memiliki tugas di RW ini
                return $this->response->setJSON([]);
            }
        }

        $result = $query->groupBy('rt')->orderBy('rt', 'ASC')->get()->getResult();

        return $this->response->setJSON($result);
    }
}
