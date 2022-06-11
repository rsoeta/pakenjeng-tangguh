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
