<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Traits\WilayahFilterTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Bpjstk extends BaseController
{
    use WilayahFilterTrait;

    protected $db;
    protected $authModel; // 🚀 TAMBAHKAN INI

    public function __construct()
    {
        // Inisialisasi koneksi database
        $this->db = \Config\Database::connect();

        // Inisialisasi AuthModel agar bisa dipakai untuk cek wilayah tugas
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        // ... (biarkan sisa kode ke bawahnya tetap sama) ...
        $data = [
            // ... (biarkan sisa kode ke bawahnya tetap sama) ...
            'title' => 'Arsip BPJS Ketenagakerjaan',
            'user'  => session()->get()
        ];
        return view('dtsen/bpjstk/index', $data);
    }

    // ========================================================
    // 📊 DATA TABLES DENGAN TRAIT WILAYAH & SENSOR
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

        // 🚀 PERBAIKAN ERROR: Tambahkan alias "rt" pada tabel agar Trait Wilayah bisa bekerja!
        $builder = $this->db->table('dtsen_bpjstk rt');

        // --- 🔐 FILTER WILAYAH TRAIT ---
        $user   = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? $user['role_id'] ?? 4;
        $filterData = ['wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')];
        $this->applyWilayahFilter($builder, $filterData, $roleId);

        $totalRecords = $builder->countAllResults(false);

        // --- 🔍 FILTER MANUAL (Wajib tambahkan awalan 'rt.') ---
        if ($filter_rw != '') $builder->where('rt.rw', str_pad($filter_rw, 3, '0', STR_PAD_LEFT));
        if ($filter_rt != '') $builder->where('rt.rt', str_pad($filter_rt, 3, '0', STR_PAD_LEFT));
        if ($filter_status != '') $builder->where('rt.status_serah_terima', $filter_status);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('rt.nik', $search)
                ->orLike('rt.nama', $search)
                ->orLike('rt.kpj', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        // Urutkan yang belum diserahkan (0) ke atas, lalu berdasarkan RW/RT
        $builder->orderBy('rt.status_serah_terima', 'ASC')->orderBy('rt.rw', 'ASC')->orderBy('rt.rt', 'ASC')->orderBy('rt.nama', 'ASC');

        if ($length != -1) $builder->limit($length, $start);
        $query = $builder->get()->getResultArray();

        $data = [];
        $no = $start + 1;

        // ==========================================
        // 🛡️ FUNGSI BANTUAN: SENSOR DATA (MASKING)
        // ==========================================
        $maskNumber = function ($number, $type) {
            $number = trim($number ?? '');
            if (empty($number) || $number === '-') return esc($number);

            $full = esc($number);
            $len = strlen($full);

            // Gunakan class btnCopyNoKK untuk KPJ agar bisa memakai fungsi JS yang sama
            $btnClass = ($type === 'nik') ? 'btnCopyNik' : 'btnCopyNoKK';
            $btnTitle = ($type === 'nik') ? 'Salin NIK' : 'Salin Nomor BPJSTK';

            if ($len <= 8) {
                $masked = $full;
                $hoverAttr = '';
            } else {
                $masked = substr($full, 0, 8) . str_repeat('*', $len - 8);
                $hoverAttr = ' onmouseenter="this.innerText=\'' . $full . '\'" onmouseleave="this.innerText=\'' . $masked . '\'" ontouchstart="this.innerText=\'' . $full . '\'" ontouchend="this.innerText=\'' . $masked . '\'" title="Tahan/Arahkan kursor untuk melihat utuh" ';
            }

            return '
            <div class="d-inline-flex align-items-center gap-2">
                <span style="display: none;">' . $full . '</span>
                <span class="text-primary fw-bold" style="cursor:pointer;"' . $hoverAttr . '>' . $masked . '</span>
                <button type="button" class="btn btn-outline-secondary btn-xs ' . $btnClass . ' py-0 px-1" data-value="' . $full . '" title="' . $btnTitle . '">
                    <i class="fas fa-copy"></i>
                </button>
            </div>';
        };

        foreach ($query as $row) {
            $status = $row['status_serah_terima'] == 1
                ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Selesai</span>'
                : '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Belum</span>';

            $btnAction = '<div class="d-flex justify-content-center" style="gap: 5px;">';
            if ($row['status_serah_terima'] == 0) {
                $btnAction .= '<button class="btn btn-sm btn-primary btn-proses shadow-sm" data-id="' . $row['id'] . '"><i class="fas fa-handshake"></i> Proses</button>';
            } else {
                $btnAction .= '<button class="btn btn-sm btn-info btn-lihat shadow-sm" data-id="' . $row['id'] . '"><i class="fas fa-eye"></i> Arsip</button>';
            }
            $btnAction .= '</div>';

            // 🚀 MENERAPKAN SENSOR NIK & KPJ
            $kpjMasked = $maskNumber($row['kpj'], 'kpj');
            $nikMasked = $maskNumber($row['nik'], 'nik');

            $data[] = [
                $no++,
                esc($row['nama']),
                $kpjMasked . '<br><small class="text-muted">' . $nikMasked . '</small>',
                'RT ' . esc($row['rt']) . ' / RW ' . esc($row['rw']),
                $status,
                $btnAction
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    // ========================================================
    // 📥 AMBIL DATA SINGLE UNTUK MODAL
    // ========================================================
    public function getData($id)
    {
        $data = $this->db->table('dtsen_bpjstk')->where('id', $id)->get()->getRowArray();
        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    // ========================================================
    // 💾 PROSES SIMPAN SERAH TERIMA & GAMBAR TTD
    // ========================================================
    public function simpanSerahTerima()
    {
        $id = $this->request->getPost('id_bpjstk');
        $namaIbu = $this->request->getPost('nama_ibu');
        $noHp = $this->request->getPost('no_hp');
        $ttdDataUrl = $this->request->getPost('ttdDataUrl'); // Ini Base64 String

        $row = $this->db->table('dtsen_bpjstk')->where('id', $id)->get()->getRowArray();
        if (!$row) return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak valid']);

        $uploadPath = FCPATH . 'uploads/bpjstk/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true); // Buat folder otomatis jika belum ada

        $safeNama = preg_replace('/[^a-zA-Z0-9]/', '_', strtoupper($row['nama']));
        $fileBaseName = "BPJSTK_{$row['kpj']}_{$row['nik']}_{$safeNama}_" . time();

        $fotoNama = null;
        $ttdNama = null;

        // 1. Simpan Foto Bukti (File Biasa)
        $fileFoto = $this->request->getFile('foto_bukti');
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $fotoNama = $fileBaseName . '_FOTO.' . $fileFoto->getExtension();
            $fileFoto->move($uploadPath, $fotoNama);
        }

        // 2. Simpan Tanda Tangan (Convert Base64 ke File PNG)
        if (!empty($ttdDataUrl)) {
            $image_parts = explode(";base64,", $ttdDataUrl);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $ttdNama = $fileBaseName . '_TTD.png';
                file_put_contents($uploadPath . $ttdNama, $image_base64);
            }
        }

        if (!$fotoNama || !$ttdNama) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Foto dan Tanda Tangan wajib diisi!']);
        }

        // 3. Update Database
        $updateData = [
            'nama_ibu' => $namaIbu,
            'no_hp' => $noHp,
            'foto_bukti' => $fotoNama,
            'ttd_penerima' => $ttdNama,
            'status_serah_terima' => 1,
            'waktu_serah_terima' => date('Y-m-d H:i:s'),
            'petugas_id' => session()->get('user_id') ?? 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('dtsen_bpjstk')->where('id', $id)->update($updateData);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data Serah Terima Berhasil Disimpan!']);
    }

    // ========================================================
    // 📤 IMPORT EXCEL (Struktur Sesuai Kesepakatan)
    // ========================================================
    public function importExcel()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) return redirect()->back()->with('error', 'Pilih file Excel yang valid!');

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $insertData = [];
            foreach ($rows as $index => $row) {
                if ($index == 0) continue; // Skip header

                // Cek jika KKPJ kosong, lewati
                if (empty($row[1])) continue;

                $insertData[] = [
                    'no_urut'    => $row[0] ?? '',
                    'kpj'        => $row[1] ?? '',
                    'nama'       => $row[2] ?? '',
                    'nik'        => $row[3] ?? '',
                    'alamat'     => $row[4] ?? '',
                    'rt'         => str_pad($row[5] ?? '0', 3, '0', STR_PAD_LEFT),
                    'rw'         => str_pad($row[6] ?? '0', 3, '0', STR_PAD_LEFT),
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }

            if (!empty($insertData)) {
                $this->db->table('dtsen_bpjstk')->insertBatch($insertData);
            }

            // 🚀 PERBAIKAN: Gunakan redirect()->to() dengan base_url agar pulangnya pas ke halaman BPJSTK
            return redirect()->to(base_url('bpjstk'))->with('success', count($insertData) . ' Data BPJSTK Berhasil Diimpor!');
        } catch (\Exception $e) {
            // 🚀 PERBAIKAN JUGA UNTUK ERROR: 
            return redirect()->to(base_url('bpjstk'))->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    // ========================================================
    // 🔄 ROLLBACK DATA SERAH TERIMA (EKSKLUSIF ROLE < 4)
    // ========================================================
    public function rollback()
    {
        $id = $this->request->getPost('id');
        $roleId = session()->get('role_id');

        // Lapis keamanan ganda: Tolak jika role_id >= 4 (Pentri)
        if ($roleId >= 4) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak! Anda tidak memiliki wewenang untuk melakukan rollback.'
            ]);
        }

        $row = $this->db->table('dtsen_bpjstk')->where('id', $id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        // 🚀 Sapu bersih file fisik lama di server agar tidak jadi sampah penyimpanan
        $uploadPath = FCPATH . 'uploads/bpjstk/';
        if (!empty($row['foto_bukti']) && file_exists($uploadPath . $row['foto_bukti'])) {
            @unlink($uploadPath . $row['foto_bukti']);
        }
        if (!empty($row['ttd_penerima']) && file_exists($uploadPath . $row['ttd_penerima'])) {
            @unlink($uploadPath . $row['ttd_penerima']);
        }

        // 🚀 Kembalikan status data ke kondisi semula (Belum Diserahkan)
        $resetData = [
            'nama_ibu'            => null,
            'no_hp'               => null,
            'foto_bukti'          => null,
            'ttd_penerima'        => null,
            'status_serah_terima' => 0,
            'waktu_serah_terima'  => null,
            'petugas_id'          => null,
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        $this->db->table('dtsen_bpjstk')->where('id', $id)->update($resetData);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data serah terima berhasil dibatalkan (Rollback)!'
        ]);
    }
}
