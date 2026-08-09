<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class MonevModel extends Model
{
    protected $table            = 'dtsen_monev';
    protected $primaryKey       = 'id_monev';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDelete    = false; // Kita biarkan hapus permanen jika ada salah import
    protected $protectFields    = true;

    protected $allowedFields    = [
        'nama_target',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'alamat',
        'rt',
        'rw',
        'nama_sdm',
        'status_kpm',
        'nik',
        'periode',
        'status_monev',
        'catatan_pendamping',
        'created_by',
        'updated_by'
    ];

    // Aktifkan timestamp bawaan CI4
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
