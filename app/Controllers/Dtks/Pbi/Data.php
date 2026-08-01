<?php

namespace App\Controllers\Dtks\Pbi;

use App\Controllers\BaseController;
use App\Models\PbiMasterDataModel;
use App\Models\Dtks\AuthModel; // 🚀 Gunakan pola UsulanBansos

class Data extends BaseController
{
    protected $pbiModel;
    protected $AuthModel;

    public function __construct()
    {
        $this->pbiModel = new PbiMasterDataModel();
        $this->AuthModel = new AuthModel(); // 🚀 Inisialisasi AuthModel
    }

    public function index()
    {
        $data = [
            'title' => 'Master Data PBI-JKN (Aktif)',
        ];
        return view('dtks/pbi/v_data_aktif', $data);
    }

    public function datatable()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $post = $this->request->getPost();

        // 🚀 Ambil data user dari model Auth seperti di UsulanBansos
        $userInfo = $this->AuthModel->getUserId();
        $kodeDesa = $userInfo['kode_desa'] ?? session()->get('kode_desa');
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');

        $draw   = (int) ($post['draw'] ?? 1);
        $start  = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $search = $post['search']['value'] ?? '';

        $builder = $this->pbiModel->builder()->where('status_kepesertaan', 'Aktif');

        if (!empty($kodeDesa)) {
            $builder->where('kode_desa', $kodeDesa);
        }

        // 🔐 PARSE wilayah_tugas → pasangan RW–RT (POLA USULAN BANSOS)
        if (!empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $builder->groupStart();

            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);

