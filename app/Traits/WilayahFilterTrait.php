<?php

namespace App\Traits;

trait WilayahFilterTrait
{
    /**
     * Mendapatkan alias tabel RT dari builder
     */
    private function getRtAlias(\CodeIgniter\Database\BaseBuilder $builder): string
    {
        $sql = $builder->getCompiledSelect(false);

        // Cari pola: dtsen_rt `alias`
        if (preg_match('/FROM\s+`?dtsen_rt`?\s+`?(\w+)`?/i', $sql, $m)) {
            return $m[1];
        }

        if (preg_match('/JOIN\s+`?dtsen_rt`?\s+`?(\w+)`?/i', $sql, $m)) {
            return $m[1];
        }

        // Cari pola tanpa backtick
        if (preg_match('/FROM\s+dtsen_rt\s+(\w+)/i', $sql, $m)) {
            return $m[1];
        }

        if (preg_match('/JOIN\s+dtsen_rt\s+(\w+)/i', $sql, $m)) {
            return $m[1];
        }

        // Default jika gagal → tetap "rt"
        return 'rt';
    }

    /**
     * Terapkan filter wilayah
     */
    protected function applyWilayahFilter(\CodeIgniter\Database\BaseBuilder $builder, array $filter, int $userRole)
    {
        log_message('error', '### TRAIT VERSION CHECK: 2025-12-02 ###');

        // DEBUG → cek apakah trait yang baru dipakai
        log_message('error', '### APPLY WILAYAH FILTER DIPANGGIL ###');

        $sqlBefore = $builder->getCompiledSelect(false);
        log_message('error', "SQL BEFORE = \n" . $sqlBefore);

        $alias = $this->getRtAlias($builder);

        // Filter kode desa
        if (!empty($filter['kode_desa'])) {
            $builder->where("$alias.kode_desa", $filter['kode_desa']);
        }

        // Filter RW
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
                    ->whereIn("$alias.rw", $rwVariants)
                    ->groupEnd();
            }
        }

        // Filter wilayah tugas (role > 3)
        if ($userRole > 3 && !empty($filter['wilayah_tugas'])) {

            $wilayah = trim(str_replace('RW:', '', $filter['wilayah_tugas']));
            $blokRW = preg_split('/[|;]/', $wilayah);

            $builder->groupStart();

            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if ($blok === '') continue;

                [$rw, $rtCSV] = array_pad(explode(':', $blok), 2, '');

                $rtList = $rtCSV ? explode(',', $rtCSV) : [];

                $builder->orGroupStart()
                    ->groupStart()
                    ->where("$alias.rw", $rw)
                    ->orWhere("$alias.rw", str_pad($rw, 2, '0', STR_PAD_LEFT))
                    ->groupEnd();

                if (!empty($rtList)) {
                    $rtVariants = [];
                    foreach ($rtList as $rt) {
                        $rt = trim($rt);
                        if ($rt === '') continue;

                        $rtVariants[] = $rt;
                        $rtVariants[] = str_pad($rt, 2, '0', STR_PAD_LEFT);
                    }
                    $builder->whereIn("$alias.rt", $rtVariants);
                }

                $builder->groupEnd();
            }

            $builder->groupEnd();
        }

        return $builder;
    }
}
