<?php

namespace App\Models;

use CodeIgniter\Model;

class DtsenSeModel extends Model
{
    protected $table            = 'dtsen_se';
    protected $primaryKey       = 'id_se';
    protected $allowedFields    = [
        'id_rt',
        'id_kk',
        'sumber_penghasilan',
        'rata_penghasilan_bulanan',
        'rata_pengeluaran_bulanan',
        'kepemilikan_aset',
        'kepemilikan_bantuan',
        'status_kks',
        'status_bpjs',
        'status_kip',
        'kategori_desil',
        'catatan_tambahan',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;

    public function getByKk($id_kk)
    {
        return $this->where('id_kk', $id_kk)->first();
    }
}
