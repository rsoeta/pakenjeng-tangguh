<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class DtsenArtModel extends Model
{
    protected $table            = 'dtsen_art';
    protected $primaryKey       = 'id_art';
    protected $allowedFields    = [
        'id_kk',
        'nik',
        'shdk',
        'nama',
        'hubungan_keluarga',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'status_kawin',
        'pendidikan_terakhir',
        'pekerjaan',
        'disabilitas',
        'status_hamil',
        'tgl_hamil',
        'foto_identitas',
        'ibu_kandung',
        'program_bansos',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;

    public function getByKk($id_kk)
    {
        return $this->where('id_kk', $id_kk)->findAll();
    }
}
