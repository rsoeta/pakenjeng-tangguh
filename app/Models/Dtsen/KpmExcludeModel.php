<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class KpmExcludeModel extends Model
{
    protected $table            = 'dtsen_kpm_exclude';
    protected $primaryKey       = 'id_exclude';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Kita biarkan hard-delete jika ada kesalahan import
    protected $protectFields    = true;

    // Kolom-kolom yang diizinkan untuk diisi via fungsi insert()/update()
    protected $allowedFields    = [
        'nik',
        'nama',
        'no_kk',
        'tgl_nonaktif',
        'keterangan',
        'bank',
        'no_rek',
        'desil',
        'created_by'
    ];

    // Mengaktifkan fitur otomatis pengisian created_at dan updated_at
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
