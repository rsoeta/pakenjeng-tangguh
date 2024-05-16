<?php

use CodeIgniter\I18n\Time;
use App\Models\GenModel;


function nameApp()
{
    return 'DTKS';
}

function logoApp()
{
    return base_url('icon-dtks8.png');
}

// function version app from database
function versionApp()
{
    $genModel = new GenModel();
    $data = $genModel->getVersion();
    return $data->tv_version;
}

function Profil_Admin()
{
    $db = \Config\Database::connect();

    $builder = $db->table('dtks_users');
    $builder->select('fullname, tb_districts.name as namaKec, user_image, kode_kab, kode_kec');
    $builder->join('tb_districts', 'tb_districts.id=dtks_users.kode_kec');
    $builder->where('dtks_users.role_id <=', 2);

    $query = $builder->get();
    return $query->getRowArray();
}

function menu()
{
    $db = \Config\Database::connect();
    $builder = $db->table('tb_menu');
    $builder->select('tm_id, tm_nama, tm_class, tm_url, tm_icon, tm_parent_id, tm_status, tm_grup_akses');
    $query = $builder->get();

    return $query->getResultArray();
}

// how to make fuction menu_child?
function menu_child($menu_id)
{
    $db = \Config\Database::connect();
    $builder = $db->table('tb_menu');
    $builder->select('tm_id, tm_nama, tm_class, tm_url, tm_icon, tm_parent_id, tm_status, tm_grup_akses');
    $builder->where('tm_parent_id', $menu_id);
    $query = $builder->get();
    return $query->getResultArray();
}

function menu_child_child($menu_child)
{
    $db = \Config\Database::connect();
    $builder = $db->table('tb_menu');
    $builder->select('tm_id, tm_nama, tm_class, tm_url, tm_icon, tm_parent_id, tm_status, tm_grup_akses');
    $builder->where('tm_parent_id', $menu_child);
    $query = $builder->get();
    return $query->getResultArray();
}

function menu_child_child_child($menu_child_child)
{
    $db = \Config\Database::connect();
    $builder = $db->table('tb_menu');
    $builder->select('tm_id, tm_nama, tm_class, tm_url, tm_icon, tm_parent_id, tm_status, tm_grup_akses');
    $builder->where('tm_parent_id', $menu_child_child);
    $query = $builder->get();
    return $query->getResultArray();
}

// function FOTO_DOKUMEN($fileName = '', $dir = '', $defFile = '')
// {
//     if ($fileName !== '' && $fileName !== null && file_exists(FCPATH . 'data/bnba/' . $dir . '/' . $fileName)) {
//         return base_url('data/bnba/' . $dir . '/' . $fileName);
//     } else {
//         if ($defFile == '') {
//             return base_url('assets/images/image_not_available.jpg');
//         } else {
//             return base_url('assets/images/' . $defFile);
//         }
//     }
// }

function FOTO_KPM($fileName = '', $dir = '', $defFile = '')
{
    $firstDir = FCPATH . 'data/bnba/' . $dir . '/';
    $secondDir = FCPATH . 'data/dkm/' . $dir . '/'; // Ganti dengan nama direktori kedua

    if ($fileName !== '' && $fileName !== null) {
        if (file_exists($firstDir . $fileName) || file_exists($secondDir . $fileName)) {
            // Jika file ditemukan di direktori pertama atau kedua, kembalikan URL-nya
            $filePath = file_exists($firstDir . $fileName) ? $firstDir : $secondDir;
            return base_url($filePath . $fileName);
        } else {
            if ($defFile == '') {
                return base_url('assets/images/image_not_available.jpg');
            } else {
                return base_url('assets/images/' . $defFile);
            }
        }
    } else {
        return base_url('assets/images/image_not_available.jpg');
    }
}

