<?php

namespace App\Traits;

/**
 * Trait WilayahFilterTrait
 *
 * Berisi fungsi applyWilayahFilter() agar bisa digunakan di semua model
 * untuk menyamakan logika penyaringan berdasarkan kode_desa, rw, dan wilayah_tugas.
 */
trait WilayahFilterTrait
{
    /**
     * Terapkan filter wilayah seperti di getFilteredData()
     * agar konsisten di semua model.
     */
    protected function applyWilayahFilter(\CodeIgniter\Database\BaseBuilder $builder, array $filter, int $userRole)
    {
        // 1️⃣ Filter berdasarkan kode desa
        if (!empty($filter['kode_desa'])) {
            $builder->where('rt.kode_desa', $filter['kode_desa']);
        }

        // 2️⃣ Filter RW (langsung, jika tersedia)
        if (!empty($filter['rw'])) {
            $rws = is_array($filter['rw']) ? $filter['rw'] : [$filter['rw']];
            $rwVariants = [];
            foreach ($rws as $rw) {
                $rw = trim($rw);
                if ($rw === '') continue;
                $rwVariants[] = $rw;
                $rwVariants[] = str_pad($rw, 2, '0', STR_PAD_LEFT);
            }
            if (!empty($rwVariants)) {
                $builder->groupStart()
                    ->whereIn('rt.rw', $rwVariants)
                    ->groupEnd();
            }
        }

        // 3️⃣ Filter wilayah_tugas (untuk role operator RW/RT)
        if ($userRole > 3 && !empty($filter['wilayah_tugas'])) {
            $wilayahTugas = trim($filter['wilayah_tugas']);
            $wilayahTugas = str_replace('RW:', '', $wilayahTugas);
            $blokRW = preg_split('/[|;]/', $wilayahTugas);

            $builder->groupStart();
            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if (!$blok) continue;

                $parts = explode(':', $blok);
                $rw = trim($parts[0]);
                $rtList = isset($parts[1]) ? explode(',', $parts[1]) : [];

                $builder->orGroupStart()
                    ->groupStart()
                    ->where('rt.rw', $rw)
                    ->orWhere('rt.rw', str_pad($rw, 2, '0', STR_PAD_LEFT))
                    ->groupEnd();

                if (!empty($rtList)) {
                    $rtVariants = [];
                    foreach ($rtList as $rt) {
                        $rt = trim($rt);
                        if ($rt === '') continue;
                        $rtVariants[] = $rt;
                        $rtVariants[] = str_pad($rt, 2, '0', STR_PAD_LEFT);
                    }
                    if (!empty($rtVariants)) {
                        $builder->whereIn('rt.rt', $rtVariants);
                    }
                }

                $builder->groupEnd();
            }
            $builder->groupEnd();
        }

        return $builder;
    }
}
