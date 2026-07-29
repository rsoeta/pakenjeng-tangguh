<?php

namespace App\Models;

use CodeIgniter\Model;

class PbiMasterDataModel extends Model
{
    protected $table            = 'pbi_master_data';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Semua kolom yang diizinkan untuk di-insert/update
    protected $allowedFields    = [
        'nik',
        'id_art',
        'no_kk',
        'nama',
        'no_kis',
        'faskes_tk1',
        'kampung',
        'rt',
        'rw',
        'kode_desa',
        'status_kepesertaan',
        'alasan_nonaktif',
        'tanggal_nonaktif',
        'dinonaktifkan_oleh',
        'periode_sinkron_terakhir',
        'created_by',
        'updated_by'
    ];

    // Aktifkan fitur otomatis pengisian created_at dan updated_at
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