function FOTO_RUMAH($fileName = '', $dir = '', $defFile = '')
{
    $firstDir = FCPATH . 'data/bnba/' . $dir . '/';
    $secondDir = FCPATH . 'data/usulan/' . $dir . '/'; // Ganti dengan nama direktori kedua

    if ($fileName !== '' && $fileName !== null) {
        if (file_exists($firstDir . $fileName) || file_exists($secondDir . $fileName)) {
            // Jika file ditemukan di direktori pertama atau kedua, kembalikan URL-nya
            $filePath = file_exists($firstDir . $fileName) ? $firstDir : $secondDir;
            return base_url($filePath . $fileName);
        } else {
            if ($defFile == '') {
                return base_url('assets/images/image_not_available.jpg');
            } else {
                return base_url('assets/images/' . $defFile);
            }
        }
    } else {
        return base_url('assets/images/image_not_available.jpg');
    }
}

function Foto_Profil($fileName = '', $dir = '', $defFile = '')
{
    if ($fileName !== '' && $fileName !== null && file_exists(FCPATH . 'data/' . $dir . '/' . $fileName)) {
        return base_url('data/' . $dir . '/' . $fileName);
    } else {
        if ($defFile == '') {
            return base_url('assets/dist/img/profile/default.png');
        } else {
            return base_url('assets/dist/img/profile/' . $defFile);
        }
    }
}

function Salam()
{
    $jam = date('H:i');
    // dd($jam);
    //atur salam menggunakan IF
    if (
        $jam > '05:30' && $jam < '10:00'
    ) {
        $salam = 'Pagi';
    } elseif ($jam >= '10:00' && $jam < '15:00') {
        $salam = 'Siang';
    } elseif ($jam < '18:00') {
        $salam = 'Sore';
    } else {
        $salam = 'Malam';
    }

    return $salam;
}

function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px)
{
    for ($c1 = ($x - abs($px)); $c1 <= ($x + abs($px)); $c1++)
        for ($c2 = ($y - abs($px)); $c2 <= ($y + abs($px)); $c2++)
            $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
    return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
}

function hari_ini()
{
    $hari_ini = date("D");
    switch ($hari_ini) {
        case 'Sun':
            $hari_ini = "Minggu";
            break;

        case 'Mon':
            $hari_ini = "Senin";
            break;

        case 'Tue':
            $hari_ini = "Selasa";
            break;

        case 'Wed':
            $hari_ini = "Rabu";
            break;

        case 'Thu':
            $hari_ini = "Kamis";
            break;

        case 'Fri':
            $hari_ini = "Jumat";
            break;

        case 'Sat':
            $hari_ini = "Sabtu";
            break;

        default:
            $hari_ini = "Tidak di ketahui";
            break;
    }
    return $hari_ini;
}

// function bulan sekarang dalam bahasa indonesia
function bulan_ini()
{
    $bulan_ini = date("F");
    switch ($bulan_ini) {
        case 'January':
            $bulan_ini = "Januari";
            break;

        case 'February':
            $bulan_ini = "Februari";
            break;

        case 'March':
            $bulan_ini = "Maret";
            break;

        case 'April':
            $bulan_ini = "April";
            break;

        case 'May':
            $bulan_ini = "Mei";
            break;
        case 'June':
            $bulan_ini = "Juni";
            break;
        case 'July':
            $bulan_ini = "Juli";
            break;
        case 'August':
            $bulan_ini = "Agustus";
            break;
        case 'September':
            $bulan_ini = "September";
            break;
        case 'October':
            $bulan_ini = "Oktober";
            break;
        case 'November':
            $bulan_ini = "November";
            break;
        case 'December':
            $bulan_ini = "Desember";
            break;
        default:
            $bulan_ini = "Tidak di ketahui";
            break;
    }
    return $bulan_ini;
}

