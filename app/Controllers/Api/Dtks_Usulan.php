<?php

namespace App\Controllers\Api;

use App\Models\Dtks\Usulan22Model;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Dtks_Usulan extends ResourceController
{
    use ResponseTrait;

    public function show($id = null)
    {
        $model = new Usulan22Model();
        $data = $model->getIdDtks($id)->get()->getRowArray();

        // var_dump($data);
        // die;
        return $this->respond($data);
    }
}
