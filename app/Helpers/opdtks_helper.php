<?php

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