function deadline_usulan()
{
    $times = new Time();
    $db = \Config\Database::connect();

    $tahun = $times->getYear();
    $bulan = $times->getMonth();
    $hari = $times->getDay();       // 12
    $jam = $times->getHour();           // 16
    $menit = $times->getMinute();         // 15
    $bulanNext = $bulan + 1;
    $hak_akses = session()->get('role_id');

    $builder = $db->table('dtks_deadline');
    $builder->select('*');
    $builder->where('dd_role', $hak_akses);
    $dataWaktu = $builder->get()->getResultArray();
    // var_dump($data);
    // die;

    foreach ($dataWaktu as $d) {
        $startDatetimeColumn = $d['dd_waktu_start'];
        $endDatetimeColumn = $d['dd_waktu_end'];
    }

    // dari chatgpt
    // Ambil nilai $startDatetime, $endDatetime, $allowedStartHour, dan $allowedEndHour dari database berdasarkan nama kolom
    $startDatetime      = $startDatetimeColumn;
    $endDatetime        = $endDatetimeColumn;
    $allowedStartHour   = $startDatetimeColumn;
    $allowedEndHour     = $endDatetimeColumn;

    // Mengubah tanggal dan waktu menjadi objek DateTime
    $hari_ini = $tahun . '-' . $bulan . '-' . $hari . ' ' . $jam . ':' . $menit;
    // $currentDatetime = strtotime($hari_ini);
    $currentDatetime = $hari_ini;

    $startDateTimeObj = new DateTime($startDatetime);
    $endDateTimeObj = new DateTime($endDatetime);
    $currentDateTimeObj = new DateTime($currentDatetime);

    // var_dump([$currentDateTimeObj, $startDateTimeObj, $endDateTimeObj]);
    // die;

    // Memeriksa apakah tanggal dan waktu saat ini berada dalam rentang tanggal deadline
    if (($currentDateTimeObj >= $startDateTimeObj && $currentDateTimeObj <= $endDateTimeObj) || ($currentDateTimeObj <= $startDateTimeObj && $currentDateTimeObj >= $endDateTimeObj)) {
        // Mengambil jam, menit, dan detik saat ini
        $currentHour = intval($currentDateTimeObj->format('H'));
        $currentMinute = intval($currentDateTimeObj->format('i'));
        $currentSecond = intval($currentDateTimeObj->format('s'));

        // Mengambil jam, menit, dan detik mulai dan akhir akses yang diizinkan
        $allowedStartHour = intval($startDateTimeObj->format('H'));
        $allowedStartMinute = intval($startDateTimeObj->format('i'));
        $allowedStartSecond = intval($startDateTimeObj->format('s'));
        $allowedEndHour = intval($endDateTimeObj->format('H'));
        $allowedEndMinute = intval($endDateTimeObj->format('i'));
        $allowedEndSecond = intval($endDateTimeObj->format('s'));

        // Memeriksa apakah waktu saat ini berada dalam rentang waktu akses yang diizinkan
        if (($currentHour > $allowedStartHour && $currentHour < $allowedEndHour) ||
            ($currentHour === $allowedStartHour && $currentMinute >= $allowedStartMinute && $currentSecond >= $allowedStartSecond) ||
            ($currentHour === $allowedEndHour && $currentMinute <= $allowedEndMinute && $currentSecond <= $allowedEndSecond)
        ) {
            return true; // Akses diizinkan
        }
    }

    return false; // Akses ditolak
}

function deadline_ppks()
{
    $times = new Time();
    $db = \Config\Database::connect();
    $tahun = $times->getYear();
    $bulan = $times->getMonth();
    $hari = $times->getDay();       // 12
    $jam = $times->getHour();           // 16
    $menit = $times->getMinute();         // 15
    $bulanNext = $bulan + 1;
    $hak_akses = session()->get('role_id');

    $builder = $db->table('ppks_deadline');
    // get last row
    // $builder = $builder->select('*');
    $query = $builder->where('dd_role', $hak_akses)->get();

    $data = $query->getRowArray();
    // dd($data);
    // foreach ($data as $d) {
    $tanggal_mulai = $data['dd_waktu_start'];
    $tanggal_akhir = $data['dd_waktu_end'];
    // }
    $tgl_a = date_create($tanggal_mulai);
    $tgl_b = date_create($tanggal_akhir);

    // $dead = date('Y-m-d H:i') . "<br>";
    $dead_mulai = ($tahun . '-' . $bulan . '-' . date_format($tgl_a, 'd H:i')); // starting waktu
    $dead_akhir = ($tahun . '-' . $bulan . '-' . date_format($tgl_b, 'd H:i')); // ending waktu untuk user
    $dead_akhir2 = ($tahun . '-' . $bulan . '-' . date_format($tgl_b, 'd H:i')); // ending waktu untuk operator

    $strdead1 = (strtotime($dead_mulai));
    $strdead2 = (strtotime($dead_akhir));
    $strdead3 = (strtotime($dead_akhir2));
    // $ini_tanggal = strtotime("14 14:12") . "<br>";
    // $ini_tanggal = strtotime() . "<br>";

    // $deadline = '141312';

    $hari_ini = $tahun . '-' . $bulan . '-' . $hari . ' ' . $jam . ':' . $menit;
    $strhari_ini = strtotime($hari_ini);

    // dd($strhari_ini);

    if ($hak_akses <= 3) {
        $deadline = ($strhari_ini <= $strdead1 || $strhari_ini >= $strdead3) ? 1 : 0;
    } else {
        $deadline = ($strhari_ini <= $strdead1 || $strhari_ini >= $strdead2) ? 1 : 0;
    }

    // return $ini_tanggal;
    // return $hari_ini > $deadline;

    return $deadline;

    // dd($deadline_usulan);
}

