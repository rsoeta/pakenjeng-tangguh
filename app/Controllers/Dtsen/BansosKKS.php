<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Traits\WilayahFilterTrait;


class BansosKKS extends BaseController
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
        // Data dasar untuk view
        $data = [
            'title' => 'Dokumentasi Bansos',
            'user'  => session()->get(),
        ];

        // 👇 UBAH BARIS INI SESUAIKAN DENGAN NAMA FOLDER BARU ANDA 👇
        return view('dtsen/bansos_kks/v_bansos_kks', $data);
    }

    public function datatable()
    {
        $user = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? 4;
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        // 🚀 TANGKAP REQUEST FILTER DARI VIEW
        $filterRw = $this->request->getPost('filter_rw');
        $filterRt = $this->request->getPost('filter_rt');
        $filterTahap = $this->request->getPost('filter_tahap');
        $filterLocked = $this->request->getPost('filter_locked');

        $builder = $this->db->table('dtsen_bansos_kks b')
            ->select('b.*, m.alamat, r.rt, r.rw')
            ->join('dtsen_master_kks m', 'm.nik = b.nik_kpm', 'left')
            ->join('dtsen_art a', 'a.nik = b.nik_kpm', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt r', 'r.id_rt = k.id_rt', 'left');

        // =======================================================
        // 🔐 TERAPKAN TRAIT WILAYAH FILTER
        // =======================================================
        $filterData = [
            'kode_desa'     => $kodeDesa,
            'wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')
        ];

        // 💥 Boom! Gunakan mesin Trait untuk memproses pola wilayah_tugas yang rumit
        $this->applyWilayahFilter($builder, $filterData, $roleId);

        // ==========================================
        // 🔍 FILTER DINAMIS DARI FRONTEND (Manual)
        // ==========================================
        if (!empty($filterRw)) {
            $builder->where('r.rw', str_pad($filterRw, 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filterRt)) {
            $builder->where('r.rt', str_pad($filterRt, 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filterTahap)) {
            $builder->where('b.tahap_salur', $filterTahap);
        }
        // 🚀 TERAPKAN FILTER KUNCI JIKA DIPILIH
        if ($filterLocked !== '' && $filterLocked !== null) {
            $builder->where('b.is_locked', (int)$filterLocked);
        }

        // Kunci Data Unik agar tidak duplikat dan Urutkan yang terbaru paling atas
        $builder->groupBy('b.id')->orderBy('b.updated_at', 'ASC');

        $query = $builder->get()->getResultArray();
        $data = [];
        $no = 1;

        // --- 🛡️ FUNGSI BANTUAN: SENSOR DATA SENSITIF + TOMBOL SALIN ---
        $maskNumber = function ($number, $type) {
            $number = trim($number ?? '');
            if (empty($number) || $number === '-' || $number === 'NOKKS') return esc($number);

            $full = esc($number);
            $len = strlen($full);

            // Tentukan Class dan Title Tombol berdasarkan jenis data
            $btnClass = ($type === 'nik') ? 'btnCopyNik' : 'btnCopyNoKK';
            $btnTitle = ($type === 'nik') ? 'Salin NIK' : 'Salin No KK';

            if ($len <= 8) {
                $masked = $full;
                $hoverEffect = '';
            } else {
                $masked = substr($full, 0, 8) . str_repeat('*', $len - 8);
                $hoverEffect = ' onmouseenter="this.innerText=\'' . $full . '\'" onmouseleave="this.innerText=\'' . $masked . '\'" ontouchstart="this.innerText=\'' . $full . '\'" ontouchend="this.innerText=\'' . $masked . '\'" title="Tahan/Arahkan kursor untuk melihat utuh" ';
            }

            return '
            <div class="d-inline-flex align-items-center" style="gap: 5px;">
                <span class="fw-bold text-primary" style="cursor:pointer;"' . $hoverEffect . '>' . $masked . '</span>
                <button type="button" class="btn btn-outline-secondary btn-xs ' . $btnClass . ' py-0 px-1" data-value="' . $full . '" title="' . $btnTitle . '">
                    <i class="fas fa-copy"></i>
                </button>
            </div>';
        };

        foreach ($query as $row) {
            $fotoPath = !empty($row['foto_kpm_kks']) ? base_url('uploads/bansos/' . $row['foto_kpm_kks']) : base_url('assets/img/no-image.png');
            $colFoto = '<a href="' . $fotoPath . '" data-lightbox="gallery-kpm" data-title="Foto KPM: ' . esc($row['nama_kpm']) . '"><img src="' . $fotoPath . '" class="rounded shadow-sm" style="width: 70px; height: 90px; object-fit: cover; border: 2px solid #fff; cursor: pointer;" title="Klik untuk memperbesar"></a>';
            $nominal = 'Rp ' . number_format($row['nominal_cair'], 0, ',', '.');

            $nikMasked = $maskNumber($row['nik_kpm'], 'nik');
            $kksMasked = $maskNumber($row['nomor_kks'], 'nokk');

            // ... (kode atasnya tetap sama: $nikMasked, $kksMasked, $nominal) ...

            // 🚀 CEK STATUS KUNCI
            $isLocked = (int)($row['is_locked'] ?? 0);

            // 🚀 INDIKATOR GEMBOK VISUAL (Biar di layar kelihatan keren)
            $badgeLock = $isLocked ? '<span class="badge bg-danger p-2 ml-1" title="Data Telah Diverifikasi & Dikunci"><i class="fas fa-lock"></i></span>' : '';

            $colDetail = '
            <div class="row align-items-center">
                <span style="display: none;">' . esc($row['nik_kpm']) . ' ' . esc($row['nomor_kks']) . '</span>

                <div class="col-md-6 mb-2 mb-md-0">
                    <span class="fw-bold text-dark d-block" style="font-size:1.1rem;">' . esc($row['nama_kpm']) . '</span>
                    <div class="text-muted small mt-1 d-flex align-items-center"><i class="fas fa-id-card mr-2"></i> ' . $nikMasked . '</div>
                </div>
                <div class="col-md-6 d-none d-md-block text-md-right border-left">
                    <div class="mb-1">
                        <span class="badge bg-primary p-2 mr-1">' . esc($row['jenis_bansos']) . '</span>
                        <span class="badge bg-success p-2">' . $nominal . '</span>
                        ' . $badgeLock . ' </div>
                    <div class="small text-muted mt-1 d-flex align-items-center justify-content-md-end">
                         <i class="fas fa-credit-card mr-2"></i> ' . $kksMasked . '
                    </div>
                </div>
                <div class="col-12 mt-1">
                    <small class="text-muted"><i class="fas fa-map-marker-alt text-danger mr-1"></i> ' . esc($row['alamat'] ?? '-') . ' RT' . ($row['rt'] ?? '00') . '/RW' . ($row['rw'] ?? '00') . '</small>
                </div>
            </div>';

            // ==========================================
            // 🚀 LOGIKA TOMBOL AKSI BERDASARKAN ROLE & KUNCI
            // ==========================================
            $btnEdit   = '';
            $btnDelete = '';
            $btnLock   = '';

            // 1. TOMBOL KUNCI (Eksklusif Role <= 3)
            if ($roleId <= 3) {
                if ($isLocked) {
                    $btnLock = '<button class="btn btn-sm btn-danger btn-toggle-lock shadow-sm" data-id="' . $row['id'] . '" data-status="1" title="Buka Kunci"><i class="fas fa-lock"></i></button>';
                } else {
                    $btnLock = '<button class="btn btn-sm btn-outline-secondary btn-toggle-lock" data-id="' . $row['id'] . '" data-status="0" title="Kunci Data"><i class="fas fa-unlock"></i></button>';
                }
            }

            // 2. TOMBOL EDIT (Hanya bisa diklik jika belum dikunci, ATAU yang login adalah Role <= 3)
            if (!$isLocked || $roleId <= 3) {
                $btnEdit = '<button class="btn btn-sm btn-outline-warning btn-edit" data-id="' . $row['id'] . '" title="Edit Data"><i class="fas fa-edit"></i></button>';
            } else {
                // Untuk Role Pentri jika data sudah dikunci
                $btnEdit = '<button class="btn btn-sm btn-secondary disabled" title="Data Terkunci"><i class="fas fa-lock"></i></button>';
            }

            // 3. TOMBOL DELETE (Eksklusif Role <= 3)
            if ($roleId <= 3) {
                $btnDelete = '<button class="btn btn-sm btn-outline-danger btn-delete" data-id="' . $row['id'] . '" title="Hapus"><i class="fas fa-trash-alt"></i></button>';
            }

            // Gabungkan semua tombol
            $btnAction = '
            <div class="d-flex flex-row justify-content-center align-items-center" style="gap: 5px;">
                ' . $btnLock . $btnEdit . $btnDelete . '
            </div>';

            $data[] = [$no++, $colFoto, $colDetail, $btnAction];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    // ========================================================
    // 🔍 PENCARIAN AJAX (Validasi Silang & Filter Wilayah Tugas)
    // ========================================================
    public function cari_nik_ajax()
    {
        $searchTerm = trim($this->request->getPost('searchTerm'));

        // 1. Ambil wilayah_tugas dari user yang sedang login
        $user = $this->authModel->getUserId();
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');

        // 2. Build Query Utama (Join ART -> KK -> RT)
        $builder = $this->db->table('dtsen_master_kks m')
            ->select('a.nik, a.nama as nama_kpm, m.no_kks')
            ->join('dtsen_art a', 'a.nik = m.nik') // Validasi dengan penduduk aktif
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left') // Ambil ID RT dari KK
            ->join('dtsen_rt r', 'r.id_rt = k.id_rt', 'left') // Ambil data RW & RT
            ->groupStart()
            ->like('a.nik', $searchTerm)
            ->orLike('m.no_kks', $searchTerm)
            ->orLike('a.nama', $searchTerm)
            ->groupEnd()
            // 🛡️ KUNCI UTAMA: GroupBy NIK agar tidak muncul ganda jika ada data join yang double
            ->groupBy('a.nik')
            ->limit(15);

        // 3. 🔐 PARSE wilayah_tugas → pasangan RW–RT (KUNCI VISIBILITAS)
        if (!empty($wilayahTugas)) {
            $wilayahPairs = [];
            $blocks = explode('|', $wilayahTugas);

            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);

                foreach (explode(',', $rtList) as $rt) {
                    $rt = trim($rt);
                    if ($rw !== '' && $rt !== '') {
                        $wilayahPairs[] = ['rw' => $rw, 'rt' => $rt];
                    }
                }
            }

            // 4. Terapkan Filter Wilayah agar petugas hanya bisa mencari warga di wilayahnya
            if (!empty($wilayahPairs)) {
                $builder->groupStart();
                foreach ($wilayahPairs as $pair) {
                    $builder->orGroupStart()
                        ->where('r.rw', $pair['rw'])
                        ->where('r.rt', $pair['rt'])
                        ->groupEnd();
                }
                $builder->groupEnd();
            }
        }

        // 5. Eksekusi Query dan Format Hasil untuk Select2
        $query = $builder->get()->getResultArray();

        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'id'       => $row['nik'],
                'text'     => "NIK: " . $row['nik'] . " - " . strtoupper($row['nama_kpm']) . " (KKS: " . ($row['no_kks'] ?? '-') . ")",
                'nama_kpm' => strtoupper($row['nama_kpm']),
                'no_kks'   => $row['no_kks'] ?? '-'
            ];
        }

        return $this->response->setJSON($data);
    }

    // ========================================================
    // 💾 SIMPAN DATA (Insert & Update dalam 1 Fungsi, dengan Logika File yang Cerdas)
    // ========================================================
    public function simpan()
    {
        try {
            $post = $this->request->getPost();
            $id = $post['id'] ?? null; // Ambil ID jika ada (untuk Update)

            if (empty($post['nik_kpm'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data KPM tidak valid!']);
            }

            // Gabungkan Tahun dan Tahap
            $tahap_salur_final = $post['tahap_salur'] . ' Tahun ' . $post['tahun_salur'];

            $fotoKpm   = $this->request->getFile('foto_kpm_kks');
            $fotoBukti = $this->request->getFile('foto_bukti_transaksi');

            // 1. Ambil data lama jika ini proses UPDATE
            $oldData = null;
            if ($id) {
                $oldData = $this->db->table('dtsen_bansos_kks')->where('id', $id)->get()->getRowArray();
            }

            $nik = $post['nik_kpm'];
            $kks = ($post['no_kks'] !== '-' && !empty($post['no_kks'])) ? $post['no_kks'] : 'NOKKS';
            $lat = $post['latitude'] ?: '0';
            $lng = $post['longitude'] ?: '0';
            $timestamp = time();

            // 2. LOGIKA FILE FOTO (Update vs Insert)
            $uploadPath = FCPATH . 'uploads/bansos/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            // -- Foto KPM --
            if ($fotoKpm && $fotoKpm->isValid()) {
                $namaFotoKpm = "KPM_{$nik}_{$kks}_{$timestamp}.jpg";
                $this->_applyWatermark($fotoKpm, $uploadPath . $namaFotoKpm, $post);
                // Jika update, hapus foto lama jika perlu (opsional)
            } else {
                // Jika update dan tidak upload baru, pakai nama foto lama
                $namaFotoKpm = ($id && $oldData) ? $oldData['foto_kpm_kks'] : '';
            }

            // -- Foto Bukti --
            if ($fotoBukti && $fotoBukti->isValid()) {
                $namaFotoBukti = "STRUK_{$nik}_{$kks}_{$timestamp}.jpg";
                $this->_applyWatermark($fotoBukti, $uploadPath . $namaFotoBukti, $post);
            } else {
                $namaFotoBukti = ($id && $oldData) ? $oldData['foto_bukti_transaksi'] : '';
            }

            // 3. PREPARASI DATA
            $dataSave = [
                'nik_kpm'              => $nik,
                'nama_kpm'             => $post['nama_kpm'],
                'nomor_kks'            => $kks,
                'jenis_bansos'         => $post['jenis_bansos'],
                'tahap_salur'          => $tahap_salur_final,
                'nominal_cair'         => (int) str_replace('.', '', $post['nominal_cair'] ?? 0),
                'status_salur'         => $post['status_salur'],
                'foto_kpm_kks'         => $namaFotoKpm,
                'foto_bukti_transaksi' => $namaFotoBukti,
                'latitude'             => $lat,
                'longitude'            => $lng,
            ];

            // 4. EKSEKUSI (INSERT vs UPDATE)
            if ($id) {
                // PROSES UPDATE
                $dataSave['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('dtsen_bansos_kks')->where('id', $id)->update($dataSave);
                $msg = 'Dokumentasi berhasil diperbarui!';
            } else {
                // PROSES INSERT
                $dataSave['created_by'] = session()->get('id') ?? 0;
                $dataSave['created_at'] = date('Y-m-d H:i:s');
                $dataSave['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('dtsen_bansos_kks')->insert($dataSave);
                $msg = 'Dokumentasi berhasil disimpan!';
            }

            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ========================================================
    // 🖼️ FUNGSI WATERMARK: NATIVE GD PREMIUM (SINDEN STYLE)
    // ========================================================
    private function _applyWatermark($file, $destinationPath, $post)
    {
        // 1. Pindahkan file terlebih dahulu dari form ke folder uploads
        $file->move(dirname($destinationPath), basename($destinationPath), true);

        // 2. Bersihkan cache server agar PHP tidak membaca status file lama
        clearstatcache();

        // 3. Cek fisik: Apakah file benar-benar sudah mendarat di folder?
        if (!file_exists($destinationPath)) {
            throw new \Exception("File foto menguap! Gagal ditulis ke dalam disk hosting.");
        }

        // 4. AUTO-ROTATE (EXIF) dengan Pelindung Try-Catch
        try {
            if (function_exists('exif_imagetype') && exif_imagetype($destinationPath) === IMAGETYPE_JPEG) {
                $exif = @exif_read_data($destinationPath); // Gunakan @ agar warning disembunyikan
                if ($exif && isset($exif['Orientation'])) {
                    $source = @imagecreatefromjpeg($destinationPath);
                    if ($source) {
                        $deg = 0;
                        switch ($exif['Orientation']) {
                            case 3:
                                $deg = 180;
                                break;
                            case 6:
                                $deg = 270;
                                break;
                            case 8:
                                $deg = 90;
                                break;
                        }
                        if ($deg) {
                            $source = imagerotate($source, $deg, 0);
                            imagejpeg($source, $destinationPath, 95);
                        }
                        imagedestroy($source);
                    }
                }
            }
        } catch (\Throwable $th) {
            // Jika HP mengirim metadata EXIF yang cacat, abaikan saja, jangan hancurkan prosesnya
            log_message('warning', 'Rotasi EXIF gagal (diabaikan): ' . $th->getMessage());
        }

        // 5. Resize awal pakai CI4 agar ukuran tidak terlalu raksasa (hemat hosting)
        try {
            \Config\Services::image('gd')
                ->withFile($destinationPath)
                ->resize(1280, 1280, true, 'auto')
                ->save($destinationPath);
        } catch (\Throwable $th) {
            throw new \Exception("Gagal melakukan Resize Gambar: " . $th->getMessage());
        }

        // ============================================================
        // 6. MULAI NATIVE GD WATERMARK (Adaptasi applyWatermarkPremium)
        // (Lanjutkan dengan kode watermark layer hitam transparan Anda di sini...)
        // ============================================================
        $info = getimagesize($destinationPath);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($destinationPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($destinationPath);
                break;
            default:
                return false;
        }

        $width  = imagesx($image);
        $height = imagesy($image);

        // Responsive Font Size (Premium Scaling)
        $baseFont = max(16, intval($width * 0.015));
        $lineSpacing = intval($baseFont * 1.45);

        // Load Fonts (Coba Ubuntu, jika tidak ada fallback ke Arial)
        $fontBold    = FCPATH . "assets/fonts/Ubuntu-Bold.ttf";
        $fontRegular = FCPATH . "assets/fonts/Ubuntu-Regular.ttf";

        if (!file_exists($fontBold)) $fontBold = FCPATH . "assets/fonts/arial.ttf";
        if (!file_exists($fontRegular)) $fontRegular = FCPATH . "assets/fonts/arial.ttf";

        // Siapkan Data Teks
        $kks   = $post['no_kks'] !== '-' && !empty($post['no_kks']) ? $post['no_kks'] : 'TIDAK BAWA KKS';
        $waktu = date('d/m/Y H:i:s') . ' WIB';

        $textLines = [
            ['font' => $fontBold,    'text' => "== SINDEN BANSOS =="],
            ['font' => $fontRegular, 'text' => "KPM: {$post['nama_kpm']} • NIK: {$post['nik_kpm']}"],
            ['font' => $fontRegular, 'text' => "Bansos: {$post['jenis_bansos']} • KKS: {$kks}"],
            ['font' => $fontRegular, 'text' => "Lokasi: {$post['latitude']}, {$post['longitude']} • Validated"],
            ['font' => $fontRegular, 'text' => "Waktu Salur: {$waktu}"]
        ];

        // Hitung Box Width agar Pas
        $padding = 28;
        $maxTextWidth = 0;

        foreach ($textLines as $line) {
            if (file_exists($line['font'])) {
                $bbox = imagettfbbox($baseFont, 0, $line['font'], $line['text']);
                $lineW = intval($bbox[2] - $bbox[0]);
                if ($lineW > $maxTextWidth) {
                    $maxTextWidth = $lineW;
                }
            }
        }

        $boxWidth  = intval($maxTextWidth + ($padding * 2));
        $boxHeight = intval(($lineSpacing * count($textLines)) + ($padding * 1.2));

        // Posisi Box (Kiri Bawah Premium)
        $boxX = 20;
        $boxY = intval($height - $boxHeight - 20);

        // Layer Background (Glass Dark)
        $layer = imagecreatetruecolor($boxWidth, $boxHeight);
        imagesavealpha($layer, true);
        $trans = imagecolorallocatealpha($layer, 0, 0, 0, 127);
        imagefill($layer, 0, 0, $trans);

        $bgColor = imagecolorallocatealpha($layer, 0, 0, 0, 68);
        $radius = 18;

        // Rounded rectangle manual
        imagefilledrectangle($layer, $radius, 0, $boxWidth - $radius, $boxHeight, $bgColor);
        imagefilledrectangle($layer, 0, $radius, $boxWidth, $boxHeight - $radius, $bgColor);
        imagefilledellipse($layer, $radius, $radius, $radius * 2, $radius * 2, $bgColor);
        imagefilledellipse($layer, $boxWidth - $radius, $radius, $radius * 2, $radius * 2, $bgColor);
        imagefilledellipse($layer, $radius, $boxHeight - $radius, $radius * 2, $radius * 2, $bgColor);
        imagefilledellipse($layer, $boxWidth - $radius, $boxHeight - $radius, $radius * 2, $radius * 2, $bgColor);

        // Text Colors
        $white = imagecolorallocate($layer, 255, 255, 255);
        $shadow = imagecolorallocatealpha($layer, 0, 0, 0, 74);

        // Draw Text + Shadow
        $cursorY = $padding;
        foreach ($textLines as $line) {
            if (file_exists($line['font'])) {
                imagettftext($layer, $baseFont, 0, intval($padding + 2), intval($cursorY + 2), $shadow, $line['font'], $line['text']);
                imagettftext($layer, $baseFont, 0, intval($padding), intval($cursorY), $white, $line['font'], $line['text']);
            }
            $cursorY += intval($lineSpacing);
        }

        // Merge ke Main Image
        imagecopy($image, $layer, intval($boxX), intval($boxY), 0, 0, $boxWidth, $boxHeight);

        // Save Image
        if ($mime === 'image/jpeg') {
            imagejpeg($image, $destinationPath, 95);
        } else {
            imagepng($image, $destinationPath);
        }

        imagedestroy($image);
        imagedestroy($layer);
    }

    // --- TAMBAHKAN FUNGSI BARU DI BAWAHNYA ---
    public function edit_ajax($id)
    {
        // Ambil data detail beserta join wilayah agar Select2 bisa sinkron
        $data = $this->db->table('dtsen_bansos_kks b')
            ->select('b.*, m.no_kks')
            ->join('dtsen_master_kks m', 'm.nik = b.nik_kpm', 'left')
            ->where('b.id', $id)
            ->get()
            ->getRowArray();

        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ]);
    }

    // ========================================================
    // 🌍 GET DYNAMIC RW (Filter Role, Wilayah & Kode Desa)
    // ========================================================
    public function get_rw_ajax()
    {
        $user = $this->authModel->getUserId();
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');
        $roleId = session()->get('role_id') ?? 4;

        // 🚀 SUPER AMAN: Cek di session, kalau tidak ada cek di data user db
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        if ($roleId <= 3 || empty($wilayahTugas)) {

            // 🛡️ PENCEGAHAN KEBOCORAN MUTLAK
            if (empty($kodeDesa)) {
                return $this->response->setJSON([]);
            }

            $query = $this->db->table('dtsen_rt')
                ->select('rw')
                ->distinct()
                ->where('kode_desa', $kodeDesa) // 🔒 Gembok paten
                ->orderBy('rw', 'ASC')
                ->get()
                ->getResultArray();

            $rwList = array_column($query, 'rw');
        } else {
            // Operator: Ekstrak RW dari string wilayah_tugas
            $rwList = [];
            $blocks = explode('|', $wilayahTugas);
            foreach ($blocks as $block) {
                [$rw,] = array_pad(explode(':', $block), 2, '');
                if (trim($rw) !== '') {
                    $rwList[] = str_pad(trim($rw), 3, '0', STR_PAD_LEFT);
                }
            }
            $rwList = array_unique($rwList);
            sort($rwList);
        }

        return $this->response->setJSON($rwList);
    }

    // ========================================================
    // 🌍 GET DYNAMIC RT (Berdasarkan Pilihan RW & Kode Desa)
    // ========================================================
    public function get_rt_ajax()
    {
        $rw = $this->request->getGet('rw');
        $user = $this->authModel->getUserId();
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');
        $roleId = session()->get('role_id') ?? 4;

        // 🚀 SUPER AMAN: Cek di session, kalau tidak ada cek di data user db
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        if ($roleId <= 3 || empty($wilayahTugas)) {

            // 🛡️ PENCEGAHAN KEBOCORAN MUTLAK
            if (empty($kodeDesa)) {
                return $this->response->setJSON([]);
            }

            $builder = $this->db->table('dtsen_rt')
                ->select('rt')
                ->distinct()
                ->where('kode_desa', $kodeDesa); // 🔒 Gembok paten

            if (!empty($rw)) {
                $builder->where('rw', str_pad($rw, 3, '0', STR_PAD_LEFT));
            }

            $query = $builder->orderBy('rt', 'ASC')->get()->getResultArray();
            $rtList = array_column($query, 'rt');
        } else {
            // Operator: Ekstrak RT khusus untuk RW yang dipilih
            $rtList = [];
            $blocks = explode('|', $wilayahTugas);
            foreach ($blocks as $block) {
                [$blokRw, $rtCSV] = array_pad(explode(':', $block), 2, '');

                if (str_pad(trim($blokRw), 3, '0', STR_PAD_LEFT) === str_pad($rw, 3, '0', STR_PAD_LEFT)) {
                    $rts = explode(',', $rtCSV);
                    foreach ($rts as $rt) {
                        if (trim($rt) !== '') {
                            $rtList[] = str_pad(trim($rt), 3, '0', STR_PAD_LEFT);
                        }
                    }
                }
            }
            $rtList = array_unique($rtList);
            sort($rtList);
        }

        return $this->response->setJSON($rtList);
    }

    // ========================================================
    // 🗑️ HAPUS DOKUMENTASI (Termasuk Hapus Foto Fisik dari Server)
    // ========================================================
    public function hapus()
    {
        try {
            $id = $this->request->getPost('id');
            if (empty($id)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak valid!']);
            }

            // 🛡️ Cek Otorisasi (Hanya Admin & Kades / Role 1-3 yang bisa hapus)
            $roleId = session()->get('role_id') ?? 4;
            if ($roleId > 3) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk menghapus data!']);
            }

            // 1. Ambil data sebelum dihapus untuk mengetahui nama file fotonya
            $data = $this->db->table('dtsen_bansos_kks')->where('id', $id)->get()->getRowArray();

            if ($data) {
                $uploadPath = FCPATH . 'uploads/bansos/';

                // 2. Sapu bersih Foto KPM dari server (jika ada)
                if (!empty($data['foto_kpm_kks']) && file_exists($uploadPath . $data['foto_kpm_kks'])) {
                    unlink($uploadPath . $data['foto_kpm_kks']);
                }

                // 3. Sapu bersih Foto Bukti/Struk dari server (jika ada)
                if (!empty($data['foto_bukti_transaksi']) && file_exists($uploadPath . $data['foto_bukti_transaksi'])) {
                    unlink($uploadPath . $data['foto_bukti_transaksi']);
                }

                // 4. Hapus baris data permanen dari database
                $this->db->table('dtsen_bansos_kks')->where('id', $id)->delete();

                return $this->response->setJSON(['status' => 'success', 'message' => 'Data dan foto dokumentasi berhasil dihapus permanen!']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan di database!']);
            }
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔒 FUNGSI KUNCI/BUKA KUNCI DATA BANSOS
    // ==========================================
    public function toggleLock()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status'); // Berisi 1 atau 0
        $roleId = session()->get('role_id');

        // Proteksi Lapis Ganda: Hanya Role Desa (3) ke atas (Kecamatan/Admin) yang boleh eksekusi
        if ($roleId > 3) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak! Anda tidak memiliki wewenang untuk mengunci/membuka data.'
            ]);
        }

        try {
            $this->db->table('dtsen_bansos_kks')
                ->where('id', $id)
                ->update(['is_locked' => $status]);

            $pesan = ($status == 1) ? 'Data berhasil dikunci dan diamankan!' : 'Kunci data berhasil dibuka!';

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $pesan
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada database: ' . $e->getMessage()
            ]);
        }
    }
}
