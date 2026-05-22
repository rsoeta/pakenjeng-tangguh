<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Traits\WilayahFilterTrait;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class MasterKKS extends BaseController
{
    use WilayahFilterTrait;

    protected $db;
    protected $authModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Master Data KKS',
            'list'  => $this->db->table('dtsen_master_kks')->get()->getResultArray()
        ];

        return view('dtsen/bansos_kks/v_master_kks', $data);
    }

    // ========================================================
    // 📊 DATA TABLES: READ DATA & INTEGRASI TRAIT WILAYAH
    // ========================================================
    public function datatable()
    {
        $request = \Config\Services::request();

        $draw   = $request->getPost('draw');
        $start  = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';

        $filter_rw     = $request->getPost('filter_rw');
        $filter_rt     = $request->getPost('filter_rt');
        $filter_status = $request->getPost('filter_status');

        // 🚀 SUNTIKAN JOIN UNTUK MENGAMBIL NO_KK (BUG FIX: DOUBLE DATA)
        $builder = $this->db->table('dtsen_master_kks rt')
            ->select('rt.*, kk.no_kk')
            // 1️⃣ Tambahkan filter deleted_at langsung di dalam kondisi JOIN
            ->join('dtsen_art art', 'art.nik = rt.nik AND art.deleted_at IS NULL', 'left')
            ->join('dtsen_kk kk', 'kk.id_kk = art.id_kk AND kk.deleted_at IS NULL', 'left')
            // 2️⃣ Sabuk pengaman: Kunci agar 1 ID Master KKS HANYA tampil 1 kali
            ->groupBy('rt.id');

        // =======================================================
        // 🔐 TERAPKAN TRAIT WILAYAH FILTER
        // =======================================================
        $user   = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? $user['role_id'] ?? 4;

        $filterData = [
            'wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')
        ];

        $this->applyWilayahFilter($builder, $filterData, $roleId);

        $totalRecords = $builder->countAllResults(false);

        // =======================================================
        // 🔍 FILTER DINAMIS DARI FRONTEND
        // =======================================================
        if (!empty($filter_rw)) {
            $builder->where('rt.rw', str_pad($filter_rw, 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filter_rt)) {
            $builder->where('rt.rt', str_pad($filter_rt, 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filter_status)) {
            $builder->where('rt.status_kks', $filter_status);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('rt.nik', $search)
                ->orLike('rt.nama_penerima', $search)
                ->orLike('rt.no_kks', $search)
                ->orLike('kk.no_kk', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('rt.rw', 'ASC')->orderBy('rt.rt', 'ASC')->orderBy('rt.nama_penerima', 'ASC');

        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $query = $builder->get()->getResultArray();

        $data = [];
        $no = $start + 1;

        // ==========================================
        // 🛡️ FUNGSI BANTUAN: SENSOR DATA + TOMBOL SALIN
        // ==========================================
        $maskNumber = function ($number, $type) {
            $number = trim($number ?? '');
            if (empty($number) || $number === '-' || $number === 'NOKKS') return esc($number);

            $full = esc($number);
            $len = strlen($full);

            $btnClass = ($type === 'nik') ? 'btnCopyNik' : 'btnCopyNoKK';
            $btnTitle = ($type === 'nik') ? 'Salin NIK' : 'Salin No KK';

            if ($len <= 8) {
                $masked = $full;
                $hoverAttr = '';
            } else {
                $masked = substr($full, 0, 8) . str_repeat('*', $len - 8);
                $hoverAttr = ' onmouseenter="this.innerText=\'' . $full . '\'" onmouseleave="this.innerText=\'' . $masked . '\'" ontouchstart="this.innerText=\'' . $full . '\'" ontouchend="this.innerText=\'' . $masked . '\'" title="Tahan/Arahkan kursor untuk melihat utuh" ';
            }

            return '
            <div class="d-flex justify-content-between align-items-center gap-2">
                <span style="display: none;">' . $full . '</span>
                <span class="text-primary fw-bold" style="cursor:pointer;"' . $hoverAttr . '>' . $masked . '</span>
                <button type="button" class="btn btn-outline-secondary btn-xs ' . $btnClass . ' py-0 px-1" data-value="' . $full . '" title="' . $btnTitle . '">
                    <i class="fas fa-copy"></i>
                </button>
            </div>';
        };

        foreach ($query as $row) {
            $status_cek = strtolower(trim($row['status_kks']));
            $badge = ($status_cek == 'aktif')
                ? '<span class="badge badge-success">Aktif</span>'
                : (($status_cek == 'non aktif' || $status_cek == 'non-aktif')
                    ? '<span class="badge badge-danger">Non Aktif</span>'
                    : '<span class="badge badge-secondary">' . esc($row['status_kks']) . '</span>');

            $btnAction = '
                <button class="btn btn-xs btn-warning btn-edit" data-id="' . $row['id'] . '" title="Edit KPM"><i class="fas fa-edit"></i></button>
            ';

            if ($roleId <= 3) {
                $btnAction .= '
                <button class="btn btn-xs btn-danger btn-delete" data-id="' . $row['id'] . '" data-nama="' . esc($row['nama_penerima']) . '" title="Hapus KPM"><i class="fas fa-trash"></i></button>
                ';
            }

            // 🚀 Menerapkan sensor
            $nikMasked = $maskNumber($row['nik'], 'nik');
            $kksMasked = $maskNumber($row['no_kks'], 'nokk');

            // 🚀 Ambil no_kk dari hasil join, beri nilai default jika KPM tidak ada di dtsen_art
            $noKkMasked = $maskNumber($row['no_kk'] ?? '-', 'nokk');

            // 🚀 SUSUNAN BARU: No | Nama KPM | No. KKS | NIK | No. KK | Alamat Lengkap | Status | Aksi
            $data[] = [
                $no++,
                esc($row['nama_penerima']),
                $kksMasked,
                $nikMasked,
                $noKkMasked,
                esc($row['alamat']) . ' RT ' . esc($row['rt']) . ' RW ' . esc($row['rw']),
                $badge,
                $btnAction
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data
        ]);
    }

    // // ========================================================
    // // 📊 DATA TABLES: READ DATA & INTEGRASI TRAIT WILAYAH
    // // ========================================================
    // public function datatable()
    // {
    //     $request = \Config\Services::request();

    //     $draw   = $request->getPost('draw');
    //     $start  = $request->getPost('start');
    //     $length = $request->getPost('length');
    //     $search = $request->getPost('search')['value'] ?? '';

    //     $filter_rw     = $request->getPost('filter_rw');
    //     $filter_rt     = $request->getPost('filter_rt');
    //     $filter_status = $request->getPost('filter_status');

    //     $builder = $this->db->table('dtsen_master_kks rt');

    //     // =======================================================
    //     // 🔐 TERAPKAN TRAIT WILAYAH FILTER
    //     // =======================================================
    //     $user   = $this->authModel->getUserId();
    //     $roleId = session()->get('role_id') ?? $user['role_id'] ?? 4;

    //     $filterData = [
    //         'wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')
    //     ];

    //     $this->applyWilayahFilter($builder, $filterData, $roleId);

    //     $totalRecords = $builder->countAllResults(false);

    //     // =======================================================
    //     // 🔍 FILTER DINAMIS DARI FRONTEND
    //     // =======================================================
    //     if (!empty($filter_rw)) {
    //         $builder->where('rt.rw', str_pad($filter_rw, 3, '0', STR_PAD_LEFT));
    //     }
    //     if (!empty($filter_rt)) {
    //         $builder->where('rt.rt', str_pad($filter_rt, 3, '0', STR_PAD_LEFT));
    //     }
    //     if (!empty($filter_status)) {
    //         $builder->where('rt.status_kks', $filter_status);
    //     }

    //     if (!empty($search)) {
    //         $builder->groupStart()
    //             ->like('rt.nik', $search)
    //             ->orLike('rt.nama_penerima', $search)
    //             ->orLike('rt.no_kks', $search)
    //             ->groupEnd();
    //     }

    //     $filteredRecords = $builder->countAllResults(false);

    //     $builder->orderBy('rt.rw', 'ASC')->orderBy('rt.rt', 'ASC')->orderBy('rt.nama_penerima', 'ASC');

    //     if ($length != -1) {
    //         $builder->limit($length, $start);
    //     }

    //     $query = $builder->get()->getResultArray();

    //     $data = [];
    //     $no = $start + 1;

    //     // ==========================================
    //     // 🛡️ FUNGSI BANTUAN: SENSOR DATA + TOMBOL SALIN
    //     // ==========================================
    //     $maskNumber = function ($number, $type) {
    //         $number = trim($number ?? '');
    //         if (empty($number) || $number === '-' || $number === 'NOKKS') return esc($number);

    //         $full = esc($number);
    //         $len = strlen($full);

    //         // Tentukan Class dan Title Tombol berdasarkan jenis data
    //         $btnClass = ($type === 'nik') ? 'btnCopyNik' : 'btnCopyNoKK';
    //         $btnTitle = ($type === 'nik') ? 'Salin NIK' : 'Salin No KK';

    //         if ($len <= 8) {
    //             $masked = $full;
    //             $hoverAttr = '';
    //         } else {
    //             $masked = substr($full, 0, 8) . str_repeat('*', $len - 8);
    //             $hoverAttr = ' onmouseenter="this.innerText=\'' . $full . '\'" onmouseleave="this.innerText=\'' . $masked . '\'" ontouchstart="this.innerText=\'' . $full . '\'" ontouchend="this.innerText=\'' . $masked . '\'" title="Tahan/Arahkan kursor untuk melihat utuh" ';
    //         }

    //         return '
    //         <div class="d-flex justify-content-between align-items-center gap-2">
    //             <span style="display: none;">' . $full . '</span>
    //             <span class="text-primary fw-bold" style="cursor:pointer;"' . $hoverAttr . '>' . $masked . '</span>
    //             <button type="button" class="btn btn-outline-secondary btn-xs ' . $btnClass . ' py-0 px-1" data-value="' . $full . '" title="' . $btnTitle . '">
    //                 <i class="fas fa-copy"></i>
    //             </button>
    //         </div>';
    //     };

    //     foreach ($query as $row) {
    //         $status_cek = strtolower(trim($row['status_kks']));
    //         $badge = ($status_cek == 'aktif')
    //             ? '<span class="badge badge-success">Aktif</span>'
    //             : (($status_cek == 'non aktif' || $status_cek == 'non-aktif')
    //                 ? '<span class="badge badge-danger">Non Aktif</span>'
    //                 : '<span class="badge badge-secondary">' . esc($row['status_kks']) . '</span>');

    //         $btnAction = '
    //             <button class="btn btn-xs btn-warning btn-edit" data-id="' . $row['id'] . '" title="Edit KPM"><i class="fas fa-edit"></i></button>
    //         ';

    //         if ($roleId <= 3) {
    //             $btnAction .= '
    //             <button class="btn btn-xs btn-danger btn-delete" data-id="' . $row['id'] . '" data-nama="' . esc($row['nama_penerima']) . '" title="Hapus KPM"><i class="fas fa-trash"></i></button>
    //             ';
    //         }

    //         // 🚀 Menerapkan sensor NIK dan KKS beserta jenis tipenya
    //         $nikMasked = $maskNumber($row['nik'], 'nik');
    //         $kksMasked = $maskNumber($row['no_kks'], 'nokk');

    //         $data[] = [
    //             $no++,
    //             $nikMasked, // Menggunakan variabel yang sudah disensor + tombol
    //             esc($row['nama_penerima']),
    //             $kksMasked, // Menggunakan variabel yang sudah disensor + tombol
    //             esc($row['alamat']) . ' RT ' . esc($row['rt']) . ' RW ' . esc($row['rw']),
    //             $badge,
    //             $btnAction
    //         ];
    //     }

    //     return $this->response->setJSON([
    //         'draw'            => $draw,
    //         'recordsTotal'    => $totalRecords,
    //         'recordsFiltered' => $filteredRecords,
    //         'data'            => $data
    //     ]);
    // }

    // ========================================================
    // 🌐 AJAX API: GET RW & RT DINAMIS
    // ========================================================
    public function get_rw_ajax()
    {
        $user   = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? $user['role_id'] ?? 4;

        // Ambil RW langsung dari master_kks agar akurat dengan data yang ada
        $builder = $this->db->table('dtsen_master_kks rt')->select('rt.rw')->distinct();
        $this->applyWilayahFilter($builder, ['wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')], $roleId);
        $builder->orderBy('rt.rw', 'ASC');

        $rws = $builder->get()->getResultArray();

        $options = '<option value="">-- Semua RW --</option>';
        foreach ($rws as $row) {
            if (!empty($row['rw'])) {
                $options .= '<option value="' . esc($row['rw']) . '">RW ' . esc($row['rw']) . '</option>';
            }
        }
        return $this->response->setBody($options);
    }

    public function get_rt_ajax()
    {
        $rw     = $this->request->getPost('rw');
        $user   = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? $user['role_id'] ?? 4;

        $builder = $this->db->table('dtsen_master_kks rt')->select('rt.rt')->distinct();
        $builder->where('rt.rw', str_pad($rw, 3, '0', STR_PAD_LEFT));
        $this->applyWilayahFilter($builder, ['wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')], $roleId);
        $builder->orderBy('rt.rt', 'ASC');

        $rts = $builder->get()->getResultArray();

        $options = '<option value="">-- Semua RT --</option>';
        foreach ($rts as $row) {
            if (!empty($row['rt'])) {
                $options .= '<option value="' . esc($row['rt']) . '">RT ' . esc($row['rt']) . '</option>';
            }
        }
        return $this->response->setBody($options);
    }

    public function import_excel()
    {
        // 🔐 VALIDASI ROLE: Tolak mentah-mentah jika bukan Admin
        $roleId = session()->get('role_id') ?? 4;
        if ($roleId > 3) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses Ditolak! Anda tidak memiliki izin untuk mengimpor data.']);
        }

        $file = $this->request->getFile('file_excel');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true); // Mematikan kalkulasi formula yang bikin error tadi

        $spreadsheet = $reader->load($file);
        // Parameter kedua false agar tidak mencoba menghitung formula (mengambil nilai mentah)
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, false, true, false);

        $count = 0;
        foreach ($sheetData as $key => $row) {
            // Lewati baris pertama (Header: Timestamp, Email, NIK, dll)
            if ($key == 0) continue;

            // Mapping berdasarkan struktur:
            // [0]Timestamp | [1]Email | [2]NIK | [3]Nama | [4]No.KKS | [5]WA | [6]Kepesertaan | [7]Status
            // [8]FotoKepemilikan | [9]Pernyataan | [10]KirimWA | [11]Alamat | [12]RT | [13]RW | [14]FotoKKS

            $nik = trim($row[2] ?? '');

            // Pastikan NIK tidak kosong sebelum diproses
            if (empty($nik)) continue;

            $data = [
                'nik'              => $nik,
                'nama_penerima'    => strtoupper(trim($row[3] ?? '')),
                'no_kks'           => trim($row[4] ?? ''),
                'no_wa'            => trim($row[5] ?? ''),
                'kepesertaan'      => trim($row[6] ?? ''),
                'status_kks'       => trim($row[7] ?? ''),
                'foto_kepemilikan' => trim($row[8] ?? ''), // Link/nama file foto orang pegang kartu
                'alamat'           => trim($row[11] ?? ''), // L-Alamat
                'rt'               => trim($row[12] ?? ''), // L-RT
                'rw'               => trim($row[13] ?? ''), // L-RW
                'foto_kks'         => trim($row[14] ?? ''), // Link/nama file foto fisik kartu
            ];

            // Replace akan mendeteksi NIK yang sama dan melakukan update, jika baru akan insert
            $this->db->table('dtsen_master_kks')->replace($data);
            $count++;
        }

        return redirect()->to('/master-kks')->with('success', "Sinkronisasi selesai. $count data KKS berhasil dipetakan ke database.");
    }

    // ========================================================
    // 📝 CRUD: AMBIL DATA SATUAN UNTUK FORM EDIT
    // ========================================================
    public function get_kpm()
    {
        $id = $this->request->getPost('id');
        $data = $this->db->table('dtsen_master_kks')->where('id', $id)->get()->getRowArray();
        return $this->response->setJSON($data);
    }

    // ========================================================
    // 💾 CRUD: SIMPAN DATA DENGAN UPLOAD FOTO & AUDIT TRAIL
    // ========================================================
    public function save()
    {
        // 🕵️ AMBIL ID ATAU NIK PETUGAS (Akurat & Relasional)
        $userId = session()->get('id'); // Atau ganti 'nik' jika Anda prefer NIK

        // Validasi keamanan sesi
        if (empty($userId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sesi Anda telah habis. Silakan login kembali untuk melanjutkan.'
            ]);
        }

        $post = $this->request->getPost();

        $id            = $post['id'] ?? '';
        $nik           = trim($post['nik']);
        $nama_penerima = strtoupper(trim($post['nama_penerima']));
        $no_kks        = empty(trim($post['no_kks'])) ? '-' : trim($post['no_kks']);
        $no_wa         = trim($post['no_wa'] ?? '');
        $alamat        = trim($post['alamat'] ?? '');
        $rw            = str_pad(trim($post['rw']), 3, '0', STR_PAD_LEFT);
        $rt            = str_pad(trim($post['rt']), 3, '0', STR_PAD_LEFT);
        $status_kks    = $post['status_kks'];

        // 🛡️ VALIDASI ANTI GANDA (Cek NIK)
        $cekNik = $this->db->table('dtsen_master_kks')->where('nik', $nik);
        if (!empty($id)) {
            $cekNik->where('id !=', $id);
        }
        if ($cekNik->countAllResults() > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal! NIK tersebut sudah terdaftar sebagai KPM.']);
        }

        // 🛡️ VALIDASI ANTI GANDA (Cek No KKS)
        if (!empty($no_kks) && $no_kks !== '-') {
            $cekKks = $this->db->table('dtsen_master_kks')->where('no_kks', $no_kks);
            if (!empty($id)) {
                $cekKks->where('id !=', $id);
            }
            if ($cekKks->countAllResults() > 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal! Nomor KKS tersebut sudah dipakai oleh KPM lain.']);
            }
        }

        // 📝 SIAPKAN BUNGKUSAN DATA
        $data = [
            'nik'           => $nik,
            'nama_penerima' => $nama_penerima,
            'no_kks'        => $no_kks,
            'no_wa'         => $no_wa,
            'alamat'        => $alamat,
            'rw'            => $rw,
            'rt'            => $rt,
            'status_kks'    => $status_kks,
            'updated_at'    => date('Y-m-d H:i:s'),
            'updated_by'    => $userId // 👈 Menyimpan ID User yang mengedit
        ];

        // 📸 PROSES UPLOAD FOTO KKS
        $fileFoto = $this->request->getFile('foto_kks');
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $uploadPath = FCPATH . 'data/master_kks/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            $newName = 'KKS_' . $nik . '_' . time() . '.' . $fileFoto->getExtension();
            $fileFoto->move($uploadPath, $newName);

            $data['foto_kks'] = 'data/master_kks/' . $newName;
        }

        try {
            if (empty($id)) {
                // JIKA INSERT
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $userId; // 👈 Menyimpan ID User yang membuat

                $this->db->table('dtsen_master_kks')->insert($data);
                $msg = 'Data KPM berhasil ditambahkan!';
            } else {
                // JIKA UPDATE
                $this->db->table('dtsen_master_kks')->where('id', $id)->update($data);
                $msg = 'Data KPM berhasil diperbarui!';
            }
            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        } catch (\Throwable $th) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kesalahan Database: ' . $th->getMessage()]);
        }
    }

    // ========================================================
    // 🔍 AJAX SELECT2: CARI PENDUDUK (TRIPLE JOIN + KUNCI DESA)
    // ========================================================
    public function search_penduduk()
    {
        $search = $this->request->getPost('searchTerm');
        $user   = $this->authModel->getUserId();

        // Ambil Role dan Kode Desa dari Session / Data User
        $roleId   = session()->get('role_id') ?? $user['role_id'] ?? 4;
        $kodeDesa = session()->get('kode_desa') ?? $user['kode_desa'] ?? '';

        // 1. Triple Join: ART -> KK -> RT
        $builder = $this->db->table('dtsen_art art')
            ->select('art.nik, art.nama, kk.alamat, rt.rt, rt.rw')
            ->join('dtsen_kk kk', 'kk.id_kk = art.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');

        // 2. Kunci wilayah otomatis + KUNCI KODE DESA 🔐
        $filterData = [
            'wilayah_tugas' => trim($user['wilayah_tugas'] ?? ''),
            'kode_desa'     => $kodeDesa // 👈 INI KUNCI PENGAMAN ANTAR DESA
        ];
        $this->applyWilayahFilter($builder, $filterData, $roleId);

        // 3. Pencarian NIK atau Nama
        if (!empty($search)) {
            $builder->groupStart()
                ->like('art.nik', $search)
                ->orLike('art.nama', $search)
                ->groupEnd();
        }

        $query = $builder->limit(20)->get()->getResultArray();

        // 4. Susun Format Kembalian untuk Select2
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'id'     => $row['nik'],
                'text'   => $row['nik'] . ' - ' . $row['nama'],
                'nama'   => $row['nama'],
                'alamat' => $row['alamat'] ?? '-',
                'rt'     => $row['rt'] ?? '0',
                'rw'     => $row['rw'] ?? '0'
            ];
        }

        return $this->response->setJSON($data);
    }

    // ========================================================
    // 🗑️ CRUD: HAPUS DATA KPM & BERSIHKAN FILE FOTO
    // ========================================================
    public function delete()
    {
        // 🔐 VALIDASI ROLE: Tolak mentah-mentah jika bukan Admin
        $roleId = session()->get('role_id') ?? 4;
        if ($roleId > 3) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses Ditolak! Anda tidak memiliki izin untuk menghapus data.']);
        }

        $id = $this->request->getPost('id');

        try {
            // 1. Ambil data KPM sebelum dihapus untuk mengecek file foto
            $kpm = $this->db->table('dtsen_master_kks')->where('id', $id)->get()->getRowArray();

            // 2. Hapus file foto fisik dari folder server (jika ada dan bukan dari G-Drive)
            if ($kpm && !empty($kpm['foto_kks'])) {
                $filePath = FCPATH . $kpm['foto_kks'];
                if (file_exists($filePath)) {
                    unlink($filePath); // Sapu bersih file-nya!
                }
            }

            // 3. Eksekusi Hapus Data dari Database
            $this->db->table('dtsen_master_kks')->where('id', $id)->delete();

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data KPM beserta fotonya berhasil dihapus secara permanen!']);
        } catch (\Throwable $th) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $th->getMessage()]);
        }
    }
}
