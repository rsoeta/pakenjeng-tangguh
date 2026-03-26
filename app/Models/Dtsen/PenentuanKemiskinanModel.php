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

        /**
         * ======================================================
         * 1️⃣ QUERY DASAR KK
         * ======================================================
         */

        $builder = $db->table('dtsen_kk kk')
            ->select('
            kk.id_kk,
            kk.no_kk,
            kk.kepala_keluarga,
            kk.alamat,
            rt.rw,
            rt.rt,
            art.nik,
            se.kategori_desil
        ')
            ->join(
                'dtsen_art art',
                'art.id_kk = kk.id_kk AND art.hubungan_keluarga = 1',
                'left'
            )
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left')
            ->where('kk.deleted_at', null);


        /**
         * ======================================================
         * 🔐 FILTER WILAYAH TUGAS
         * ======================================================
         */
        $userRole = session()->role_id ?? 99;

        $this->applyWilayahFilter($builder, $filter, $userRole);

        /**
         * ======================================================
         * 2️⃣ HANYA KK VERIFIED
         * ======================================================
         */

        $builder->whereIn('kk.id_kk', function ($sub) {

            $sub->select('dtsen_kk_id')
                ->from('dtsen_usulan')
                ->whereIn('status', ['verified', 'diverifikasi']);
        });

        /**
         * ======================================================
         * 3️⃣ HILANGKAN YANG MASIH AKTIF (pending & approved)
         * ======================================================
         */

        $builder->whereNotIn('kk.id_kk', function ($sub) {

            $sub->select('dtsen_kk_id')
                ->from('dtsen_penentuan_kemiskinan')
                ->whereIn('status_verifikasi', ['pending', 'approved']);
        });

        /**
         * ======================================================
         * 4️⃣ FILTER RW / RT / DESIL
         * ======================================================
         */

        if (!empty($filter['rw']) && $filter['rw'] !== 'all') {
            $builder->where('rt.rw', $filter['rw']);
        }

        if (!empty($filter['rt']) && $filter['rt'] !== 'all') {
            $builder->where('rt.rt', $filter['rt']);
        }

        if (!empty($filter['desil']) && $filter['desil'] !== 'all') {

            if ($filter['desil'] === 'none') {
                $builder->where('se.kategori_desil', null);
            } else {
                $builder->where('se.kategori_desil', $filter['desil']);
            }
        }

        return $builder
            ->get()
            ->getResultArray();
    }

    /*
    ====================================
    DATA VERIFIKASI OPERATOR
    ====================================
    */

    public function getVerifikasiKemiskinan(array $filter)
    {
        $db = $this->db;

        $builder = $db->table('dtsen_penentuan_kemiskinan pk')
            ->select('
            pk.id,
            pk.status_kemiskinan,
            pk.created_by,
            u.fullname as petugas_entri,
            pk.catatan,
            kk.no_kk,
            kk.kepala_keluarga,
            art.nik,
            rt.rw,
            rt.rt,
            se.kategori_desil
        ')
            ->join('dtsen_kk kk', 'kk.id_kk = pk.dtsen_kk_id')
            ->join(
                'dtsen_art art',
                'art.id_kk = kk.id_kk AND art.hubungan_keluarga = 1',
                'left'
            )
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left')
            ->join('dtks_users u', 'u.id = pk.created_by', 'left')
            ->where('pk.status_verifikasi', 'pending');

        /**
         * ======================================================
         * 🔐 FILTER DESA
         * ======================================================
         */

        if (!empty($filter['kode_desa'])) {
            $builder->where('rt.kode_desa', $filter['kode_desa']);
        }

        /**
         * ======================================================
         * 🔐 FILTER WILAYAH TUGAS (LOGIKA SAMA DENGAN KK MODEL)
         * ======================================================
         */

        if (!empty($filter['wilayah_tugas'])) {

            $wilayahTugas = str_replace('RW:', '', trim($filter['wilayah_tugas']));
            $blokRW = preg_split('/[|;]/', $wilayahTugas);

            $builder->groupStart();

            foreach ($blokRW as $blok) {

                $blok = trim($blok);
                if (!$blok) continue;

                [$rw, $rtStr] = array_pad(explode(':', $blok), 2, null);
                $rtList = $rtStr ? explode(',', $rtStr) : [];

                $builder->orGroupStart()
                    ->groupStart()
                    ->where('rt.rw', $rw)
                    ->orWhere('rt.rw', str_pad($rw, 2, '0', STR_PAD_LEFT))
                    ->groupEnd();

                if (!empty($rtList)) {

                    $rtVariants = [];

                    foreach ($rtList as $rt) {
                        $rtVariants[] = $rt;
                        $rtVariants[] = str_pad($rt, 2, '0', STR_PAD_LEFT);
                    }

                    $builder->whereIn('rt.rt', $rtVariants);
                }

                $builder->groupEnd();
            }

            $builder->groupEnd();
        }

        return $builder
            ->orderBy('pk.created_at', 'ASC')
            ->get()
            ->getResultArray() ?? [];
    }

    /*
    ====================================
    DATA KEMISKINAN FINAL
    ====================================
    */

    public function getDataKemiskinanFinal(array $filter)
    {
        $db = $this->db;

        $builder = $db->table('dtsen_penentuan_kemiskinan pk')
            ->select('
            pk.id,
            pk.status_kemiskinan,
            pk.verified_at,
            kk.no_kk,
            kk.kepala_keluarga,
            art.nik,
            rt.rw,
            rt.rt
        ')
            ->join('dtsen_kk kk', 'kk.id_kk = pk.dtsen_kk_id')
            ->join(
                'dtsen_art art',
                'art.id_kk = kk.id_kk AND art.hubungan_keluarga = 1',
                'left'
            )
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('pk.status_verifikasi', 'approved');

        /**
         * ======================================================
         * 🔐 FILTER DESA
         * ======================================================
         */

        if (!empty($filter['kode_desa'])) {
            $builder->where('rt.kode_desa', $filter['kode_desa']);
        }

        /**
         * ======================================================
         * 🔐 FILTER WILAYAH TUGAS
         * ======================================================
         */

        if (!empty($filter['wilayah_tugas'])) {

            $wilayahTugas = str_replace('RW:', '', trim($filter['wilayah_tugas']));
            $blokRW = preg_split('/[|;]/', $wilayahTugas);

            $builder->groupStart();

            foreach ($blokRW as $blok) {

                $blok = trim($blok);
                if (!$blok) continue;

                [$rw, $rtStr] = array_pad(explode(':', $blok), 2, null);
                $rtList = $rtStr ? explode(',', $rtStr) : [];

                $builder->orGroupStart()
                    ->groupStart()
                    ->where('rt.rw', $rw)
                    ->orWhere('rt.rw', str_pad($rw, 2, '0', STR_PAD_LEFT))
                    ->groupEnd();

                if (!empty($rtList)) {

                    $rtVariants = [];

                    foreach ($rtList as $rt) {
                        $rtVariants[] = $rt;
                        $rtVariants[] = str_pad($rt, 2, '0', STR_PAD_LEFT);
                    }

                    $builder->whereIn('rt.rt', $rtVariants);
                }

                $builder->groupEnd();
            }

            $builder->groupEnd();
        }

        return $builder
            ->orderBy('pk.verified_at', 'DESC')
            ->get()
            ->getResultArray();
    }


    /*
    ====================================
    JUMLAH DATA MENUNGGU VERIFIKASI
    ====================================
    */

    public function countPending()
    {
        return $this->db->table('dtsen_penentuan_kemiskinan')

            ->where('status_verifikasi', 'pending')

            ->countAllResults();
    }
}
