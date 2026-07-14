<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class PpksPasModel extends Model
{
    protected $table            = 'dtsen_ppks_pas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Kita hapus permanen jika ada salah input
    protected $allowedFields    = [
        'nik_kpm',
        'kategori_id',
        'jenis_ppks_gform',
        'status_gform',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
