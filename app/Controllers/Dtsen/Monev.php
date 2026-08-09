<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtsen\MonevModel;
use App\Models\Dtks\AuthModel;

class Monev extends BaseController
{
    protected $monevModel;
    protected $authModel;

    public function __construct()
    {
        $this->monevModel = new MonevModel();
        $this->authModel  = new AuthModel();
        // Pastikan PhpSpreadsheet sudah terinstall via Composer
        // composer require phpoffice/phpspreadsheet
    }

    public function index()
    {
        $userInfo = $this->authModel->getUserId();

        $data = [
            'title'      => 'Monitoring dan Evaluasi (MONEV) PKH',
            'user_login' => $userInfo
        ];

        return view('dtsen/monev/index', $data); // Tampilan view-nya nanti kita buat
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 FUNGSI DATATABLES SERVER-SIDE
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

        $nikPetugas = session()->get('nik');
        $roleId     = session()->get('role_id');

        // 🚀 THE MAGIC: Gunakan db_connect() agar 100% aman dari Fatal Error CI4
        $db = \Config\Database::connect();

        $builder = $db->table('dtsen_monev m')
            ->select("
                m.id_monev, 
                m.nama_target, 
                m.nik, 
                m.alamat, 
                m.rt, 
                m.rw, 
                m.status_monev,
                a.nama as nama_sinden,
                k.alamat as alamat_sinden,
                rt.rt as rt_sinden,
                rt.rw as rw_sinden,
                rt.foto_rumah,
                rt.foto_rumah_dalam,
                bk.foto_kpm_kks,
                COALESCE(mk.foto_kks, mk.foto_kepemilikan) as foto_kks_final
            ")
            // Hubungkan NIK Excel ke ART
            ->join('dtsen_art a', 'a.nik = m.nik AND a.deleted_at IS NULL', 'left')
            // Hubungkan ART ke KK (Pastikan aktif)
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            // Hubungkan KK ke RT/RW
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            // Ambil Foto KPM (Bansos KKS)
            ->join('dtsen_bansos_kks bk', 'bk.nik_kpm = m.nik', 'left')
            // Ambil Foto Fisik KKS (Master KKS)
            ->join('dtsen_master_kks mk', 'mk.nik = m.nik', 'left');

        // 🔒 Proteksi: Pendamping PKH (role 5) hanya melihat data target unggahannya sendiri
        if ($roleId == 5) {
            $builder->where('m.created_by', $nikPetugas);
        }

        // 🔍 Fitur Pencarian Global
        if (!empty($search)) {
            $builder->groupStart()
                ->like('m.nama_target', $search)
                ->orLike('m.nik', $search)
                ->groupEnd();
        }

        // 🚀 FITUR PENGURUTAN (SORTING)
        // Indeks array ini memetakan kolom DataTables (0-4) ke nama kolom di Database
        $columnOrder = [
            null,               // 0: No
            'm.nama_target',    // 1: Nama Target
            'm.alamat',         // 2: Alamat Lengkap
            'm.status_monev',   // 3: Status Monev
            null                // 4: Aksi
        ];

        if (isset($post['order'])) {
            $colIndex = (int) $post['order'][0]['column'];
            $dir      = $post['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC'; // Validasi arah panah

            // Jika kolom tersebut diizinkan untuk di-sort (bukan null)
            if (isset($columnOrder[$colIndex]) && $columnOrder[$colIndex] !== null) {
                $builder->orderBy($columnOrder[$colIndex], $dir);
            } else {
                $builder->orderBy('m.id_monev', 'ASC'); // Fallback
            }
        } else {
            $builder->orderBy('m.id_monev', 'ASC'); // Default load pertama kali
        }

        // HAPUS ATAU KOMENTARI BARIS INI (Karena sudah digantikan oleh logika di atas):
        // $results = $builder->orderBy('m.id_monev', 'ASC')->get()->getResultArray(); 

        // GANTI MENJADI SEPERTI INI:
        $recordsFiltered = $builder->countAllResults(false);
        if ($length != -1) $builder->limit($length, $start);
        $results = $builder->get()->getResultArray();

        // 📊 Hitung Total Data Murni
        $totalBuilder = $db->table('dtsen_monev m');
        if ($roleId == 5) {
            $totalBuilder->where('m.created_by', $nikPetugas);
        }
        $recordsTotal = $totalBuilder->countAllResults();

        $data = [];
        $no = $start + 1;

        foreach ($results as $row) {
            // Label Status Pengerjaan
            $badgeStatus = ($row['status_monev'] == 'Selesai')
                ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Selesai</span>'
                : '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu</span>';

            // 🧠 Cerdas: Jika data SINDEN ada, pakai alamat SINDEN. Jika tidak ada, pakai alamat mentah Excel.
            $alamatFinal = !empty($row['alamat_sinden']) ? $row['alamat_sinden'] : $row['alamat'];
            $rtFinal = !empty($row['rt_sinden']) ? str_pad($row['rt_sinden'], 3, '0', STR_PAD_LEFT) : $row['rt'];
            $rwFinal = !empty($row['rw_sinden']) ? str_pad($row['rw_sinden'], 3, '0', STR_PAD_LEFT) : $row['rw'];

            $alamatStr = $alamatFinal . '<br><small class="text-muted">RT ' . $rtFinal . ' / RW ' . $rwFinal . '</small>';

            // Tombol Eksekusi
            $btnAksi = '
                <button class="btn btn-sm btn-primary shadow-sm" onclick="lihatDetailMonev(' . $row['id_monev'] . ')" title="Lakukan Monev">
                    <i class="fas fa-camera"></i> Eksekusi
                </button>
            ';

            $data[] = [
                $no++,
                '<b>' . esc($row['nama_target']) . '</b><br><small class="text-muted">NIK: ' . esc($row['nik']) . '</small>',
                $alamatStr,
                $badgeStatus,
                $btnAksi
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
    | 🚀 FUNGSI IMPORT EXCEL MONEV PKH
    |--------------------------------------------------------------------------
    */
    public function import_excel()
    {
        if (!$this->request->isAJAX()) {
            return exit('Tidak diizinkan');
        }

        $file = $this->request->getFile('file_excel');
        $periode = $this->request->getPost('periode') ?? 'Triwulan 2 ' . date('Y');

        // Validasi File
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File Excel tidak ditemukan atau tidak valid.']);
        }

        $extension = $file->getClientExtension();
        if (!in_array($extension, ['xls', 'xlsx'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format file harus .xls atau .xlsx']);
        }

        try {
            // Load file Excel menggunakan IOFactory
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $sukses = 0;
            $gagal  = 0;
            $nikPetugas = session()->get('nik') ?? 'system';

            $dataInsert = [];

            // Looping data Excel (Mulai dari indeks 1 untuk melewati baris Header/Judul Kolom)
            for ($i = 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];

                // Pemetaan Kolom Excel ke Database
                // Format Excel: No | Nama Target | Provinsi | Kabupaten | Kecamatan | Kelurahan | Alamat | RT | RW | Nama SDM | Status | NIK

                $nik = trim($row[11] ?? ''); // Indeks 11 adalah Kolom NIK (Kolom L)

                // Lewati jika NIK kosong (baris kosong di akhir excel)
                if (empty($nik)) {
                    $gagal++;
                    continue;
                }

                $dataInsert[] = [
                    'nama_target'  => trim($row[1] ?? ''),
                    'provinsi'     => trim($row[2] ?? ''),
                    'kabupaten'    => trim($row[3] ?? ''),
                    'kecamatan'    => trim($row[4] ?? ''),
                    'kelurahan'    => trim($row[5] ?? ''),
                    'alamat'       => trim($row[6] ?? ''),
                    'rt'           => trim($row[7] ?? ''),
                    'rw'           => trim($row[8] ?? ''),
                    'nama_sdm'     => trim($row[9] ?? ''),
                    'status_kpm'   => trim($row[10] ?? ''), // Kolom Status
                    'nik'          => $nik,
                    'periode'      => $periode,
                    'status_monev' => 'Menunggu',
                    'created_by'   => $nikPetugas
                ];

                $sukses++;
            }

            // Eksekusi Insert Batch ke Database jika ada data yang valid
            if (!empty($dataInsert)) {
                // Untuk mencegah duplikasi saat import ulang di periode yang sama,
                // idealnya kita kosongkan dulu data Monev periode tersebut untuk NIK petugas terkait (opsional)
                // $this->monevModel->where('periode', $periode)->where('created_by', $nikPetugas)->delete();

                $this->monevModel->insertBatch($dataInsert);
            }

            return $this->response->setJSON([
                'status'  => true,
                'message' => "Import selesai! <b>{$sukses}</b> KPM berhasil ditambahkan untuk periode {$periode}."
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem saat membaca Excel: ' . $e->getMessage()
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 FUNGSI AMBIL DETAIL DATA & FOTO
    |--------------------------------------------------------------------------
    */
    public function get_detail($id)
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $db = \Config\Database::connect();

        $data = $db->table('dtsen_monev m')
            ->select("
                m.id_monev, 
                m.nama_target, 
                m.nik, 
                m.status_monev,
                a.nama as nama_sinden,
                COALESCE(k.foto_rumah, rt.foto_rumah) as foto_rumah,
                COALESCE(k.foto_rumah_dalam, rt.foto_rumah_dalam) as foto_rumah_dalam,
                bk.foto_kpm_kks,
                CASE 
                    WHEN mk.foto_kks IS NOT NULL AND mk.foto_kks != '' THEN mk.foto_kks 
                    ELSE mk.foto_kepemilikan 
                END as foto_kks_final
            ")

            ->join('dtsen_art a', 'a.nik = m.nik AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->join('dtsen_bansos_kks bk', 'bk.nik_kpm = m.nik', 'left')
            ->join('dtsen_master_kks mk', 'mk.nik = m.nik', 'left')
            ->where('m.id_monev', $id)
            ->get()->getRowArray();

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan di database.']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 FUNGSI TANDAI MONEV SELESAI
    |--------------------------------------------------------------------------
    */
    public function tandai_selesai()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $id = $this->request->getPost('id_monev');
        $catatan = $this->request->getPost('catatan_pendamping');

        $this->monevModel->update($id, [
            'status_monev'       => 'Selesai',
            'catatan_pendamping' => $catatan,
            'updated_by'         => session()->get('nik')
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Monev berhasil ditandai selesai!']);
    }
}
