<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class DtsenUsulanArtModel extends Model
{
    protected $table         = 'dtsen_usulan_art';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'dtsen_usulan_id',
        'nik',
        'nama',
        'hubungan',
        'payload_member',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';
}
