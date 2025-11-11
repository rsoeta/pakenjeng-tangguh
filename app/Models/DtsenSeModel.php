<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class DtsenSeModel extends Model
{
    use WilayahFilterTrait; // ✅ panggil trait-nya

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

    // 📁 app/Models/DtsenSeModel.php
    public function getDesilByRole(int $userRole, array $filter = [])
    {
        $builder = $this->db->table('dtsen_se se')
            ->select('se.kategori_desil, COUNT(DISTINCT kk.id_kk) AS jumlah')
            ->join('dtsen_kk kk', 'kk.id_kk = se.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->groupBy('se.kategori_desil')
            ->orderBy('se.kategori_desil', 'ASC');

        $this->applyWilayahFilter($builder, $filter, $userRole);

        return $builder->get()->getResult();
    }
}
