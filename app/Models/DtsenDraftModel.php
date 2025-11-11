<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class DtsenDraftModel extends Model
{
    use WilayahFilterTrait; // ✅ panggil trait-nya

    protected $table            = 'dtsen_usulan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'usulan_no',
        'jenis',
        'status',
        'dtsen_kk_id',
        'no_kk_target',
        'created_by',
        'assigned_to',
        'payload',
        'summary',
        'created_at',
        'updated_at',
        'verified_at',
        'applied_at'
    ];
    protected $useTimestamps = true;

    /**
     * Hitung jumlah draft (semua wilayah)
     */
    public function countDraft()
    {
        return $this->where('status', 'draft')
            ->countAllResults();
    }

    /**
     * Hitung jumlah draft berdasarkan wilayah kerja user login
     * Catatan: tabel ini tidak punya field wilayah_kerja → filter dilakukan
     * berdasarkan dtks_users.wilayah_kerja di session
     */
    public function countDraftByUser(int $userRole, array $filter = [])
    {
        $builder = $this->db->table('dtsen_usulan u')
            ->select('COUNT(DISTINCT kk.id_kk) AS total')
            ->join('dtsen_kk kk', 'kk.id_kk = u.dtsen_kk_id', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('u.status', 'draft');

        $this->applyWilayahFilter($builder, $filter, $userRole);

        $row = $builder->get()->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Jika struktur tabel berbeda (misal kolom penanda draft bernama 'is_draft' boolean),
     * kamu bisa panggil method ini dengan parameter kolom dan value.
     */
    public function countDraftCustom(string $column = 'status', $value = 'draft', ?string $wilayah = null)
    {
        $builder = $this->builder();
        $builder->where($column, $value);
        if ($wilayah !== null) {
            $builder->where('wilayah_kerja', $wilayah);
        }
        return $builder->countAllResults();
    }
}
