<?php

namespace App\Models;

use CodeIgniter\Model;

class UsulanBansosModel extends Model
{
    protected $table            = 'dtsen_usulan_bansos';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id_kk',
        'nik',
        'program_bansos',
        'catatan',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
    protected $useTimestamps    = false;
}
