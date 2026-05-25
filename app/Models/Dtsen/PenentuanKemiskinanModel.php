<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class PenentuanKemiskinanModel extends Model
{
    use WilayahFilterTrait;

    protected $table      = 'dtsen_penentuan_kemiskinan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'dtsen_kk_id',
        'status_kemiskinan',
        'status_verifikasi',
        'catatan',
        'created_by',
        'updated_by',
        'verified_by',
        'verified_at',
        'created_at',
        'updated_at'
    ];


    public function getPenentuanKemiskinan(array $filter)
    {
        $db = $this->db;
        $userRole = session()->role_id ?? 99;

        $builder = $db->table('dtsen_kk kk')
            ->select('
                kk.id_kk, kk.no_kk, kk.kepala_keluarga, kk.alamat,
                rt.rw, rt.rt, art.nik, se.kategori_desil
            ')
            ->join('dtsen_art art', 'art.id_kk = kk.id_kk AND art.hubungan_keluarga = 1 AND art.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left')
            ->where('kk.deleted_at', null); // 🛡️ Cegah data sampah

        // 🔐 Panggil Filter Trait
        $this->applyWilayahFilter($builder, $filter, $userRole);

        // Hanya yang sudah verified dari usulan
        $builder->whereIn('kk.id_kk', function ($sub) {
            $sub->select('dtsen_kk_id')
                ->from('dtsen_usulan')
                ->whereIn('status', ['verified', 'diverifikasi']);
        });

        // 🔄 LOGIKA SIKLUS: Hilangkan yang masih aktif (pending/approved). 
        // Jika statusnya 'rollback', otomatis akan muncul kembali di sini!
        $builder->whereNotIn('kk.id_kk', function ($sub) {
            $sub->select('dtsen_kk_id')
                ->from('dtsen_penentuan_kemiskinan')
                ->whereIn('status_verifikasi', ['pending', 'approved']);
        });

        // Filter Tambahan
        if (!empty($filter['rw']) && $filter['rw'] !== 'all') {
            $builder->where('rt.rw', $filter['rw']);
        }
        if (!empty($filter['rt']) && $filter['rt'] !== 'all') {
            $builder->where('rt.rt', $filter['rt']);
        }
        if (!empty($filter['desil']) && $filter['desil'] !== 'all') {
            $builder->where('se.kategori_desil', $filter['desil'] === 'none' ? null : $filter['desil']);
        }

        return $builder->get()->getResultArray();
    }

    public function getVerifikasiKemiskinan(array $filter)
    {
        $db = $this->db;
        $userRole = session()->role_id ?? 99;

        $builder = $db->table('dtsen_penentuan_kemiskinan pk')
            ->select('
                pk.id, pk.status_kemiskinan, pk.created_by, u.fullname as petugas_entri, pk.catatan,
                kk.no_kk, kk.kepala_keluarga, art.nik, rt.rw, rt.rt, se.kategori_desil
            ')
            ->join('dtsen_kk kk', 'kk.id_kk = pk.dtsen_kk_id')
            ->join('dtsen_art art', 'art.id_kk = kk.id_kk AND art.hubungan_keluarga = 1 AND art.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left')
            ->join('dtks_users u', 'u.id = pk.created_by', 'left')
            ->where('pk.status_verifikasi', 'pending')
            ->where('kk.deleted_at', null); // 🛡️ Cegah data sampah

        // 🔐 Panggil Filter Trait (Menggantikan puluhan baris logika override)
        $this->applyWilayahFilter($builder, $filter, $userRole);

        if (!empty($filter['kode_desa'])) {
            $builder->where('rt.kode_desa', $filter['kode_desa']);
        }
        if (!empty($filter['desil'])) {
            $builder->where('se.kategori_desil', $filter['desil'] === 'none' ? null : $filter['desil']);
        }
        if (!empty($filter['status'])) {
            $builder->where('pk.status_kemiskinan', $filter['status']);
        }
        if (!empty($filter['petugas'])) {
            $builder->like('u.fullname', $filter['petugas']);
        }

        return $builder->orderBy('pk.updated_at', 'ASC')->get()->getResultArray() ?? [];
    }

    public function getDataKemiskinanFinal(array $filter)
    {
        $db = $this->db;
        $userRole = session()->role_id ?? 99;

        $builder = $db->table('dtsen_penentuan_kemiskinan pk')
            ->select('
                pk.id, pk.status_kemiskinan, pk.verified_at,
                kk.no_kk, kk.kepala_keluarga, art.nik, rt.rw, rt.rt
            ')
            ->join('dtsen_kk kk', 'kk.id_kk = pk.dtsen_kk_id')
            ->join('dtsen_art art', 'art.id_kk = kk.id_kk AND art.hubungan_keluarga = 1 AND art.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('pk.status_verifikasi', 'approved')
            ->where('kk.deleted_at', null); // 🛡️ Cegah data sampah

        // 🔐 Panggil Filter Trait
        $this->applyWilayahFilter($builder, $filter, $userRole);

        if (!empty($filter['kode_desa'])) {
            $builder->where('rt.kode_desa', $filter['kode_desa']);
        }
        if (!empty($filter['status'])) {
            $builder->where('pk.status_kemiskinan', $filter['status']);
        }

        return $builder->orderBy('pk.verified_at', 'DESC')->get()->getResultArray();
    }

    public function countPending(array $filter = [])
    {
        $db = $this->db;
        $userRole = session()->role_id ?? 99;

        $builder = $db->table('dtsen_penentuan_kemiskinan pk')
            ->join('dtsen_kk kk', 'kk.id_kk = pk.dtsen_kk_id')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('pk.status_verifikasi', 'pending')
            ->where('kk.deleted_at', null);

        // Filter jumlah pending berdasarkan wilayah petugas
        $this->applyWilayahFilter($builder, $filter, $userRole);

        return $builder->countAllResults();
    }
}
