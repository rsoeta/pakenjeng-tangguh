<?php

function nameApp()
{
    return 'Opr NewDTKS';
}

function Profil_Admin()
{
    $db = \Config\Database::connect();

    $builder = $db->table('dtks_users');
    $builder->select('fullname, kode_kec, tb_districts.name as namaKec, user_image');
    $builder->join('tb_districts', 'tb_districts.id=dtks_users.kode_kec');
    $builder->where('dtks_users.role_id', 2);

    $query = $builder->get();
    return $query->getRowArray();
}

function FOTO_DOKUMEN($fileName = '', $dir = '', $defFile = '')
{
    if ($fileName !== '' && $fileName !== null && file_exists(FCPATH . 'data/bnba/' . $dir . '/' . $fileName)) {
        return base_url('data/bnba/' . $dir . '/' . $fileName);
    } else {
        if ($defFile == '') {
            return base_url('assets/images/image_not_available.jpg');
        } else {
            return base_url('assets/images/' . $defFile);
        }
    }
    # code...
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

// function hari ini

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
