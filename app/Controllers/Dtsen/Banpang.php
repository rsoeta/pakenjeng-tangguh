<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel; // Pastikan Model Auth dipanggil
use App\Traits\WilayahFilterTrait; // 🚀 Panggil Trait Jagoan Kita

class Banpang extends BaseController
{
    use WilayahFilterTrait; // 🚀 Gunakan Trait di dalam class

    protected $db;
    protected $authModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->authModel = new AuthModel(); // Inisialisasi AuthModel
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
    // 🚀 DATATABLES REKAP BANPANG (WITH TRAIT & OPTIMIZED JOIN)
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

        // 🚀 IMPLEMENTASI JOIN SUPER AMAN & PRESISI
        $builder = $this->db->table('dtsen_banpang b')
            ->select('b.*, rt.rt, rt.rw')
            ->join('dtsen_art a', 'a.nik = b.nik_kpm AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left');

        // 🔐 TERAPKAN TRAIT WILAYAH FILTER
        $filterData = [
            'kode_desa'     => $kodeDesa,
            'wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')
        ];
        $this->applyWilayahFilter($builder, $filterData, $roleId);

        // Hitung Total Data (Sebelum filter manual)
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
            // Masking NIK untuk keamanan tampilan
            $nikLengkap = esc($row['nik_kpm']);
            $nikMasked = strlen($nikLengkap) >= 16 ? substr($nikLengkap, 0, 8) . '********' : $nikLengkap;

            // Alamat RT/RW (Jika NULL karena data KPM tidak ada di database kependudukan, beri tanda strip)
            $alamat = (!empty($row['rt']) && !empty($row['rw']))
                ? '<span class="badge badge-light border">RT ' . esc($row['rt']) . ' / RW ' . esc($row['rw']) . '</span>'
                : '<span class="badge badge-warning">Luar Wilayah / Tidak Ditemukan</span>';

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
                '<button class="btn btn-xs btn-outline-primary" title="Detail"><i class="fas fa-search"></i></button>' // Placeholder untuk aksi
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data
        ]);
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
}
