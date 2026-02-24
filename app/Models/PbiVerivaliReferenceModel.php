<?php

namespace App\Models;

use CodeIgniter\Model;

class PbiVerivaliReferenceModel extends Model
{
    protected $table = 'pbi_verivali_reference';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nik',
        'no_kk',
        'nama',
        'desil_nasional',
        'status',
        'cn',
        'alamat',
        'rw',
        'rt',
        'created_at',
    ];
}
