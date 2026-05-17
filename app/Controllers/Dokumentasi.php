<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Traits\WilayahFilterTrait;

class Dokumentasi extends BaseController
{
    protected $db;
    protected $AuthModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->authModel = new AuthModel();
    }

    // 🚀 Fungsi baru untuk melempar data dropdown ke Frontend
    public function get_kegiatan()
    {
        $db = \Config\Database::connect();
        $data = $db->table('dtsen_jenis_kegiatan')
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON($data);
    }

    public function upload()
    {
        $file = $this->request->getFile('foto');
        $kegiatan = $this->request->getPost('jenis_kegiatan');
        $lat = $this->request->getPost('latitude') ?: 'Akses Lokasi Ditolak';
        $lng = $this->request->getPost('longitude') ?: '';

        $namaPetugas = session()->get('fullname') ?? 'Petugas Entri';
        $userId = session()->get('id') ?? 1;

        if (!$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid']);
        }

        // ============================================================
        // 1. NAMA FILE INFORMATIF (Tidak lagi random)
        // ============================================================
        $ext = $file->getExtension();
        // Bersihkan spasi dan karakter aneh jadi strip (-)
        $slugKegiatan = preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($kegiatan));
        $slugPetugas = preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($namaPetugas));
        $timestamp = date('Ymd_His');

        $newName = "DOC_{$slugKegiatan}_{$slugPetugas}_{$timestamp}.{$ext}";

        $uploadPath = FCPATH . 'uploads/dokumentasi/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        $destinationPath = $uploadPath . $newName;

        // Pindahkan file terlebih dahulu dari form ke folder uploads
        $file->move($uploadPath, $newName);

        // ============================================================
        // 2. LOAD IMAGE KE RAM (Mulai Native Pipeline)
        // ============================================================
        $info = getimagesize($destinationPath);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($destinationPath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($destinationPath);
                break;
            default:
                unlink($destinationPath); // Hapus jika format tidak sah
                return $this->response->setJSON(['success' => false, 'message' => 'Format salah']);
        }

        // ============================================================
        // 3. AUTO-ROTATE EXIF (Fix Gambar Berbaring)
        // ============================================================
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($destinationPath);
            if ($exif && isset($exif['Orientation'])) {
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
                }
            }
        }

        // ============================================================
        // 4. RESIZE NATIVE GD (Fix Terpotong & Hemat Hosting)
        // ============================================================
        $origWidth = imagesx($source);
        $origHeight = imagesy($source);
        $maxWidth = 1280;
        $maxHeight = 1280;

        if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
            // Hitung rasio untuk mempertahankan proporsi (Tidak gepeng/terpotong)
            $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
            $newWidth = round($origWidth * $ratio);
            $newHeight = round($origHeight * $ratio);

            $image = imagecreatetruecolor($newWidth, $newHeight);

            // Handle transparansi untuk PNG
            if ($mime === 'image/png') {
                imagealphablending($image, false);
                imagesavealpha($image, true);
                $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
                imagefilledrectangle($image, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Copy & Resize
            imagecopyresampled($image, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($source); // Bersihkan memori RAM
        } else {
            $image = $source; // Jika ukuran aslinya sudah kecil, biarkan saja
        }

        // ============================================================
        // 5. NATIVE GD WATERMARK (SINDEN STYLE PREMIUM)
        // ============================================================
        // Ambil width & height terbaru setelah diputar & diresize
        $width  = imagesx($image);
        $height = imagesy($image);

        $baseFont = max(16, intval($width * 0.015));
        $lineSpacing = intval($baseFont * 1.45);

        // Load Fonts
        $fontBold    = FCPATH . "assets/fonts/Ubuntu-Bold.ttf";
        $fontRegular = FCPATH . "assets/fonts/Ubuntu-Regular.ttf";
        if (!file_exists($fontBold)) $fontBold = FCPATH . "assets/fonts/OpenSans-Bold.ttf";
        if (!file_exists($fontRegular)) $fontRegular = FCPATH . "assets/fonts/OpenSans-Regular.ttf";

        $waktu = date('d/m/Y H:i:s') . ' WIB';
        $lokasiText = ($lat !== 'Akses Lokasi Ditolak') ? "{$lat}, {$lng}" : "Lokasi tidak diketahui";

        $textLines = [
            ['font' => $fontBold,    'text' => "== DOKUMENTASI SINDEN =="],
            ['font' => $fontRegular, 'text' => "Petugas: " . strtoupper($namaPetugas)],
            ['font' => $fontRegular, 'text' => "Kegiatan: " . strtoupper($kegiatan)],
            ['font' => $fontRegular, 'text' => "Lokasi GPS: " . $lokasiText],
            ['font' => $fontRegular, 'text' => "Waktu Laporan: " . $waktu]
        ];

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

        $boxX = 20;
        $boxY = intval($height - $boxHeight - 20);

        // Layer Glass Dark
        $layer = imagecreatetruecolor($boxWidth, $boxHeight);
        imagesavealpha($layer, true);
        $trans = imagecolorallocatealpha($layer, 0, 0, 0, 127);
        imagefill($layer, 0, 0, $trans);

        $bgColor = imagecolorallocatealpha($layer, 0, 0, 0, 68);
        $radius = 18;

        imagefilledrectangle($layer, $radius, 0, $boxWidth - $radius, $boxHeight, $bgColor);
        imagefilledrectangle($layer, 0, $radius, $boxWidth, $boxHeight - $radius, $bgColor);
        imagefilledellipse($layer, $radius, $radius, $radius * 2, $radius * 2, $bgColor);
        imagefilledellipse($layer, $boxWidth - $radius, $radius, $radius * 2, $radius * 2, $bgColor);
        imagefilledellipse($layer, $radius, $boxHeight - $radius, $radius * 2, $radius * 2, $bgColor);
        imagefilledellipse($layer, $boxWidth - $radius, $boxHeight - $radius, $radius * 2, $radius * 2, $bgColor);

        $white = imagecolorallocate($layer, 255, 255, 255);
        $shadow = imagecolorallocatealpha($layer, 0, 0, 0, 74);

        $cursorY = $padding;
        foreach ($textLines as $line) {
            if (file_exists($line['font'])) {
                imagettftext($layer, $baseFont, 0, intval($padding + 2), intval($cursorY + 2), $shadow, $line['font'], $line['text']);
                imagettftext($layer, $baseFont, 0, intval($padding), intval($cursorY), $white, $line['font'], $line['text']);
            }
            $cursorY += intval($lineSpacing);
        }

        imagecopy($image, $layer, intval($boxX), intval($boxY), 0, 0, $boxWidth, $boxHeight);

        // ============================================================
        // 6. SIMPAN GAMBAR AKHIR
        // ============================================================
        if ($mime === 'image/jpeg') {
            imagejpeg($image, $destinationPath, 90); // Kualitas 90% sudah sangat baik dan hemat
        } else {
            imagepng($image, $destinationPath);
        }

        imagedestroy($image);
        imagedestroy($layer);

        // ============================================================
        // 7. SIMPAN KE DATABASE
        // ============================================================
        $db = \Config\Database::connect();
        $db->table('dtsen_dokumentasi_petugas')->insert([
            'user_id'        => $userId,
            'nama_petugas'   => $namaPetugas,
            'jenis_kegiatan' => $kegiatan,
            'latitude'       => $lat,
            'longitude'      => $lng,
            'foto_path'      => $newName
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    // ========================================================
    // 📅 FITUR GALERI TIMELINE DOKUMENTASI PETUGAS
    // ========================================================
    public function timeline()
    {
        $authModel = new AuthModel();
        $user_login = $authModel->getUserId();

        $user_image = $user_login['image'] ?? $user_login['user_image'] ?? 'default.jpg';

        // Ambil ID dan Role User yang sedang login
        $userId = session()->get('id') ?? session()->get('id_user') ?? $user_login['id'] ?? 0;
        $roleId = session()->get('role_id') ?? $user_login['role_id'] ?? 4;

        $db = \Config\Database::connect();
        $builder = $db->table('dtsen_dokumentasi_petugas');

        // ========================================================
        // 🔐 PEMBATASAN AKSES (FILTER ROLE)
        // ========================================================
        if ($roleId == 4) {
            // Jika dia Petugas (Role 4), kunci datanya HANYA untuk user_id miliknya
            $builder->where('user_id', $userId);
        }

        // Tarik 100 data terbaru
        $dokumentasi = $builder->orderBy('created_at', 'DESC')
            ->limit(100)
            ->get()
            ->getResultArray();

        // Kelompokkan data berdasarkan Tanggal
        $timelineData = [];
        foreach ($dokumentasi as $doc) {
            $date = date('Y-m-d', strtotime($doc['created_at']));
            $timelineData[$date][] = $doc;
        }

        $data = [
            'title'        => 'Timeline Pentri',
            'timelineData' => $timelineData,
            'user_login'   => $user_login,
            'user_image'   => $user_image
        ];

        return view('dokumentasi/timeline', $data);
    }
}
