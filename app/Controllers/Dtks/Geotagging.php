<?php

namespace App\Controllers\Dtks;

use App\Models\Dtks\AuthModel;
use App\Models\WilayahModel;
use App\Models\GenModel;


use App\Controllers\BaseController;

class Geotagging extends BaseController
{
    public function __construct()
    {
        $this->WilayahModel = new WilayahModel();
        $this->AuthModel = new AuthModel();
        $this->GenModel = new GenModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Verifikasi dan Validasi Anomali DTKS',
            'user_login' => $this->AuthModel->getUserId(),
            'statusRole' => $this->GenModel->getStatusRole(),


        ];

        return view('dtks/data/dtks/file_bpk/index', $data);
    }
}
