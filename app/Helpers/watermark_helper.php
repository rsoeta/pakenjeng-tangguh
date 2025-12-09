<?php

function applyWatermarkPremium(string $imagePath, array $data)
{
    // ============================================================
    // 0. VALIDATION
    // ============================================================
    if (!file_exists($imagePath)) {
        return false;
    }

    // ============================================================
    // 1. LOAD IMAGE
    // ============================================================
    $info = getimagesize($imagePath);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($imagePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($imagePath);
            break;
        default:
            return false;
    }

    $width  = imagesx($image);
    $height = imagesy($image);


    // ============================================================
    // 2. RESPONSIVE FONT SIZE (premium scaling)
    // ============================================================
    $baseFont = max(16, intval($width * 0.015));   // ukuran ideal
    $lineSpacing = intval($baseFont * 1.45);       // jarak antar baris


    // ============================================================
    // 3. LOAD FONTS (Ubuntu)
    // ============================================================
    $fontBold    = FCPATH . "assets/fonts/Ubuntu-Bold.ttf";
    $fontRegular = FCPATH . "assets/fonts/Ubuntu-Regular.ttf";

    if (!file_exists($fontBold))    $fontBold = $fontRegular;
    if (!file_exists($fontRegular)) $fontRegular = $fontBold;


    // ============================================================
    // 4. FORMAT TANGGAL INDONESIA — SAFE VERSION
    // ============================================================
    if (!function_exists('tanggal_indo')) {
        function tanggal_indo($dateString)
        {
            if (empty($dateString)) {
                return '-';
            }

            $bulan = [
                1 => "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember"
            ];

            $t = strtotime($dateString);
            if (!$t) return $dateString;

            return date("d", $t) . " " . $bulan[(int) date("m", $t)] . " " . date("Y", $t);
        }
    }

    $tanggalIndo = tanggal_indo($data['tanggal'] ?? date('Y-m-d'));


    // ============================================================
    // 5. BUILD TEXT LINES
    // ============================================================
    $textLines = [
        ['font' => $fontBold,    'text' => "== SINDEN System =="],
        ['font' => $fontRegular, 'text' => "KK: {$data['no_kk']} • {$data['kepala']}"],
        ['font' => $fontRegular, 'text' => "Petugas: {$data['petugas']} • {$tanggalIndo}"],
        ['font' => $fontRegular, 'text' => "Lokasi: {$data['latitude']}, {$data['longitude']} • Validated"],
        ['font' => $fontRegular, 'text' => $data['wilayah']],
    ];


    // ============================================================
    // 6. HITUNG BOX WIDTH (agar premium & pas)
    // ============================================================
    $padding = 28;
    $maxTextWidth = 0;

    foreach ($textLines as $line) {
        $bbox = imagettfbbox($baseFont, 0, $line['font'], $line['text']);
        $lineW = intval($bbox[2] - $bbox[0]);
        if ($lineW > $maxTextWidth) {
            $maxTextWidth = $lineW;
        }
    }

    $boxWidth  = intval($maxTextWidth + ($padding * 2));
    $boxHeight = intval(($lineSpacing * count($textLines)) + ($padding * 1.2));


    // ============================================================
    // 7. POSISI BOX (bottom-left premium standard)
    // ============================================================
    $boxX = 20;
    $boxY = intval($height - $boxHeight - 20);


    // ============================================================
    // 8. LAYER BACKGROUND (glass dark)
    // ============================================================
    $layer = imagecreatetruecolor($boxWidth, $boxHeight);
    imagesavealpha($layer, true);
    $trans = imagecolorallocatealpha($layer, 0, 0, 0, 127);
    imagefill($layer, 0, 0, $trans);

    // background semi-transparan
    $bgColor = imagecolorallocatealpha($layer, 0, 0, 0, 68);

    // Rounded rectangle manual (optimised)
    $radius = 18;
    imagefilledrectangle($layer, $radius, 0, $boxWidth - $radius, $boxHeight, $bgColor);
    imagefilledrectangle($layer, 0, $radius, $boxWidth, $boxHeight - $radius, $bgColor);

    imagefilledellipse($layer, $radius, $radius, $radius * 2, $radius * 2, $bgColor);
    imagefilledellipse($layer, $boxWidth - $radius, $radius, $radius * 2, $radius * 2, $bgColor);
    imagefilledellipse($layer, $radius, $boxHeight - $radius, $radius * 2, $radius * 2, $bgColor);
    imagefilledellipse($layer, $boxWidth - $radius, $boxHeight - $radius, $radius * 2, $radius * 2, $bgColor);


    // ============================================================
    // 9. TEXT COLORS
    // ============================================================
    $white = imagecolorallocate($layer, 255, 255, 255);
    $shadow = imagecolorallocatealpha($layer, 0, 0, 0, 74);


    // ============================================================
    // 10. DRAW TEXT + SHADOW
    // ============================================================
    $cursorY = $padding;

    foreach ($textLines as $line) {

        // Shadow
        imagettftext(
            $layer,
            $baseFont,
            0,
            intval($padding + 2),
            intval($cursorY + 2),
            $shadow,
            $line['font'],
            $line['text']
        );

        // Real text
        imagettftext(
            $layer,
            $baseFont,
            0,
            intval($padding),
            intval($cursorY),
            $white,
            $line['font'],
            $line['text']
        );

        $cursorY += intval($lineSpacing);
    }


    // ============================================================
    // 11. MERGE TO MAIN IMAGE
    // ============================================================
    imagecopy(
        $image,
        $layer,
        intval($boxX),
        intval($boxY),
        0,
        0,
        $boxWidth,
        $boxHeight
    );


    // ============================================================
    // 12. SAVE IMAGE
    // ============================================================
    if ($mime === 'image/jpeg') {
        imagejpeg($image, $imagePath, 95);
    } else {
        imagepng($image, $imagePath);
    }

    imagedestroy($image);
    imagedestroy($layer);

    return true;
}