                if ($rw !== '') {
                    $builder->orGroupStart()
                        ->where('rw', str_pad($rw, 3, '0', STR_PAD_LEFT)); // Format PBI 3 digit

                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        $rtVariants = [];
                        foreach ($rts as $rt) {
                            $rtVariants[] = str_pad($rt, 3, '0', STR_PAD_LEFT);
                        }
                        $builder->whereIn('rt', $rtVariants);
                    }
                    $builder->groupEnd();
                }
            }
            $builder->groupEnd();
        }

        // 🎛️ Filter UI (RW / RT)
        if (!empty($post['rw'])) $builder->where('rw', str_pad($post['rw'], 3, '0', STR_PAD_LEFT));
        if (!empty($post['rt'])) $builder->where('rt', str_pad($post['rt'], 3, '0', STR_PAD_LEFT));

        // 🔍 Pencarian
        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama', $search)
                ->orLike('nik', $search)
                ->orLike('no_kk', $search)
                ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        if ($length != -1) $builder->limit($length, $start);

        $results = $builder->orderBy('rw', 'ASC')->orderBy('rt', 'ASC')->get()->getResultArray();

        // 4️⃣ Hitung Total Data Murni (Beserta Gembok Wilayah Tugas)
        $totalBuilder = $this->pbiModel->builder()->where('status_kepesertaan', 'Aktif');
        if (!empty($kodeDesa)) $totalBuilder->where('kode_desa', $kodeDesa);

        // 🔐 Gembok Total Records dengan pola yang sama
        if (!empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $totalBuilder->groupStart();
            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);
                if ($rw !== '') {
                    $totalBuilder->orGroupStart()->where('rw', str_pad($rw, 3, '0', STR_PAD_LEFT));
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        $rtVariants = [];
                        foreach ($rts as $rt) $rtVariants[] = str_pad($rt, 3, '0', STR_PAD_LEFT);
                        $totalBuilder->whereIn('rt', $rtVariants);
                    }
                    $totalBuilder->groupEnd();
                }
            }
            $totalBuilder->groupEnd();
        }
        $recordsTotal = $totalBuilder->countAllResults();

        // 🚀 Tangkap role_id user yang sedang login
        $roleId = (int) ($userInfo['role_id'] ?? session()->get('role_id') ?? 99);

        $data = [];
        $no = $start + 1;

        foreach ($results as $row) {
            $btnAksi = '-'; // Default kosong untuk role >= 4

            // 🔐 Hanya role 1, 2, 3 (Kades, Sekdes, dsb) yang boleh melihat tombol Non-Aktif
            if ($roleId < 4) {
                $btnAksi = '
                    <button type="button" class="btn btn-sm btn-outline-danger shadow-sm" onclick="nonAktifkan(\'' . $row['id'] . '\', \'' . esc($row['nama']) . '\')" title="Non-Aktifkan Data">
                        <i class="fas fa-user-times"></i> Non-Aktif
                    </button>
                ';
            }

            $alamat = ($row['kampung'] ?? '-') . '<br><small class="text-muted">RT ' . str_pad($row['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($row['rw'] ?? '0', 3, '0', STR_PAD_LEFT) . '</small>';

            // ... (lanjutan array $data[] biarkan sama seperti sebelumnya) ...

            $data[] = [
                $no++,
                '<b>' . esc($row['nama']) . '</b><br><small class="text-muted">NIK: ' . $row['nik'] . ' <a href="javascript:void(0)" onclick="copyText(\'' . $row['nik'] . '\')" class="text-primary ms-1"><i class="far fa-copy"></i></a></small>',
                $row['no_kk'] ? $row['no_kk'] . ' <a href="javascript:void(0)" onclick="copyText(\'' . $row['no_kk'] . '\')" class="text-primary ms-1"><i class="far fa-copy"></i></a>' : '-',
                '<span class="badge bg-success"><i class="fas fa-id-card"></i> ' . esc($row['no_kis'] ?? '-') . '</span>',
                esc($row['faskes_tk1'] ?? '-'),
                $alamat,
                $btnAksi
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    // ... (Fungsi import_excel, cek_nik, simpan_manual, proses_nonaktif biarkan utuh seperti sebelumnya) ...

    public function import_excel()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File tidak valid']);
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $sukses = 0;
            $kodeDesa = session()->get('kode_desa') ?? '3205332004';
            $periode = date('F Y');

            for ($i = 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];

                $noKk = trim($row[1] ?? '');
                $nik = trim($row[2] ?? '');
                $nama = trim($row[3] ?? '');
                $alamatRaw = trim($row[6] ?? '');
                $noKis = trim($row[7] ?? '');
                $statusRaw = trim($row[8] ?? '');

                if (empty($nik) || empty($nama)) continue;

                // 🧠 LOGIKA 1: PENENTUAN STATUS
                $statusUpper = strtoupper($statusRaw);
                $isAktif = (strpos($statusUpper, 'AKTIF') !== false && strpos($statusUpper, 'NON') === false);

                $statusKepesertaan = $isAktif ? 'Aktif' : 'Non-Aktif';
                $alasanNonAktif = $isAktif ? null : $statusRaw;

                // 🧠 LOGIKA 2: MENCARI id_art
                $db = \Config\Database::connect();

                $artData = $db->table('dtsen_art a')
                    ->select('a.id_art, rt.rt, rt.rw')
                    ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
                    ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                    ->where('a.nik', $nik)
                    ->where('a.deleted_at IS NULL')
                    ->get()->getRowArray();

                $idArt = $artData ? $artData['id_art'] : null;
                $rt = $artData ? $artData['rt'] : '000';
                $rw = $artData ? $artData['rw'] : '000';

                $dataPbi = [
                    'nik' => $nik,
                    'id_art' => $idArt,
                    'no_kk' => $noKk,
                    'nama' => $nama,
                    'no_kis' => $noKis,
                    'kampung' => $alamatRaw,
                    'rt' => $rt,
                    'rw' => $rw,
                    'kode_desa' => $kodeDesa,
                    'status_kepesertaan' => $statusKepesertaan,
                    'alasan_nonaktif' => $alasanNonAktif,
                    'periode_sinkron_terakhir' => $periode,
                    'updated_by' => session()->get('id')
                ];

                $cekExists = $this->pbiModel->where('nik', $nik)->first();
                if ($cekExists) {
                    $this->pbiModel->update($cekExists['id'], $dataPbi);
                } else {
                    $dataPbi['created_by'] = session()->get('id');
                    $this->pbiModel->insert($dataPbi);
                }

                $sukses++;
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => "Berhasil memproses <b>{$sukses}</b> data."
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    public function cek_nik()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $nik = trim($this->request->getPost('nik'));

        $cekPbi = $this->pbiModel->where('nik', $nik)->first();
        if ($cekPbi && $cekPbi['status_kepesertaan'] == 'Aktif') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Warga ini sudah terdaftar sebagai peserta PBI Aktif!'
            ]);
        }

        $db = \Config\Database::connect();

        $artData = $db->table('dtsen_art a')
            ->select('a.id_art, a.nik, a.nama, k.no_kk, k.alamat as kampung, rt.rt, rt.rw')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->where('a.nik', $nik)
            ->where('a.deleted_at IS NULL')
            ->get()->getRowArray();

        if ($artData) {
            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'id_art'  => $artData['id_art'],
                    'nik'     => $artData['nik'],
                    'nama'    => $artData['nama'],
                    'no_kk'   => $artData['no_kk'],
                    'kampung' => $artData['kampung'] ?? '-',
                    'rt'      => $artData['rt'] ?? '000',
                    'rw'      => $artData['rw'] ?? '000',
                    'pbi_id'  => $cekPbi ? $cekPbi['id'] : null
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'NIK tidak ditemukan di database kependudukan Pasirlangu!'
            ]);
        }
    }

    public function simpan_manual()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $post = $this->request->getPost();
        $kodeDesa = session()->get('kode_desa') ?? '3205332004';

        $dataPbi = [
            'nik'                => $post['nik'],
            'id_art'             => $post['id_art'],
            'no_kk'              => $post['no_kk'],
            'nama'               => $post['nama'],
            'no_kis'             => $post['no_kis'],
            'faskes_tk1'         => $post['faskes_tk1'],
            'kampung'            => $post['kampung'],
            'rt'                 => str_pad($post['rt'], 3, '0', STR_PAD_LEFT),
            'rw'                 => str_pad($post['rw'], 3, '0', STR_PAD_LEFT),
            'kode_desa'          => $kodeDesa,
            'status_kepesertaan' => 'Aktif',
            'alasan_nonaktif'    => null,
            'periode_sinkron_terakhir' => date('F Y'),
            'updated_by'         => session()->get('id')
        ];

        if (!empty($post['pbi_id'])) {
            $this->pbiModel->update($post['pbi_id'], $dataPbi);
        } else {
            $dataPbi['created_by'] = session()->get('id');
            $this->pbiModel->insert($dataPbi);
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Data PBI berhasil ditambahkan!']);
    }

    public function proses_nonaktif()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $id = $this->request->getPost('id');
        $alasan = $this->request->getPost('alasan');

        if (empty($id) || empty($alasan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap.']);
        }

        $dataUpdate = [
            'status_kepesertaan' => 'Non-Aktif',
            'alasan_nonaktif'    => $alasan,
            'tanggal_nonaktif'   => date('Y-m-d'),
            'dinonaktifkan_oleh' => session()->get('id')
        ];

        $this->pbiModel->update($id, $dataUpdate);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Data PBI berhasil dinonaktifkan.'
        ]);
    }
}
