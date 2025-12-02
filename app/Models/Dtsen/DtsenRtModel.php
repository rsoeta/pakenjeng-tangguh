<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class DtsenRtModel extends Model
{
    protected $table            = 'dtsen_rt';
    protected $primaryKey       = 'id_rt';
    protected $allowedFields    = [
        'kode_desa',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'latitude',
        'longitude',
        'accuracy',
        'kepemilikan_rumah',
        'kondisi_atap',
        'kondisi_dinding',
        'kondisi_lantai',
        'sumber_air',
        'sanitasi',
        'sumber_listrik',
        'foto_rumah',
        'foto_rumah_dalam',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;
}
