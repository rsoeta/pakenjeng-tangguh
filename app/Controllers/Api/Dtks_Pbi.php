<?php

namespace App\Controllers\Api;

use App\Models\Dtks\VervalPbiModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Dtks_Pbi extends ResourceController
{
    use ResponseTrait;

    public function show($id = null)
    {
        $model = new VervalPbiModel();
        $data = $model->getIdPbi($id)->get()->getRowArray();

        // var_dump($data);
        // die;
        return $this->respond($data);
    }
}
