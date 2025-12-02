<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class DtsenUsulanBansosModel extends Model
{
    use WilayahFilterTrait;

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

    /**
     * Hitung total usulan bansos bulan ini
     * sesuai filter wilayah dan role pengguna.
     */
    public function countUsulanBansosBulanIni(int $userRole, array $filter = []): int
    {
        $builder = $this->db->table('dtsen_usulan_bansos ub')
            ->join('dtsen_kk kk', 'kk.id_kk = ub.id_kk', 'left') // ✅ kolom benar
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->select('COUNT(DISTINCT ub.id) AS total')
            ->where('MONTH(ub.created_at)', date('m'))
            ->where('YEAR(ub.created_at)', date('Y'));

        // 🔹 Terapkan filter wilayah_tugas (gunakan trait)
        $this->applyWilayahFilter($builder, $filter, $userRole);

        $row = $builder->get()->getRowArray();
        return (int) ($row['total'] ?? 0);
    }
}
