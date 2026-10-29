<?php

namespace App\Controllers;

use App\Models\AssessmentModel;
use App\Models\Dtks\AuthModel;
use App\Models\Dtsen\DtsenKkModel;
use App\Models\Dtsen\DtsenArtModel;
use App\Models\Dtsen\DtsenSeModel;
use App\Models\Dtsen\DtsenRtModel;
use App\Models\WilayahModel;

class Assessment extends BaseController
{
    protected $assessmentModel;
    protected $AuthModel;
    protected $kkModel;
    protected $artModel;
    protected $seModel;
    protected $rtModel;
    protected $wilayahModel;
    protected $db;

    public function __construct()
    {
        $this->assessmentModel = new AssessmentModel();
        $this->AuthModel       = new AuthModel();
        $this->kkModel         = new DtsenKkModel();
        $this->artModel        = new DtsenArtModel();
        $this->seModel         = new DtsenSeModel();
        $this->rtModel         = new DtsenRtModel();
        $this->wilayahModel    = new WilayahModel();
        $this->db              = \Config\Database::connect();

        helper(['form', 'url', 'watermark_helper', 'opdtks_helper', 'opdtsen_helper']);
    }

    /**
     * 🏠 Halaman Utama Daftar Asesmen
     */
    public function index()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return redirect()->to(base_url('login'));
        }

        $kodeDesa = $user['kode_desa'] ?? session()->get('kode_desa');
        $builder = $this->db->table('slrt_assessment')->where('deleted_at IS NULL');
        if (!empty($kodeDesa)) {
            $builder->where('kode_desa', $kodeDesa);
        }

        $totalAsesmen = (clone $builder)->countAllResults();
        $bulanIni     = (clone $builder)->where('MONTH(tanggal_asesmen)', date('m'))->where('YEAR(tanggal_asesmen)', date('Y'))->countAllResults();
        $siapCetak    = (clone $builder)->where('status', 1)->countAllResults();

        $data = [
            'title'         => 'Asesmen Lapad Ruhama / SLRT',
            'user_login'    => $user,
            'totalAsesmen'  => $totalAsesmen,
            'bulanIni'      => $bulanIni,
            'siapCetak'     => $siapCetak,
        ];

        return view('assessment/index', $data);
    }

    /**
     * 📊 Server-side DataTables Endpoint
     */
    public function datatable()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->response->setJSON(['data' => []]);
        }

        $roleId       = (int) ($user['role_id'] ?? 4);
        $kodeDesa     = $user['kode_desa'] ?? session()->get('kode_desa');
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');

        $request = service('request');
        $params = [
            'kode_desa'     => $kodeDesa,
            'wilayah_tugas' => $wilayahTugas,
            'search'        => $request->getPost('search')['value'] ?? ''
        ];

        $builder = $this->assessmentModel->getDatatables($params, $roleId);

        // Hitung total filtered
        $recordsFiltered = (clone $builder)->countAllResults(false);

        // Sorting & Limit
        $start  = (int) ($request->getPost('start') ?? 0);
        $length = (int) ($request->getPost('length') ?? 10);
        $builder->orderBy('a.id', 'DESC')->limit($length, $start);

        $query = $builder->get()->getResultArray();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {
            $btnCetak  = '<a href="' . base_url('assessment/cetak/' . $row['id']) . '" target="_blank" class="btn btn-xs btn-primary mr-1" title="Cetak Berkas Lengkap"><i class="fas fa-print"></i> Cetak</a>';
            $btnWord   = '<a href="' . base_url('assessment/export-word/' . $row['id']) . '" class="btn btn-xs btn-success mr-1" title="Unduh File Word (.docx)"><i class="fas fa-file-word"></i> Word</a>';
            $btnDetail = '<button type="button" class="btn btn-xs btn-info mr-1 btn-detail" data-id="' . $row['id'] . '" title="Lihat Detail"><i class="fas fa-eye"></i></button>';
            $btnHapus  = '<button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row['id'] . '" data-no="' . esc($row['nomor_asesmen']) . '" title="Hapus Asesmen"><i class="fas fa-trash"></i></button>';

            $tglAsesmen = !empty($row['tanggal_asesmen']) ? date('d-m-Y', strtotime($row['tanggal_asesmen'])) : '-';

            $data[] = [
                'no'             => $no++,
                'nomor_asesmen'  => '<strong>' . esc($row['nomor_asesmen']) . '</strong><br><small class="text-muted"><i class="far fa-calendar-alt"></i> ' . $tglAsesmen . '</small>',
                'kk_info'        => '<strong>' . esc($row['no_kk']) . '</strong><br><small>' . esc($row['alamat']) . ' RT ' . str_pad($row['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . ' RW ' . str_pad($row['rw'] ?? '0', 3, '0', STR_PAD_LEFT) . '</small>',
                'sasaran'        => '<strong>' . esc($row['nama_sasaran']) . '</strong><br><small class="text-secondary">NIK: ' . esc($row['nik_sasaran']) . '</small>',
                'permasalahan'   => '<span class="badge badge-warning text-wrap text-left">' . esc($row['riwayat_penyakit'] ?: 'Permasalahan Sosial/Kesehatan') . '</span><br><small class="text-info font-weight-bold">' . esc($row['permohonan_bantuan'] ?: 'Rekomendasi Lapad Ruhama') . '</small>',
                'petugas'        => esc($row['fasilitator_nama']),
                'status'         => '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Berkas Siap</span>',
                'action'         => '<div class="btn-group">' . $btnCetak . $btnWord . $btnDetail . $btnHapus . '</div>',
            ];
        }

        return $this->response->setJSON([
            'draw'            => (int) ($request->getPost('draw') ?? 1),
            'recordsTotal'    => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * 📝 Halaman Form Pembuatan Asesmen Baru
     */
    public function tambah()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return redirect()->to(base_url('login'));
        }

        $kodeDesa = $user['kode_desa'] ?? session()->get('kode_desa');
        $nomorPreview = $this->assessmentModel->generateNomorAsesmen($kodeDesa ?? '3205332006');

        // Kategori PPKS dari Kemensos
        $kategoriPpks = $this->db->table('ppks_kategori')->get()->getResultArray();

        // Profil Pejabat Desa / SLRT
        $profilLembaga = $this->db->table('lembaga_profil lp')
            ->where('lp.lp_user', $user['id_user'])
            ->orWhere('lp.lp_kode', $kodeDesa)
            ->get()
            ->getRowArray();

        if (empty($profilLembaga)) {
            $profilLembaga = $this->db->table('lembaga_profil')->orderBy('lp_id', 'ASC')->first();
        }

        // Ambil riwayat penyakit & permohonan unik untuk pilihan autocomplete / dropdown
        $listRiwayatPenyakit   = $this->assessmentModel->getDistinctRiwayatPenyakit();
        $listPermohonanBantuan = $this->assessmentModel->getDistinctPermohonanBantuan();

        $data = [
            'title'                 => 'Buat Asesmen Lapad Ruhama',
            'user_login'            => $user,
            'nomorPreview'          => $nomorPreview,
            'kategoriPpks'          => $kategoriPpks,
            'profilLembaga'         => $profilLembaga,
            'listRiwayatPenyakit'   => $listRiwayatPenyakit,
            'listPermohonanBantuan' => $listPermohonanBantuan,
        ];

        return view('assessment/tambah', $data);
    }

    /**
     * 🔍 AJAX Autocomplete Pencarian KK / NIK
     */
    public function searchKk()
    {
        $term = trim((string) $this->request->getGet('q'));
        if (strlen($term) < 3) {
            return $this->response->setJSON(['results' => []]);
        }

        $user = $this->AuthModel->getUserId();
        $kodeDesa = $user['kode_desa'] ?? session()->get('kode_desa');

        $builder = $this->db->table('dtsen_kk kk')
            ->select('
                kk.id_kk,
                kk.no_kk,
                kk.kepala_keluarga,
                kk.alamat,
                rt.rt,
                rt.rw,
                se.kategori_desil,
                art.nik AS matched_nik,
                art.nama AS matched_nama
            ')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left')
            ->join('dtsen_art art', 'art.id_kk = kk.id_kk AND art.deleted_at IS NULL', 'left')
            ->where('kk.deleted_at IS NULL');

        if (!empty($kodeDesa)) {
            $builder->where('rt.kode_desa', $kodeDesa);
        }

        $builder->groupStart()
            ->like('kk.no_kk', $term)
            ->orLike('kk.kepala_keluarga', $term)
            ->orLike('art.nik', $term)
            ->orLike('art.nama', $term)
            ->groupEnd()
            ->groupBy('kk.id_kk')
            ->limit(20);

        $results = $builder->get()->getResultArray();

        $output = [];
        foreach ($results as $r) {
            $desil = $r['kategori_desil'] ? "Desil {$r['kategori_desil']}" : 'Non-Desil';
            $infoMatched = '';
            if (!empty($r['matched_nik']) && (str_contains($r['matched_nik'], $term) || stripos($r['matched_nama'], $term) !== false)) {
                $infoMatched = " [Cocok: {$r['matched_nama']} - {$r['matched_nik']}]";
            }
            $output[] = [
                'id'    => $r['id_kk'],
                'text'  => "{$r['no_kk']} - {$r['kepala_keluarga']}{$infoMatched} ({$r['alamat']} RT {$r['rt']}/RW {$r['rw']} | {$desil})"
            ];
        }

        return $this->response->setJSON(['results' => $output]);
    }

    /**
     * 🔍 AJAX Mengambil Detail Lengkap KK & Susunan ART Terpilih
     */
    public function getKkDetail($id_kk)
    {
        $id_kk = (int) $id_kk;
        $kk = $this->db->table('dtsen_kk kk')
            ->select('
                kk.*,
                rt.rt,
                rt.rw,
                rt.latitude,
                rt.longitude,
                rt.foto_kamar_mandi,
                v.name AS nama_desa,
                d.name AS nama_kecamatan,
                reg.name AS nama_kabupaten,
                se.kategori_desil,
                se.rata_penghasilan_bulanan,
                se.status_bpjs,
                se.status_kks,
                se.status_kip
            ')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('tb_villages v', 'v.id = rt.kode_desa', 'left')
            ->join('tb_districts d', 'd.id = v.district_id', 'left')
            ->join('tb_regencies reg', 'reg.id = v.regency_id', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left')
            ->where('kk.id_kk', $id_kk)
            ->where('kk.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (!$kk) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data KK tidak ditemukan']);
        }

        // Ambil Anggota Rumah Tangga (ART)
        $artList = $this->db->table('dtsen_art art')
            ->select('
                art.id_art,
                art.nik,
                art.nama,
                art.hubungan_keluarga,
                art.jenis_kelamin,
                art.tempat_lahir,
                art.tanggal_lahir,
                art.status_kawin,
                art.pekerjaan,
                art.program_bansos,
                sk.StatusKawin AS nama_status_kawin,
                pk.pk_nama AS nama_pekerjaan,
                shdk.jenis_shdk AS nama_shdk
            ')
            ->join('tb_status_kawin sk', 'sk.idStatus = art.status_kawin', 'left')
            ->join('tb_penduduk_pekerjaan pk', 'pk.pk_id = art.pekerjaan', 'left')
            ->join('tb_shdk shdk', 'shdk.id = art.hubungan_keluarga', 'left')
            ->where('art.id_kk', $id_kk)
            ->where('art.deleted_at IS NULL')
            ->orderBy('art.hubungan_keluarga', 'ASC')
            ->orderBy('art.tanggal_lahir', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung umur masing-masing ART
        foreach ($artList as &$a) {
            $a['umur'] = '-';
            if (!empty($a['tanggal_lahir']) && $a['tanggal_lahir'] !== '0000-00-00') {
                $birth = new \DateTime($a['tanggal_lahir']);
                $today = new \DateTime('today');
                $a['umur'] = $birth->diff($today)->y . ' Thn';
            }
        }
        unset($a);

        return $this->response->setJSON([
            'status'  => true,
            'kk'      => $kk,
            'art'     => $artList,
        ]);
    }

    /**
     * 💾 Simpan Data Asesmen Baru (+ Upload & Auto-Watermark Foto Pasien)
     */
    public function simpan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi login telah berakhir']);
        }

        $post = $this->request->getPost();

        // Validasi Sederhana
        if (empty($post['id_kk']) || empty($post['nik_sasaran']) || empty($post['nik_pembuat_pernyataan'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Harap lengkapi KK, Sasaran Pasien, dan Pembuat Pernyataan!']);
        }

        $id_kk = (int) $post['id_kk'];
        $kk = $this->kkModel->find($id_kk);
        if (!$kk) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data KK tidak valid!']);
        }

        $artSasaran = $this->artModel->where('nik', $post['nik_sasaran'])->first();
        $artPernyataan = $this->artModel->where('nik', $post['nik_pembuat_pernyataan'])->first();

        $kodeDesa = $user['kode_desa'] ?? session()->get('kode_desa') ?? '3205332006';
        $nomorAsesmen = $this->assessmentModel->generateNomorAsesmen($kodeDesa);

        // Snapshot RT & Geotagging untuk Watermark
        $rt = $this->rtModel->find($kk['id_rt']);
        $lat = !empty($rt['latitude']) ? $rt['latitude'] : '-7.451975';
        $lng = !empty($rt['longitude']) ? $rt['longitude'] : '107.643160';

        // Nama Wilayah untuk Watermark
        $desaRow = $this->wilayahModel
            ->select("tb_villages.name AS desa, tb_districts.name AS kecamatan, tb_regencies.name AS kabupaten, tb_provinces.name AS provinsi")
            ->join('tb_districts', 'tb_districts.id = tb_villages.district_id', 'left')
            ->join('tb_regencies', 'tb_regencies.id = tb_villages.regency_id', 'left')
            ->join('tb_provinces', 'tb_provinces.id = tb_villages.province_id', 'left')
            ->where('tb_villages.id', $kodeDesa)
            ->get()
            ->getRowArray();

        $namaDesa      = strtoupper($desaRow['desa'] ?? 'PASIRLANGU');
        $namaKecamatan = strtoupper($desaRow['kecamatan'] ?? 'PAKENJENG');
        $namaKabupaten = strtoupper($desaRow['kabupaten'] ?? 'GARUT');
        $namaProvinsi  = strtoupper($desaRow['provinsi'] ?? 'JAWA BARAT');
        $wilayahFull   = "Desa {$namaDesa}, Kec. {$namaKecamatan}, Kab. {$namaKabupaten}, Prov. {$namaProvinsi}";

        // ==============================================================
        // 📸 PROSES UPLOAD FOTO 4 PILAR (PASIEN, DEPAN, DALAM, MCK) + WATERMARK
        // ==============================================================
        $processUploadFoto = function ($fieldName, $prefix) use ($kk, $artSasaran, $user, $lat, $lng, $wilayahFull) {
            $file = $this->request->getFile($fieldName);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadDir = FCPATH . 'uploads/assessment/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $timestamp = time();
                $ext = $file->getExtension() ?: 'jpg';
                $cleanPrefix = preg_replace('/[^a-zA-Z0-9_-]/', '', $prefix);
                $newName = $cleanPrefix . '_' . $timestamp . '.' . $ext;
                $finalPath = $uploadDir . $newName;

                $file->move($uploadDir, $newName, true);

                // Terapkan Watermark Native GD SINDEN
                applyWatermarkPremium($finalPath, [
                    'no_kk'     => (string) $kk['no_kk'],
                    'kepala'    => (string) ($artSasaran['nama'] ?? $kk['kepala_keluarga']),
                    'petugas'   => (string) ($user['fullname'] ?? 'Petugas Lapangan'),
                    'tanggal'   => date('d F Y'),
                    'latitude'  => (string) $lat,
                    'longitude' => (string) $lng,
                    'wilayah'   => (string) $wilayahFull
                ]);

                // Kompresi ukuran file target max 500kb
                recompressImageToTarget($finalPath, 500);

                return 'uploads/assessment/' . $newName;
            }
            return null;
        };

        // 1. Upload Foto Pasien (Wajib)
        $pathFotoPasien = $processUploadFoto('foto_pasien', 'pasien_' . preg_replace('/\s+/', '', $post['nik_sasaran']));

        // 2. Upload Foto Rumah Depan (Jika ada upload baru, gunakan itu; jika tidak, gunakan data DTSEN)
        $uploadedDepan = $processUploadFoto('foto_depan', 'depan_' . $kk['no_kk']);
        $pathFotoDepan = $uploadedDepan ?: ($kk['foto_rumah'] ?? null);

        // 3. Upload Foto Rumah Dalam (Jika ada upload baru, gunakan itu; jika tidak, gunakan data DTSEN)
        $uploadedDalam = $processUploadFoto('foto_dalam', 'dalam_' . $kk['no_kk']);
        $pathFotoDalam = $uploadedDalam ?: ($kk['foto_rumah_dalam'] ?? null);

        // 4. Upload Foto Kamar Mandi / MCK (Jika ada upload baru, gunakan itu; jika tidak, gunakan data DTSEN)
        $uploadedMck = $processUploadFoto('foto_kamar_mandi', 'mck_' . $kk['no_kk']);
        $pathFotoMck = $uploadedMck ?: ($rt['foto_kamar_mandi'] ?? $kk['foto_kamar_mandi'] ?? null);

        // Ambil Data Pejabat dari Profil Lembaga
        $profilLembaga = $this->db->table('lembaga_profil lp')
            ->where('lp.lp_user', $user['id_user'])
            ->orWhere('lp.lp_kode', $kodeDesa)
            ->get()
            ->getRowArray();

        $dataInsert = [
            'nomor_asesmen'           => $nomorAsesmen,
            'tanggal_asesmen'         => !empty($post['tanggal_asesmen']) ? $post['tanggal_asesmen'] : date('Y-m-d'),
            'id_kk'                   => $id_kk,
            'no_kk'                   => $kk['no_kk'],
            'nik_sasaran'             => $post['nik_sasaran'],
            'nama_sasaran'            => $artSasaran['nama'] ?? '-',
            'nik_pembuat_pernyataan'  => $post['nik_pembuat_pernyataan'],
            'nama_pembuat_pernyataan' => $artPernyataan['nama'] ?? '-',
            'pembuat_pernyataan_hp'   => trim($post['pembuat_pernyataan_hp'] ?? '0831-6612-8082'),
            'kategori_ppks_id'        => !empty($post['kategori_ppks_id']) ? (int) $post['kategori_ppks_id'] : 24, // default Fakir Miskin
            'riwayat_penyakit'        => trim($post['riwayat_penyakit'] ?? ''),
            'permasalahan_tambahan'   => trim($post['permasalahan_tambahan'] ?? ''),
            'permohonan_bantuan'      => trim($post['permohonan_bantuan'] ?? 'Mendapatkan Pelayanan BPJS KIS PBI'),
            'foto_pasien'             => $pathFotoPasien,
            'foto_depan'              => $pathFotoDepan,
            'foto_dalam'              => $pathFotoDalam,
            'foto_kamar_mandi'        => $pathFotoMck,
            'fasilitator_nama'        => $user['fullname'] ?? session()->get('fullname') ?? 'Fasilitator',
            'fasilitator_nik'         => $user['nik'] ?? session()->get('nik') ?? '-',
            'ketua_puskesos_nama'     => $post['ketua_puskesos_nama'] ?? ($profilLembaga['lp_puskesos_ketua'] ?? 'AJAT SUDRAJAT'),
            'supervisor_nama'         => $post['supervisor_nama'] ?? ($profilLembaga['lp_supervisor_slrt'] ?? 'ABDURRAHMAN LAKA, S.Sos'),
            'supervisor_niat'         => $post['supervisor_niat'] ?? ($profilLembaga['lp_supervisor_niat'] ?? 'NIAT. 32.05.33-0183'),
            'kepala_desa_nama'        => $post['kepala_desa_nama'] ?? ($profilLembaga['lp_kepala'] ?? 'TATA RUSTANDI'),
            'camat_nama'              => $post['camat_nama'] ?? ($profilLembaga['lp_camat'] ?? 'Camat Pakenjeng'),
            'status'                  => 1,
            'kode_desa'               => $kodeDesa,
            'created_by'              => $user['id_user'] ?? session()->get('id') ?? 1,
        ];

        try {
            $inserted = $this->assessmentModel->insert($dataInsert);
            if (!$inserted) {
                return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data asesmen ke basis data.']);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal simpan asesmen: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menyimpan data asesmen: ' . $e->getMessage()
            ]);
        }

        $newId = $this->assessmentModel->getInsertID();

        return $this->response->setJSON([
            'status'       => true,
            'message'      => 'Asesmen Lapad Ruhama berhasil disimpan!',
            'redirect_url' => base_url('assessment'),
            'cetak_url'    => base_url('assessment/cetak/' . $newId),
            'word_url'     => base_url('assessment/export-word/' . $newId),
        ]);
    }

    /**
     * 🔍 AJAX Detail Asesmen untuk Modal
     */
    public function detail($id)
    {
        $data = $this->assessmentModel->getDetailCetak((int) $id);
        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    /**
     * 🖨️ Cetak Dokumen Lengkap (1:1 Sesuai Template Fisik Lapad Ruhama)
     */
    public function cetak($id)
    {
        $data = $this->assessmentModel->getDetailCetak((int) $id);
        if (!$data) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Dokumen Asesmen tidak ditemukan.');
        }

        $data['title'] = 'Cetak Asesmen Lapad Ruhama - ' . $data['nomor_asesmen'];

        return view('assessment/cetak', $data);
    }

    /**
     * 📄 Ekspor Dokumen Word (.docx) Menggunakan Template Word Resmi
     */
    public function exportWord($id)
    {
        $asesmen = $this->assessmentModel->getDetailCetak((int) $id);
        if (!$asesmen) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Dokumen Asesmen tidak ditemukan.');
        }

        $templatePath = FCPATH . 'uploads/templates/Template_Asesmen_PMKS.docx';
        if (!file_exists($templatePath)) {
            return $this->response->setStatusCode(404)->setBody('File template Word tidak ditemukan di server.');
        }

        $cleanNomor = preg_replace('/[^A-Za-z0-9]/', '_', $asesmen['nomor_asesmen']);
        $cleanNama  = preg_replace('/[^A-Za-z0-9]/', '_', $asesmen['nama_sasaran']);
        $fileName   = "Asesmen_Lapad_Ruhama_{$cleanNomor}_{$cleanNama}.docx";
        $outputDir  = WRITEPATH . 'uploads/assessment_docx/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $outputPath = $outputDir . $fileName;

        try {
            $tp = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

            $namaDesa      = $asesmen['nama_desa'] ?: 'Pasirlangu';
            $namaKecamatan = $asesmen['nama_kecamatan'] ?: 'Pakenjeng';
            $namaKabupaten = $asesmen['nama_kabupaten'] ?: 'Garut';

            $rawAlamat     = ($asesmen['kk_alamat'] ?: '-') . ' RT ' . str_pad($asesmen['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . ' RW ' . str_pad($asesmen['rw'] ?? '0', 3, '0', STR_PAD_LEFT);
            $alamatLengkap = toTitleCase($rawAlamat);

            $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $tglTimestamp = !empty($asesmen['tanggal_asesmen']) ? strtotime($asesmen['tanggal_asesmen']) : time();
            $tanggalIndo = date('d', $tglTimestamp) . ' ' . $bulanIndo[(int)date('m', $tglTimestamp)] . ' ' . date('Y', $tglTimestamp);

            // 1. Data Sasaran PMKS (Capitalize Each Word)
            $sasaranTtl = !empty($asesmen['sasaran']['tanggal_lahir']) && $asesmen['sasaran']['tanggal_lahir'] !== '0000-00-00'
                ? toTitleCase($asesmen['sasaran']['tempat_lahir'] ?? 'Garut') . ', ' . date('d', strtotime($asesmen['sasaran']['tanggal_lahir'])) . ' ' . $bulanIndo[(int)date('m', strtotime($asesmen['sasaran']['tanggal_lahir']))] . ' ' . date('Y', strtotime($asesmen['sasaran']['tanggal_lahir']))
                : '-';

            $programBansos = !empty($asesmen['sasaran']['program_bansos'])
                ? toTitleCase($asesmen['sasaran']['program_bansos'])
                : ($asesmen['status_kks'] === 'Ya' ? 'PKH, Sembako' : '-');

            // 2. Data Pembuat Pernyataan Keaslian Dokumen (Capitalize Each Word)
            $pembuatTtl = !empty($asesmen['pembuat_pernyataan']['tanggal_lahir']) && $asesmen['pembuat_pernyataan']['tanggal_lahir'] !== '0000-00-00'
                ? toTitleCase($asesmen['pembuat_pernyataan']['tempat_lahir'] ?? 'Garut') . ', ' . date('d', strtotime($asesmen['pembuat_pernyataan']['tanggal_lahir'])) . ' ' . $bulanIndo[(int)date('m', strtotime($asesmen['pembuat_pernyataan']['tanggal_lahir']))] . ' ' . date('Y', strtotime($asesmen['pembuat_pernyataan']['tanggal_lahir']))
                : '-';

            $pembuatJk    = ($asesmen['pembuat_pernyataan']['jenis_kelamin'] ?? 1) == 2 ? 'Perempuan' : 'Laki-Laki';
            $pembuatAgama = !empty($asesmen['pembuat_pernyataan']['agama']) ? toTitleCase($asesmen['pembuat_pernyataan']['agama']) : 'Islam';

            // 3. Pejabat & Tanda Tangan (Capitalize Each Word)
            $puskesosNama   = toTitleCase($asesmen['ketua_puskesos_nama'] ?: ($asesmen['profil_desa']['lp_puskesos'] ?? 'Ajat Sudrajat'));
            $kadesNama      = toTitleCase($asesmen['kepala_desa_nama'] ?: ($asesmen['profil_desa']['lp_kades'] ?? 'Tata Rustandi'));
            $supervisorNama = toTitleCase($asesmen['supervisor_nama'] ?: ($asesmen['profil_desa']['lp_supervisor_slrt'] ?? 'Abdurrahman Laka, S.Sos'));

            $supervisorNiat = $asesmen['supervisor_niat'] ?: ($asesmen['profil_desa']['lp_supervisor_niat'] ?? '32.05.33-0183');
            if (!empty($supervisorNiat) && !str_starts_with(strtoupper($supervisorNiat), 'NIAT.')) {
                $supervisorNiat = 'NIAT. ' . $supervisorNiat;
            }

            $camatNama = !empty($asesmen['camat_nama']) && $asesmen['camat_nama'] !== 'Camat Pakenjeng'
                ? toTitleCase($asesmen['camat_nama'])
                : toTitleCase($asesmen['profil_desa']['lp_camat'] ?? '...................................');

            // 4. Kop Desa
            $desaAlamat = !empty($asesmen['profil_desa']['lp_alamat'])
                ? toTitleCase($asesmen['profil_desa']['lp_alamat'])
                : ('Jl. Desa ' . toTitleCase($namaDesa) . ' Kec. ' . toTitleCase($namaKecamatan) . ' Kab. ' . toTitleCase($namaKabupaten));

            $cleanDesaSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaDesa));
            $desaEmail = !empty($asesmen['profil_desa']['lp_email'])
                ? strtolower($asesmen['profil_desa']['lp_email'])
                : ('info@' . ($cleanDesaSlug ?: 'pasirlangu') . '.desa.id');

            $desaKodepos = !empty($asesmen['profil_desa']['lp_kodepos'])
                ? $asesmen['profil_desa']['lp_kodepos']
                : '44164';

            // Helper escape XML khusus template Word
            $esc = static fn($val) => htmlspecialchars((string) ($val ?? ''), ENT_QUOTES, 'UTF-8');

            // Set Semua Nilai Placeholder Variabel Tunggal (Capitalize Each Word)
            $tp->setValues([
                'alamat'                => $esc($alamatLengkap),
                'desa'                  => $esc(toTitleCase($namaDesa)),
                'kecamatan'             => $esc(toTitleCase($namaKecamatan)),
                'kabupaten'             => $esc(toTitleCase($namaKabupaten)),
                'nama'                  => $esc(toTitleCase($asesmen['nama_sasaran'])),
                'nik'                   => $esc($asesmen['nik_sasaran']),
                'ttl'                   => $esc($sasaranTtl),
                'status_kawin'          => $esc(toTitleCase($asesmen['sasaran']['nama_status_kawin'] ?? 'Belum Kawin')),
                'pekerjaan'             => $esc(toTitleCase($asesmen['sasaran']['nama_pekerjaan'] ?? 'Belum Bekerja')),
                'jml_art'               => $esc(count($asesmen['anggota_keluarga'])),
                'bansos'                => $esc($programBansos),
                'riwayat_penyakit'      => $esc(toTitleCase(!empty($asesmen['riwayat_penyakit']) ? $asesmen['riwayat_penyakit'] : '-')),
                'permasalahan_tambahan' => $esc(toTitleCase(!empty($asesmen['permasalahan_tambahan']) ? $asesmen['permasalahan_tambahan'] : 'Keluarga Miskin/Tidak Mampu')),
                'status_kks_ket'        => $esc(toTitleCase(($asesmen['status_kks'] === 'Ya') ? 'Memiliki BPJS/KKS' : 'Tidak Memiliki BPJS APBN KIS')),
                'permohonan'            => $esc(toTitleCase(!empty($asesmen['permohonan_bantuan']) ? $asesmen['permohonan_bantuan'] : 'Mendapatkan Pelayanan BPJS KIS PBI')),
                'tgl_asesmen'           => $esc($tanggalIndo),
                'fasilitator_nama'      => $esc(toTitleCase($asesmen['fasilitator_nama'])),
                'puskesos_nama'         => $esc($puskesosNama),
                'kades_nama'            => $esc($kadesNama),
                'supervisor_nama'       => $esc($supervisorNama),
                'supervisor_niat'       => $esc($supervisorNiat),
                'camat_wilayah'         => $esc(toTitleCase($namaKecamatan)),
                'camat_nama'            => $esc($camatNama),
                'kabupaten_upper'       => $esc(strtoupper($namaKabupaten)),
                'kecamatan_upper'       => $esc(strtoupper($namaKecamatan)),
                'desa_upper'            => $esc(strtoupper($namaDesa)),
                'desa_alamat'           => $esc($desaAlamat),
                'desa_email'            => $esc($desaEmail),
                'desa_kodepos'          => $esc($desaKodepos),
                'p_nama'                => $esc(toTitleCase($asesmen['nama_pembuat_pernyataan'])),
                'p_ttl'                 => $esc($pembuatTtl),
                'p_nik'                 => $esc($asesmen['nik_pembuat_pernyataan']),
                'p_no_kk'               => $esc($asesmen['no_kk']),
                'p_jk'                  => $esc($pembuatJk),
                'p_agama'               => $esc($pembuatAgama),
                'p_alamat'              => $esc($alamatLengkap . ' Desa ' . toTitleCase($namaDesa) . ' Kec. ' . toTitleCase($namaKecamatan)),
                'p_hp'                  => $esc($asesmen['pembuat_pernyataan_hp'] ?: '0831-6612-8082'),
            ]);

            // 5. Susunan Anggota Keluarga (ART) dengan cloneRowAndSetValues
            $artRows = [];
            $noArt = 1;
            if (!empty($asesmen['anggota_keluarga'])) {
                foreach ($asesmen['anggota_keluarga'] as $art) {
                    $umurText = '-';
                    if (!empty($art['tanggal_lahir']) && $art['tanggal_lahir'] !== '0000-00-00') {
                        $birth = new \DateTime($art['tanggal_lahir']);
                        $now   = new \DateTime('today');
                        $umurText = $birth->diff($now)->y . ' Thn';
                    }

                    $penghasilanText = '-';
                    if ($art['hubungan_keluarga'] == 1) {
                        $penghasilanText = !empty($asesmen['rata_penghasilan_bulanan'])
                            ? 'Rp ' . number_format($asesmen['rata_penghasilan_bulanan'], 0, ',', '.')
                            : '< 1 Jt';
                    }

                    $artRows[] = [
                        'art_no'          => $esc($noArt++),
                        'art_nama'        => $esc(toTitleCase($art['nama'])),
                        'art_shdk'        => $esc(toTitleCase($art['nama_shdk'] ?? '-')),
                        'art_umur'        => $esc($umurText),
                        'art_pekerjaan'   => $esc(toTitleCase($art['nama_pekerjaan'] ?? 'Belum Bekerja')),
                        'art_penghasilan' => $esc($penghasilanText),
                    ];
                }
            } else {
                $artRows[] = [
                    'art_no'          => '1',
                    'art_nama'        => $esc(toTitleCase($asesmen['nama_sasaran'])),
                    'art_shdk'        => 'Kepala Keluarga',
                    'art_umur'        => '-',
                    'art_pekerjaan'   => $esc(toTitleCase($asesmen['sasaran']['nama_pekerjaan'] ?? 'Belum Bekerja')),
                    'art_penghasilan' => '< 1 Jt',
                ];
            }

            $tp->cloneRowAndSetValues('art_no', $artRows);

            // Simpan file hasil proses
            $tp->saveAs($outputPath);

            // 6. Injeksi 4 Pilar Foto Dokumentasi Lapangan ke dalam Arsip DOCX
            // Mapping resmi berkas di dalam Template_Asesmen_PMKS.docx:
            // - word/media/image1.png : Logo SLRT (Kop Halaman 1) - TETAP ASLI
            // - word/media/image3.png : Logo Pemkab Garut (Kop Surat Pernyataan Halaman 2) - TETAP ASLI
            // - word/media/image4.jpeg (rId8)  : FOTO 1 - FOTO PASIEN / SASARAN
            // - word/media/image5.jpeg (rId9)  : FOTO 2 - FOTO RUMAH TAMPAK DEPAN
            // - word/media/image6.jpeg (rId10) : FOTO 3 - FOTO RUMAH TAMPAK DALAM
            // - word/media/image8.jpeg (rId14) : FOTO 4 - FOTO WC / KAMAR MANDI
            $zip = new \ZipArchive();
            if ($zip->open($outputPath) === true) {
                // Pastikan integritas relasi foto MCK / Sanitasi (rId14 -> image8.jpeg)
                $docXml  = $zip->getFromName('word/document.xml');
                $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
                $modifiedDoc  = false;
                $modifiedRels = false;

                // 1. Jika di word/document.xml foto MCK masih merujuk ke rId10 (Rumah Dalam), pisahkan ke rId14
                if ($docXml !== false) {
                    $firstRid10 = strpos($docXml, 'r:embed="rId10"');
                    if ($firstRid10 !== false) {
                        $secondRid10 = strpos($docXml, 'r:embed="rId10"', $firstRid10 + 15);
                        if ($secondRid10 !== false) {
                            $docXml = substr_replace($docXml, 'r:embed="rId14"', $secondRid10, strlen('r:embed="rId10"'));
                            $modifiedDoc = true;
                        }
                    }
                }

                // 2. Jika di document.xml.rels belum ada rId14 untuk image8.jpeg, daftarkan relasinya
                if ($relsXml !== false && strpos($relsXml, 'rId14') === false) {
                    $relTag = '<Relationship Id="rId14" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/image8.jpeg"/>';
                    $relsXml = str_replace('</Relationships>', $relTag . '</Relationships>', $relsXml);
                    $modifiedRels = true;
                }

                if ($modifiedDoc) {
                    $zip->addFromString('word/document.xml', $docXml);
                }
                if ($modifiedRels) {
                    $zip->addFromString('word/_rels/document.xml.rels', $relsXml);
                }

                // 3. Pastikan image8.jpeg ada sebagai default fallback jika belum ada
                if ($zip->getFromName('word/media/image8.jpeg') === false) {
                    $img6 = $zip->getFromName('word/media/image6.jpeg');
                    if ($img6 !== false) {
                        $zip->addFromString('word/media/image8.jpeg', $img6);
                    }
                }

                // 1. Foto Pasien / Sasaran (image4.jpeg)
                if (!empty($asesmen['foto_pasien']) && file_exists(FCPATH . $asesmen['foto_pasien'])) {
                    $this->injectDocxImage($zip, FCPATH . $asesmen['foto_pasien'], 'word/media/image4.jpeg');
                }

                // 2. Foto Rumah Tampak Depan (image5.jpeg)
                $fotoDepan = $asesmen['foto_depan'] ?: $asesmen['kk_foto_depan'];
                if (!empty($fotoDepan) && file_exists(FCPATH . $fotoDepan)) {
                    $this->injectDocxImage($zip, FCPATH . $fotoDepan, 'word/media/image5.jpeg');
                }

                // 3. Foto Rumah Tampak Dalam (image6.jpeg)
                $fotoDalam = $asesmen['foto_dalam'] ?: $asesmen['kk_foto_dalam'];
                if (!empty($fotoDalam) && file_exists(FCPATH . $fotoDalam)) {
                    $this->injectDocxImage($zip, FCPATH . $fotoDalam, 'word/media/image6.jpeg');
                }

                // 4. Foto WC / Kamar Mandi (image8.jpeg)
                $fotoMck = $asesmen['foto_kamar_mandi'] ?: ($asesmen['rt_foto_mck'] ?? $asesmen['rt_foto_kamar_mandi'] ?? $asesmen['foto_mck'] ?? null);
                if (!empty($fotoMck) && file_exists(FCPATH . $fotoMck)) {
                    $this->injectDocxImage($zip, FCPATH . $fotoMck, 'word/media/image8.jpeg');
                }

                $zip->close();
            }

            return $this->response->download($outputPath, null)->setFileName($fileName);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal generate Word Asesmen: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setBody('Gagal memproses template Word: ' . $e->getMessage());
        }
    }

    /**
     * 🖼️ Injeksi Gambar ke DOCX dengan Konversi JPEG Otomatis jika Dibutuhkan
     */
    private function injectDocxImage(\ZipArchive $zip, string $filePath, string $targetEntry): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $imageInfo = @getimagesize($filePath);
        if (!$imageInfo) {
            return;
        }

        // Jika sudah murni JPEG, langsung masukkan ke arsip ZIP
        if ($imageInfo['mime'] === 'image/jpeg') {
            $zip->addFile($filePath, $targetEntry);
            return;
        }

        // Jika format lain (misal PNG / WebP), ubah sementara ke JPEG agar kompatibel penuh dengan Word
        $tempJpg = WRITEPATH . 'cache/' . uniqid('docx_img_') . '.jpg';
        $img = null;
        if ($imageInfo['mime'] === 'image/png') {
            $img = @imagecreatefrompng($filePath);
        } elseif ($imageInfo['mime'] === 'image/webp') {
            $img = @imagecreatefromwebp($filePath);
        }

        if ($img) {
            $w = imagesx($img);
            $h = imagesy($img);
            $bg = imagecreatetruecolor($w, $h);
            $white = imagecolorallocate($bg, 255, 255, 255);
            imagefilledrectangle($bg, 0, 0, $w, $h, $white);
            imagecopy($bg, $img, 0, 0, 0, 0, $w, $h);
            imagejpeg($bg, $tempJpg, 92);
            imagedestroy($img);
            imagedestroy($bg);

            if (file_exists($tempJpg)) {
                $zip->addFile($tempJpg, $targetEntry);
            }
        }
    }

    /**
     * 🗑️ Hapus Asesmen (Soft Delete)
     */
    public function hapus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $deleted = $this->assessmentModel->delete((int) $id);
        if ($deleted) {
            return $this->response->setJSON(['status' => true, 'message' => 'Asesmen berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus asesmen.']);
    }
}
