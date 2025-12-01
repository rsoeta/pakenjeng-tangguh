<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class KKModel extends Model
{
    protected $table = 'dtsen_kk';
    protected $primaryKey = 'id_kk';

    public function getKK($id)
    {
        return $this->where('id_kk', $id)->get()->getRowArray();
    }
}
