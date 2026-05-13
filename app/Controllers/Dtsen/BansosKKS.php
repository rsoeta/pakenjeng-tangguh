<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;

class BansosKKS extends BaseController
{
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
            'title' => 'Dokumentasi Penyaluran Bansos melalui KKS',
            'user'  => session()->get(),
        ];

        // 👇 UBAH BARIS INI SESUAIKAN DENGAN NAMA FOLDER BARU ANDA 👇
        return view('dtsen/bansos_kks/v_kks_form', $data);
    }

    // ========================================================
    // 🔍 PENCARIAN AJAX (Validasi Silang & Filter Wilayah Tugas)
    // ========================================================
    public function cari_nik_ajax()
    {
        $searchTerm = trim($this->request->getPost('searchTerm'));

        // 1. Ambil wilayah_tugas dari user yang sedang login
        // Gunakan authModel yang sudah di-load di __construct
        $user = $this->authModel->getUserId();
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');

        // ... (lanjutkan kode builder dan filter wilayah tugas di bawahnya)

        // 2. Build Query Utama (Join ART -> KK -> RT)
        $builder = $this->db->table('dtsen_master_kks m')
            ->select('a.nik, a.nama as nama_kpm, m.no_kks')
            ->join('dtsen_art a', 'a.nik = m.nik') // Validasi dengan penduduk aktif
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk', 'left') // Ambil ID RT dari KK
            ->join('dtsen_rt r', 'r.id_rt = k.id_rt', 'left') // Ambil data RW & RT
            ->groupStart()
            ->like('a.nik', $searchTerm)
            ->orLike('m.no_kks', $searchTerm)
            ->groupEnd()
            ->limit(10);

        // 3. 🔐 PARSE wilayah_tugas → pasangan RW–RT (KUNCI)
        if (!empty($wilayahTugas)) {
            $wilayahPairs = [];
            // contoh: 001:005,007|004:002
            $blocks = explode('|', $wilayahTugas);

            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);

                foreach (explode(',', $rtList) as $rt) {
                    $rt = trim($rt);
                    if ($rw !== '' && $rt !== '') {
                        $wilayahPairs[] = [
                            'rw' => $rw,
                            'rt' => $rt
                        ];
                    }
                }
            }

            // 4. Terapkan Filter Wilayah ke dalam Query
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

        // 5. Eksekusi Query dan Format Hasil
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
    // 💾 SIMPAN DOKUMENTASI (Watermark & Custom Filename)
    // ========================================================
    public function simpan()
    {
        try {

            $post = $this->request->getPost();

            if (empty($post['nik_kpm'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data KPM tidak valid!']);
            }

            // Gabungkan Tahun dan Tahap
            $tahap_salur_final = $post['tahap_salur'] . ' Tahun ' . $post['tahun_salur'];

            $fotoKpm   = $this->request->getFile('foto_kpm_kks');
            $fotoBukti = $this->request->getFile('foto_bukti_transaksi');

            // ========================================================
            // 🛡️ LOGIKA ROBUSTNESS: CEK VALIDITAS FILE SEBELUM DIPROSES
            // ========================================================
            if ($fotoKpm && !$fotoKpm->isValid()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal Upload Foto KPM: ' . $fotoKpm->getErrorString() . ' (Kode: ' . $fotoKpm->getError() . ')'
                ]);
            }

            if ($fotoBukti && !$fotoBukti->isValid()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal Upload Bukti: ' . $fotoBukti->getErrorString() . ' (Kode: ' . $fotoBukti->getError() . ')'
                ]);
            }
            // ========================================================

            $nik = $post['nik_kpm'];
            // ... (lanjutkan ke penamaan file $namaFotoKpm dan seterusnya)
            $kks = $post['no_kks'] !== '-' && !empty($post['no_kks']) ? $post['no_kks'] : 'NOKKS';
            $lat = $post['latitude'] ?: '0';
            $lng = $post['longitude'] ?: '0';

            // Tambahkan time() agar file selalu unik dan terhindar dari Cache Browser
            $timestamp = time();
            $namaFotoKpm   = "KPM_{$nik}_{$kks}_{$timestamp}.jpg";
            $namaFotoBukti = "STRUK_{$nik}_{$kks}_{$timestamp}.jpg";

            $uploadPath = FCPATH . 'uploads/bansos/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            // Fungsi internal untuk memberi Watermark
            $this->_applyWatermark($fotoKpm, $uploadPath . $namaFotoKpm, $post);
            $this->_applyWatermark($fotoBukti, $uploadPath . $namaFotoBukti, $post);

            $dataInsert = [
                'nik_kpm'              => $nik,
                'nama_kpm'             => $post['nama_kpm'],
                'nomor_kks'            => $kks,
                'jenis_bansos'         => $post['jenis_bansos'],
                'tahap_salur'          => $tahap_salur_final,
                'nominal_cair'         => (int) ($post['nominal_cair'] ?? 0),
                'status_salur'         => $post['status_salur'],
                'foto_kpm_kks'         => $namaFotoKpm,
                'foto_bukti_transaksi' => $namaFotoBukti,
                'latitude'             => $lat,
                'longitude'            => $lng,
                'created_by'           => session()->get('id') ?? 0,
                'created_at'           => date('Y-m-d H:i:s')
            ];

            $this->db->table('dtsen_bansos_kks')->insert($dataInsert);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Dokumentasi berhasil disimpan!']);
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
}
