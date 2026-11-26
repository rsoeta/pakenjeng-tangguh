<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Models\Dtsen\LaporanPeneranganModel;
use App\Models\GenModel;
use App\Models\SettingsModel;
use App\Traits\WilayahFilterTrait;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanPenerangan extends BaseController
{
    use WilayahFilterTrait;

    protected $db;
    protected $authModel;
    protected $laporanModel;
    protected $settingsModel;

    public function __construct()
    {
        $this->db            = \Config\Database::connect();
        $this->authModel     = new AuthModel();
        $this->laporanModel  = new LaporanPeneranganModel();
        $this->settingsModel = new SettingsModel();

        helper(['opdtks_helper', 'opdtsen_helper', 'dtsen_helper']);
    }

    /**
     * 🔐 Proteksi akses khusus role_id < 4 (Super Admin, Admin, Operator)
     */
    private function checkAccess(bool $isAjax = false)
    {
        $session = session();
        if (!$session->get('logDtks')) {
            return $isAjax
                ? $this->response->setStatusCode(401)->setJSON(['error' => 'Sesi login berakhir.'])
                : redirect()->to(base_url('logout'));
        }

        $roleId = (int) ($session->get('role_id') ?? 99);
        if ($roleId >= 4) {
            return $isAjax
                ? $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak. Fitur ini khusus Operator/Admin (role_id < 4).'])
                : redirect()->to(base_url('lockscreen'));
        }

        return null;
    }

    /**
     * Ambil konteks user & filter standar
     */
    private function getUserContext(): array
    {
        $session  = session();
        $userInfo = $this->authModel->getUserId();

        return [
            'id'        => $session->get('id') ?? ($userInfo['id'] ?? 0),
            'nama'      => $session->get('fullname') ?? ($userInfo['fullname'] ?? 'Operator'),
            'fullname'  => $session->get('fullname') ?? ($userInfo['fullname'] ?? 'Operator'),
            'username'  => $session->get('username') ?? ($userInfo['username'] ?? '-'),
            'nik'       => $session->get('nik') ?? ($userInfo['nik'] ?? ''),
            'role_id'   => (int) ($session->get('role_id') ?? ($userInfo['role_id'] ?? 99)),
            'kode_desa' => trim((string) ($session->get('kode_desa') ?? ($userInfo['kode_desa'] ?? ''))),
        ];
    }

    private function extractFilterFromRequest(array $user, string $method = 'post'): array
    {
        $getParam = fn(string $key, $default = '') => ($method === 'get')
            ? ($this->request->getGet($key) ?? $default)
            : ($this->request->getPost($key) ?? $default);

        $roleId          = (int) $user['role_id'];
        $sessionKodeDesa = $user['kode_desa'];

        // Operator (role_id = 3) wajib dikunci ke kode_desa miliknya sendiri
        $requestedDesa = trim((string) $getParam('filter_desa', ''));
        if ($roleId >= 3) {
            $kodeDesa = $sessionKodeDesa;
        } else {
            $kodeDesa = ($requestedDesa !== '') ? $requestedDesa : $sessionKodeDesa;
            if ($kodeDesa === '') {
                $kodeDesa = 'all';
            }
        }

        return [
            'kode_desa'         => $kodeDesa,
            'session_kode_desa' => $sessionKodeDesa,
            'rw'                => trim((string) $getParam('filter_rw', 'all')),
            'rt'                => trim((string) $getParam('filter_rt', 'all')),
            'sumber_mode'       => trim((string) $getParam('filter_sumber_mode', 'all')),
            'kategori'          => trim((string) $getParam('filter_kategori', 'all')),
            'daya'              => trim((string) $getParam('filter_daya', 'all')),
            'desil'             => trim((string) $getParam('filter_desil', 'all')),
            'bansos'            => trim((string) $getParam('filter_bansos', 'all')),
        ];
    }

    /**
     * 📋 HALAMAN UTAMA LAPORAN SUMBER PENERANGAN UTAMA
     */
    public function index()
    {
        if ($resp = $this->checkAccess(false)) {
            return $resp;
        }

        $user   = $this->getUserContext();
        $roleId = $user['role_id'];
        $master = $this->laporanModel->getFilterMaster($roleId, $user['kode_desa']);

        // Default desa terpilih di dropdown
        $defaultDesa = ($roleId >= 3) ? $user['kode_desa'] : ($user['kode_desa'] !== '' ? $user['kode_desa'] : 'all');

        // Nama desa user untuk badge keterangan wilayah
        $namaDesaAktif = 'Semua Desa / Kelurahan';
        if ($defaultDesa !== '' && $defaultDesa !== 'all') {
            foreach ($master['desa_list'] as $d) {
                if ($d['kode_desa'] === $defaultDesa) {
                    $namaDesaAktif = 'Desa ' . $d['nama_desa_label'];
                    break;
                }
            }
        }

        return view('dtsen/laporan_penerangan/index', [
            'title'           => 'Laporan Klasifikasi Sumber Penerangan Utama',
            'namaApp'         => nameApp(),
            'user_login'      => $user,
            'statusRole'      => (new GenModel())->getStatusRole(),
            'role_id'         => $roleId,
            'desa_list'       => $master['desa_list'],
            'rw_list'         => $master['rw_list'],
            'default_desa'    => $defaultDesa,
            'nama_desa_aktif' => $namaDesaAktif,
            'kategori_list'   => LaporanPeneranganModel::KATEGORI_LIST,
            'daya_groups'     => LaporanPeneranganModel::DAYA_GROUPS,
        ]);
    }

    /**
     * 📊 AJAX ENDPOINT TUNGGAL: DATATABLES SERVER-SIDE + ANALYTICS SUMMARY
     */
    public function datatable()
    {
        if ($resp = $this->checkAccess(true)) {
            return $resp;
        }

        try {
            $user   = $this->getUserContext();
            $roleId = $user['role_id'];
            $filter = $this->extractFilterFromRequest($user, 'post');

            $draw             = (int) ($this->request->getPost('draw') ?? 1);
            $start            = max(0, (int) ($this->request->getPost('start') ?? 0));
            $length           = (int) ($this->request->getPost('length') ?? 25);
            $search           = trim((string) ($this->request->getPost('search')['value'] ?? ''));
            $includeAnalytics = (int) ($this->request->getPost('include_analytics') ?? 1) === 1;

            $orderParams = $this->request->getPost('order')[0] ?? [];
            $orderColIdx = (int) ($orderParams['column'] ?? 4);
            $orderDir    = strtolower((string) ($orderParams['dir'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';

            // Lepaskan file session lock agar request paralel browser tidak saling blokir
            session_write_close();

            // 1️⃣ Ambil dataset hasil filter utama
            $dataset = $this->laporanModel->getDataset($filter, $roleId);
            $recordsTotal = count($dataset);

            // 2️⃣ Hitung analytics jika diminta (sinkron dengan panel filter utama)
            $analytics = $includeAnalytics ? $this->laporanModel->buildAnalytics($dataset, $filter) : null;

            // 3️⃣ Terapkan pencarian DataTables
            $filteredData = $this->laporanModel->applySearch($dataset, $search);
            $recordsFiltered = count($filteredData);

            // 4️⃣ Pengurutan kolom DataTables
            $orderKeys = [
                0 => 'id_kk',
                1 => 'kepala_keluarga',
                2 => 'no_kk',
                3 => 'alamat',
                4 => 'rw',
                5 => 'sumber_penerangan',
                6 => 'daya_raw',
                7 => 'desil',
                8 => 'bansos',
                9 => 'sumber_data',
            ];
            $sortField = $orderKeys[$orderColIdx] ?? 'rw';

            usort($filteredData, static function ($a, $b) use ($sortField, $orderDir) {
                if ($sortField === 'rw') {
                    $cmp = strnatcasecmp($a['rw'] . '_' . $a['rt'] . '_' . $a['kepala_keluarga'], $b['rw'] . '_' . $b['rt'] . '_' . $b['kepala_keluarga']);
                } elseif ($sortField === 'desil') {
                    $va = $a['desil'] ?? 999;
                    $vb = $b['desil'] ?? 999;
                    $cmp = $va <=> $vb;
                } elseif ($sortField === 'id_kk') {
                    $cmp = $a['id_kk'] <=> $b['id_kk'];
                } else {
                    $cmp = strnatcasecmp((string) ($a[$sortField] ?? ''), (string) ($b[$sortField] ?? ''));
                }
                return ($orderDir === 'desc') ? -$cmp : $cmp;
            });

            // 5️⃣ Potong slice halaman aktif
            $pageRows = ($length > 0) ? array_slice($filteredData, $start, $length) : $filteredData;

            $formatted = [];
            $no = $start + 1;
            foreach ($pageRows as $r) {
                $formatted[] = [
                    'no'                 => $no++,
                    'id_kk'              => $r['id_kk'],
                    'id_kk_encoded'      => encode_id($r['id_kk']),
                    'kepala_keluarga'    => $r['kepala_keluarga'],
                    'no_kk'              => $r['no_kk'],
                    'alamat'             => $r['alamat'],
                    'nama_desa'          => $r['nama_desa'],
                    'rw'                 => $r['rw'],
                    'rt'                 => $r['rt'],
                    'sumber_penerangan'  => $r['sumber_penerangan'],
                    'daya_raw'           => $r['daya_raw'],
                    'daya_group'         => $r['daya_group'],
                    'jumlah_meteran'     => $r['jumlah_meteran'],
                    'nomor_pelanggan'    => $r['nomor_pelanggan'],
                    'nomor_meter'        => $r['nomor_meter'],
                    'pengeluaran_listrik' => $r['pengeluaran_listrik'],
                    'desil'              => $r['desil'],
                    'bansos'             => $r['bansos'],
                    'bansos_tahap'       => $r['bansos_tahap'],
                    'sumber_data'        => $r['sumber_data'],
                ];
            }

            $response = [
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $formatted,
            ];
            if ($analytics !== null) {
                $response['analytics'] = $analytics;
            }

            return $this->response->setJSON($response);
        } catch (\Throwable $e) {
            log_message('error', '❌ LaporanPenerangan::datatable error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw'            => (int) ($this->request->getPost('draw') ?? 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage(),
            ]);
        }
    }

    /**
     * 🌐 AJAX LIST RW BERDASARKAN DESA
     */
    public function getRwByDesa()
    {
        if ($resp = $this->checkAccess(true)) {
            return $resp;
        }

        $user     = $this->getUserContext();
        $roleId   = $user['role_id'];
        $desaReq  = trim((string) ($this->request->getGet('desa') ?? ''));
        $kodeDesa = ($roleId >= 3) ? $user['kode_desa'] : $desaReq;

        session_write_close();

        return $this->response->setJSON($this->laporanModel->getRwListByDesa($kodeDesa));
    }

    /**
     * 🌐 AJAX LIST RT BERDASARKAN RW (+ DESA)
     */
    public function getRtByRw($rw)
    {
        if ($resp = $this->checkAccess(true)) {
            return $resp;
        }

        $user     = $this->getUserContext();
        $roleId   = $user['role_id'];
        $desaReq  = trim((string) ($this->request->getGet('desa') ?? ''));
        $kodeDesa = ($roleId >= 3) ? $user['kode_desa'] : $desaReq;

        session_write_close();

        return $this->response->setJSON($this->laporanModel->getRtListByDesaRw($kodeDesa, (string) $rw));
    }

    /**
     * Bangun metadata wilayah & label filter untuk judul Export Excel dan Cetak Resmi
     */
    private function buildReportMeta(array $filter, array $user): array
    {
        $kodeDesa = $filter['kode_desa'];
        $namaDesa = $this->settingsModel->getSetting('nama_desa') ?? 'Pasirlangu';
        $namaKec  = $this->settingsModel->getSetting('nama_kecamatan') ?? 'Pakenjeng';
        $namaKab  = $this->settingsModel->getSetting('nama_kabupaten') ?? 'Garut';

        $isSemuaDesa = ($kodeDesa === '' || $kodeDesa === 'all');
        if (!$isSemuaDesa) {
            $vil = $this->db->table('tb_villages v')
                ->select('v.name as desa, d.name as kec, r.name as kab')
                ->join('tb_districts d', 'd.id = v.district_id', 'left')
                ->join('tb_regencies r', 'r.id = d.regency_id', 'left')
                ->where('v.id', $kodeDesa)
                ->get()
                ->getRowArray();

            if (!empty($vil['desa'])) {
                $namaDesa = ucwords(strtolower(trim($vil['desa'])));
            }
            if (!empty($vil['kec'])) {
                $namaKec = ucwords(strtolower(trim(str_ireplace('KEC. ', '', $vil['kec']))));
            }
            if (!empty($vil['kab'])) {
                $namaKab = ucwords(strtolower(trim(str_ireplace('KAB. ', '', $vil['kab']))));
            }
        }

        $sumberLabels = [
            'all'         => 'Smart Merge (Usulan Terbaru Verified/Submitted/Draft + Master RT)',
            'verified'    => 'Smart Merge Terverifikasi (Usulan Verified/Submitted + Master RT)',
            'draft_only'  => 'Hanya Usulan Status Draft',
            'master_only' => 'Hanya Data Master Rumah Tangga (dtsen_rt)',
        ];

        // Ambil Profil Lembaga / Desa untuk Kop Surat & Penandatangan Resmi
        $profilDesa = null;
        try {
            if (!$isSemuaDesa) {
                $profilDesa = $this->db->table('lembaga_profil')
                    ->where('lp_kode', $kodeDesa)
                    ->get()
                    ->getRowArray();
            }
            if (empty($profilDesa)) {
                $profilDesa = $this->db->table('lembaga_profil')
                    ->orderBy('lp_id', 'ASC')
                    ->get()
                    ->getRowArray();
            }
        } catch (\Throwable $e) {
            $profilDesa = null;
        }

        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $tanggalIndo = date('d') . ' ' . $bulanIndo[(int) date('n')] . ' ' . date('Y');

        return [
            'is_semua_desa'  => $isSemuaDesa,
            'kode_desa'      => $kodeDesa,
            'nama_desa'      => $namaDesa,
            'nama_kec'       => $namaKec,
            'nama_kab'       => $namaKab,
            'wilayah_label'  => ($isSemuaDesa ? 'Semua Desa Kec. ' . $namaKec : 'Desa ' . $namaDesa)
                . (($filter['rw'] !== '' && $filter['rw'] !== 'all') ? ' | RW ' . $filter['rw'] : '')
                . (($filter['rt'] !== '' && $filter['rt'] !== 'all') ? ' | RT ' . $filter['rt'] : ''),
            'sumber_label'   => $sumberLabels[$filter['sumber_mode']] ?? $sumberLabels['all'],
            'kategori_label' => ($filter['kategori'] !== '' && $filter['kategori'] !== 'all') ? $filter['kategori'] : 'Semua Kategori',
            'daya_label'     => ($filter['daya'] !== '' && $filter['daya'] !== 'all') ? $filter['daya'] : 'Semua Daya',
            'desil_label'    => $this->formatDesilFilterLabel($filter['desil']),
            'bansos_label'   => ($filter['bansos'] !== '' && $filter['bansos'] !== 'all') ? $filter['bansos'] : 'Semua Status Bansos',
            'operator_nama'  => $user['nama'] ?? 'Operator DTSEN',
            'kepala_desa'    => $profilDesa['lp_kepala'] ?? 'Kepala Desa ' . $namaDesa,
            'sekretariat'    => $profilDesa['lp_sekretariat'] ?? "Jl. Desa Km. 200 Kp. Rahayu Desa {$namaDesa} Kec. {$namaKec} Kab. {$namaKab}",
            'email_desa'     => $profilDesa['lp_email'] ?? ('info@' . strtolower(preg_replace('/\s+/', '', $namaDesa)) . '.desa.id'),
            'kode_pos'       => $profilDesa['lp_kode_pos'] ?? '44163',
            'tanggal_indo'   => $tanggalIndo,
            'printed_at'     => date('d/m/Y H:i') . ' WIB',
        ];
    }

    private function formatDesilFilterLabel(string $val): string
    {
        return match ($val) {
            '', 'all' => 'Semua Desil',
            '1_4'     => 'Desil 1–4 (Prioritas)',
            '5_10'    => 'Desil 5–10',
            'belum'   => 'Belum Ada Desil / Desil 0',
            default   => 'Desil ' . $val,
        };
    }

    /**
     * 🟢 EKSPOR LAPORAN EXCEL (.xlsx) MULTI-SHEET
     */
    public function exportExcel()
    {
        if ($resp = $this->checkAccess(false)) {
            return $resp;
        }

        try {
            $user   = $this->getUserContext();
            $roleId = $user['role_id'];
            $filter = $this->extractFilterFromRequest($user, 'get');
            $search = trim((string) ($this->request->getGet('search') ?? ''));

            session_write_close();

            $dataset = $this->laporanModel->getDataset($filter, $roleId);
            if ($search !== '') {
                $dataset = $this->laporanModel->applySearch($dataset, $search);
            }

            $analytics = $this->laporanModel->buildAnalytics($dataset, $filter);
            $meta      = $this->buildReportMeta($filter, $user);

            $spreadsheet = new Spreadsheet();

            // Gaya header seragam
            $headerStyle = [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F5132']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF999999']]],
            ];
            $subHeaderStyle = [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E78']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF999999']]],
            ];
            $borderThin = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ];
            $totalRowStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE9ECEF']],
            ];

            // ============================================================
            // SHEET 1: REKAPITULASI & WILAYAH
            // ============================================================
            $s1 = $spreadsheet->getActiveSheet();
            $s1->setTitle('Rekap Wilayah');

            $s1->mergeCells('A1:K1');
            $s1->setCellValue('A1', 'LAPORAN KLASIFIKASI KELUARGA BERDASARKAN SUMBER PENERANGAN UTAMA');
            $s1->getStyle('A1')->getFont()->setSize(13)->setBold(true);
            $s1->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $s1->mergeCells('A2:K2');
            $s1->setCellValue('A2', 'Wilayah: ' . $meta['wilayah_label'] . ' | Sumber Data: ' . $meta['sumber_label']);
            $s1->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
            $s1->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $s1->mergeCells('A3:K3');
            $s1->setCellValue('A3', 'Filter: ' . $meta['kategori_label'] . ' | ' . $meta['daya_label'] . ' | ' . $meta['desil_label'] . ' | ' . $meta['bansos_label'] . ' | Dicetak: ' . $meta['printed_at']);
            $s1->getStyle('A3')->getFont()->setSize(9);
            $s1->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Tabel 1A: Ringkasan Klasifikasi Sumber Penerangan
            $s1->setCellValue('A5', 'A. RINGKASAN KLASIFIKASI SUMBER PENERANGAN UTAMA');
            $s1->getStyle('A5')->getFont()->setBold(true);

            $s1->fromArray(['No', 'Kategori Sumber Penerangan Utama', 'Jumlah (KK)', 'Persentase (%)'], null, 'A6');
            $s1->getStyle('A6:D6')->applyFromArray($headerStyle);

            $sum = $analytics['summary'];
            $summaryRows = [
                [1, 'Listrik PLN dengan meteran', $sum['pln_meteran']['count'], $sum['pln_meteran']['percent']],
                [2, 'Listrik PLN tanpa meteran', $sum['pln_tanpa']['count'], $sum['pln_tanpa']['percent']],
                [3, 'Listrik non-PLN', $sum['non_pln']['count'], $sum['non_pln']['percent']],
                [4, 'Bukan listrik', $sum['bukan_listrik']['count'], $sum['bukan_listrik']['percent']],
                [5, 'Belum Terdata', $sum['belum_terdata']['count'], $sum['belum_terdata']['percent']],
            ];
            $r = 7;
            foreach ($summaryRows as $sr) {
                $s1->fromArray($sr, null, 'A' . $r);
                $s1->getStyle('A' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s1->getStyle('C' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $s1->getStyle('D' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $r++;
            }
            $s1->fromArray(['', 'TOTAL KELUARGA', $sum['total'], $sum['total'] > 0 ? 100.0 : 0.0], null, 'A' . $r);
            $s1->getStyle('A6:D' . $r)->applyFromArray($borderThin);
            $s1->getStyle('A' . $r . ':D' . $r)->applyFromArray($totalRowStyle);

            // Tabel 1B: Breakdown Daya Listrik PLN (di sebelah kanan: F5:I...)
            $s1->setCellValue('F5', 'B. RINCIAN DAYA LISTRIK (KHUSUS PLN DENGAN METERAN)');
            $s1->getStyle('F5')->getFont()->setBold(true);
            $s1->fromArray(['No', 'Golongan Daya PLN', 'Jumlah (KK)', 'Porsi dari PLN Meteran (%)'], null, 'F6');
            $s1->getStyle('F6:I6')->applyFromArray($subHeaderStyle);

            $rd = 7;
            foreach ($analytics['daya_breakdown'] as $idx => $dbItem) {
                $s1->fromArray([$idx + 1, $dbItem['label'], $dbItem['count'], $dbItem['percent']], null, 'F' . $rd);
                $s1->getStyle('F' . $rd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s1->getStyle('H' . $rd . ':I' . $rd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $rd++;
            }
            $s1->fromArray(['', 'TOTAL PLN DENGAN METERAN', $analytics['daya_total'], $analytics['daya_total'] > 0 ? 100.0 : 0.0], null, 'F' . $rd);
            $s1->getStyle('F6:I' . $rd)->applyFromArray($borderThin);
            $s1->getStyle('F' . $rd . ':I' . $rd)->applyFromArray($totalRowStyle);

            // Tabel 1C: Rekapitulasi per Wilayah
            $startRowWil = max($r, $rd) + 2;
            $s1->setCellValue('A' . $startRowWil, 'C. REKAPITULASI PER WILAYAH (' . strtoupper($analytics['wilayah_level']) . ')');
            $s1->getStyle('A' . $startRowWil)->getFont()->setBold(true);

            $hRow = $startRowWil + 1;
            $s1->fromArray([
                'No',
                'Wilayah (' . $analytics['wilayah_level'] . ')',
                'Total KK',
                'PLN Meteran',
                'PLN Tanpa Meteran',
                'Non-PLN',
                'Bukan Listrik',
                'Belum Terdata',
                'Daya 450W',
                'Daya 900W',
                'Daya >= 1.300W',
            ], null, 'A' . $hRow);
            $s1->getStyle('A' . $hRow . ':K' . $hRow)->applyFromArray($headerStyle);

            $rwRow = $hRow + 1;
            $totW = array_fill(0, 9, 0);
            foreach ($analytics['wilayah_rekap'] as $idx => $w) {
                $vals = [
                    $idx + 1,
                    $w['label'],
                    $w['total'],
                    $w['pln_meteran'],
                    $w['pln_tanpa'],
                    $w['non_pln'],
                    $w['bukan_listrik'],
                    $w['belum_terdata'],
                    $w['daya_450'],
                    $w['daya_900'],
                    $w['daya_1300_plus'],
                ];
                $s1->fromArray($vals, null, 'A' . $rwRow);
                $s1->getStyle('A' . $rwRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                for ($ci = 2; $ci < 11; $ci++) {
                    $totW[$ci - 2] += (int) $vals[$ci];
                }
                $rwRow++;
            }
            $s1->fromArray(array_merge(['', 'TOTAL KESELURUHAN'], $totW), null, 'A' . $rwRow);
            $s1->getStyle('A' . $hRow . ':K' . $rwRow)->applyFromArray($borderThin);
            $s1->getStyle('A' . $rwRow . ':K' . $rwRow)->applyFromArray($totalRowStyle);

            foreach (range('A', 'K') as $col) {
                $s1->getColumnDimension($col)->setAutoSize(true);
            }

            // ============================================================
            // SHEET 2: KORELASI DESIL & BANSOS
            // ============================================================
            $s2 = $spreadsheet->createSheet();
            $s2->setTitle('Korelasi Desil & Bansos');

            $s2->mergeCells('A1:G1');
            $s2->setCellValue('A1', 'ANALISIS KORELASI SUMBER PENERANGAN DENGAN DESIL & BANSOS AKTIF');
            $s2->getStyle('A1')->getFont()->setSize(13)->setBold(true);

            // 2A. Matriks Desil
            $s2->setCellValue('A3', 'A. KORELASI SUMBER PENERANGAN UTAMA × KELOMPOK DESIL');
            $s2->getStyle('A3')->getFont()->setBold(true);
            $s2->fromArray(['Kategori Sumber Penerangan', 'Desil 1–4 (Prioritas)', 'Desil 5–10', 'Belum Ada / Desil 0', 'Total KK'], null, 'A4');
            $s2->getStyle('A4:E4')->applyFromArray($headerStyle);

            $rd2 = 5;
            $totDesil = [0, 0, 0, 0];
            foreach ($analytics['korelasi_desil'] as $katLabel => $vals) {
                $rowArr = [
                    $katLabel,
                    $vals['Desil 1–4'],
                    $vals['Desil 5–10'],
                    $vals['Belum Ada / Desil 0'],
                    $vals['Total'],
                ];
                $s2->fromArray($rowArr, null, 'A' . $rd2);
                $totDesil[0] += $vals['Desil 1–4'];
                $totDesil[1] += $vals['Desil 5–10'];
                $totDesil[2] += $vals['Belum Ada / Desil 0'];
                $totDesil[3] += $vals['Total'];
                $rd2++;
            }
            $s2->fromArray(array_merge(['TOTAL'], $totDesil), null, 'A' . $rd2);
            $s2->getStyle('A4:E' . $rd2)->applyFromArray($borderThin);
            $s2->getStyle('A' . $rd2 . ':E' . $rd2)->applyFromArray($totalRowStyle);

            // 2B. Matriks Bansos
            $bStart = $rd2 + 2;
            $s2->setCellValue('A' . $bStart, 'B. KORELASI SUMBER PENERANGAN UTAMA × PENERIMA BANSOS AKTIF');
            $s2->getStyle('A' . $bStart)->getFont()->setBold(true);

            $bhRow = $bStart + 1;
            $s2->fromArray(['Kategori Sumber Penerangan', 'PKH', 'SEMBAKO', 'PKH + SEMBAKO', 'Total Penerima Bansos', 'Non-Bansos', 'Total KK'], null, 'A' . $bhRow);
            $s2->getStyle('A' . $bhRow . ':G' . $bhRow)->applyFromArray($subHeaderStyle);

            $rb = $bhRow + 1;
            $totBansos = [0, 0, 0, 0, 0, 0];
            foreach ($analytics['korelasi_bansos'] as $katLabel => $vals) {
                $rowArr = [
                    $katLabel,
                    $vals['PKH'],
                    $vals['SEMBAKO'],
                    $vals['PKH + SEMBAKO'],
                    $vals['Total Penerima'],
                    $vals['Non-Bansos'],
                    $vals['Total'],
                ];
                $s2->fromArray($rowArr, null, 'A' . $rb);
                for ($ci = 0; $ci < 6; $ci++) {
                    $totBansos[$ci] += $rowArr[$ci + 1];
                }
                $rb++;
            }
            $s2->fromArray(array_merge(['TOTAL'], $totBansos), null, 'A' . $rb);
            $s2->getStyle('A' . $bhRow . ':G' . $rb)->applyFromArray($borderThin);
            $s2->getStyle('A' . $rb . ':G' . $rb)->applyFromArray($totalRowStyle);

            foreach (range('A', 'G') as $col) {
                $s2->getColumnDimension($col)->setAutoSize(true);
            }

            // ============================================================
            // SHEET 3: DATA RINCI KELUARGA (BNBA)
            // ============================================================
            $s3 = $spreadsheet->createSheet();
            $s3->setTitle('Data Rinci BNBA');

            $s3->mergeCells('A1:P1');
            $s3->setCellValue('A1', 'DATA RINCI KELUARGA BERDASARKAN SUMBER PENERANGAN UTAMA (BNBA)');
            $s3->getStyle('A1')->getFont()->setSize(12)->setBold(true);

            $bnbaHeaders = [
                'NO', 'NO. KK', 'KEPALA KELUARGA', 'NIK KEPALA KELUARGA', 'ALAMAT',
                'DESA', 'RW', 'RT', 'SUMBER PENERANGAN UTAMA', 'GOLONGAN DAYA',
                'RINCIAN DAYA / METERAN', 'JML METERAN', 'ID PELANGGAN PLN', 'NOMOR METER',
                'DESIL', 'BANSOS AKTIF', 'SUMBER DATA'
            ];
            $s3->fromArray($bnbaHeaders, null, 'A3');
            $lastCol = Coordinate::stringFromColumnIndex(count($bnbaHeaders));
            $s3->getStyle('A3:' . $lastCol . '3')->applyFromArray($headerStyle);

            $r3 = 4;
            $no3 = 1;
            foreach ($dataset as $row) {
                $s3->setCellValue('A' . $r3, $no3++);
                $s3->setCellValueExplicit('B' . $r3, $row['no_kk'], DataType::TYPE_STRING);
                $s3->setCellValue('C' . $r3, $row['kepala_keluarga']);
                $s3->setCellValueExplicit('D' . $r3, $row['nik_kepala_keluarga'], DataType::TYPE_STRING);
                $s3->setCellValue('E' . $r3, $row['alamat']);
                $s3->setCellValue('F' . $r3, $row['nama_desa']);
                $s3->setCellValueExplicit('G' . $r3, $row['rw'], DataType::TYPE_STRING);
                $s3->setCellValueExplicit('H' . $r3, $row['rt'], DataType::TYPE_STRING);
                $s3->setCellValue('I' . $r3, $row['sumber_penerangan']);
                $s3->setCellValue('J' . $r3, $row['daya_group']);
                $s3->setCellValue('K' . $r3, $row['daya_raw'] !== '' ? $row['daya_raw'] : '-');
                $s3->setCellValue('L' . $r3, $row['jumlah_meteran']);
                $s3->setCellValueExplicit('M' . $r3, $row['nomor_pelanggan'] !== '' ? $row['nomor_pelanggan'] : '-', DataType::TYPE_STRING);
                $s3->setCellValueExplicit('N' . $r3, $row['nomor_meter'] !== '' ? $row['nomor_meter'] : '-', DataType::TYPE_STRING);
                $s3->setCellValue('O' . $r3, $row['desil'] !== null ? 'Desil ' . $row['desil'] : 'Belum Ada');
                $s3->setCellValue('P' . $r3, $row['bansos']);
                $s3->setCellValue('Q' . $r3, $row['sumber_data']);

                $s3->getStyle('A' . $r3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s3->getStyle('G' . $r3 . ':H' . $r3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s3->getStyle('L' . $r3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s3->getStyle('O' . $r3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $r3++;
            }

            if ($r3 > 4) {
                $s3->getStyle('A3:' . $lastCol . ($r3 - 1))->applyFromArray($borderThin);
            }

            foreach (range('A', $lastCol) as $col) {
                $s3->getColumnDimension($col)->setAutoSize(true);
            }

            $spreadsheet->setActiveSheetIndex(0);

            $fileName = 'Laporan_Sumber_Penerangan_' . date('Ymd_His') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            log_message('error', '❌ exportExcel LaporanPenerangan error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor Excel: ' . $e->getMessage());
        }
    }

    /**
     * 🖨️ CETAK LAPORAN RESMI (KOP SURAT DESA + RINGKASAN + MATRIKS + LAMPIRAN BNBA OPSIONAL)
     */
    public function cetak()
    {
        if ($resp = $this->checkAccess(false)) {
            return $resp;
        }

        $user     = $this->getUserContext();
        $roleId   = $user['role_id'];
        $filter   = $this->extractFilterFromRequest($user, 'get');
        $search   = trim((string) ($this->request->getGet('search') ?? ''));
        $lampiran = (int) ($this->request->getGet('lampiran') ?? 0) === 1;
        $maskKk   = (int) ($this->request->getGet('mask') ?? 0) === 1;

        session_write_close();

        $dataset = $this->laporanModel->getDataset($filter, $roleId);
        if ($search !== '') {
            $dataset = $this->laporanModel->applySearch($dataset, $search);
        }

        $analytics = $this->laporanModel->buildAnalytics($dataset, $filter);
        $meta      = $this->buildReportMeta($filter, $user);

        return view('dtsen/laporan_penerangan/cetak', [
            'title'            => 'Laporan Resmi Sumber Penerangan Utama Keluarga',
            'meta'             => $meta,
            'analytics'        => $analytics,
            'dataset'          => $lampiran ? $dataset : [],
            'total_bnba'       => count($dataset),
            'include_lampiran' => $lampiran,
            'mask_kk'          => $maskKk,
            'filter'           => $filter,
            'search'           => $search,
        ]);
    }
}