function dkm_foto_cpm($fileName = '', $dir = '', $defFile = '')
{
    if ($fileName !== '' && $fileName !== null && file_exists(FCPATH . 'data/dkm/' . $dir . '/' . $fileName)) {
        return base_url('data/dkm/' . $dir . '/' . $fileName);
    } else {
        if ($defFile == '') {
            return base_url('assets/images/image_not_available.jpg');
        } else {
            return base_url('assets/images/' . $defFile);
        }
    }
    # code...
}

function usulan_foto($fileName = '', $dir = '', $defFile = '')
{
    if ($fileName !== '' && $fileName !== null && file_exists(FCPATH . 'data/usulan/' . $dir . '/' . $fileName)) {
        return base_url('data/usulan/' . $dir . '/' . $fileName);
    } else {
        if ($defFile == '') {
            return base_url('assets/images/image_not_available.jpg');
        } else {
            return base_url('assets/images/' . $defFile);
        }
    }
    # code...
}

function kip_foto($fileName = '', $dir = '', $defFile = '')
{
    if ($fileName !== '' && $fileName !== null && file_exists(FCPATH . 'data/kip_foto/' . $dir . '/' . $fileName)) {
        return base_url('data/kip_foto/' . $dir . '/' . $fileName);
    } else {
        if ($defFile == '') {
            return base_url('assets/images/image_not_available.jpg');
        } else {
            return base_url('assets/images/' . $defFile);
        }
    }
    # code...
}

function ppks_foto($fileName = '', $dir = '', $defFile = '')
{
    if ($fileName !== '' && $fileName !== null && file_exists(FCPATH . 'data/ppks_kpm/' . $dir . '/' . $fileName)) {
        return base_url('data/ppks_kpm/' . $dir . '/' . $fileName);
    } else {
        if ($defFile == '') {
            return base_url('assets/images/image_not_available.jpg');
        } else {
            return base_url('assets/images/' . $defFile);
        }
    }
    # code...
}

// $nohp = "08562121141";
function nope($nohp)
{
    // kadang ada penulisan no hp 0811 239 345
    $nohp = str_replace(" ", "", $nohp);
    // kadang ada penulisan no hp (0274) 778787
    $nohp = str_replace("(", "", $nohp);
    // kadang ada penulisan no hp (0274) 778787
    $nohp = str_replace(")", "", $nohp);
    // kadang ada penulisan no hp 0811.239.345
    $nohp = str_replace(".", "", $nohp);

    // cek apakah no hp mengandung karakter + dan 0-9
    if (!preg_match('/[^+0-9]/', trim($nohp))) {
        // cek apakah no hp karakter 1-3 adalah +62
        if (substr(trim($nohp), 0, 3) == '62') {
            $hp = trim($nohp);
        }
        // cek apakah no hp karakter 1 adalah 0
        elseif (substr(trim($nohp), 0, 1) == '0') {
            $hp = '62' . substr(trim($nohp), 1);
        }
        // abaikan no hp karakter 1 bukan 0
        else {
            $hp = $nohp;
        }
    }
    return $hp;
}
// hp($nohp);
