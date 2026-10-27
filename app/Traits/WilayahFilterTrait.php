<?php

namespace App\Traits;

trait WilayahFilterTrait
{
    /**
     * Mendapatkan alias tabel dtsen_rt dari builder
     */
    protected function getRtAlias(\CodeIgniter\Database\BaseBuilder $builder): string
    {
        $sql = $builder->getCompiledSelect(false);

        // Cari pola: FROM/JOIN [db.]dtsen_rt [AS] `alias`
        if (preg_match('/(?:FROM|JOIN)\s+`?(?:[a-zA-Z0-9_]+\.)?dtsen_rt`?\s+(?:AS\s+)?`?([a-zA-Z0-9_]+)`?/i', $sql, $m)) {
            $word = strtolower($m[1]);
            $keywords = ['on', 'where', 'left', 'right', 'inner', 'cross', 'join', 'group', 'order', 'limit', 'using'];
            if (!in_array($word, $keywords, true)) {
                return $m[1];
            }
        }

        // Default jika gagal atau tanpa alias -> tetap "rt"
        return 'rt';
    }

    /**
     * Helper membuat variasi format numerik untuk pencocokan database yang fleksibel
     * Contoh: input '1', '01', atau '001' -> ['1', '01', '001']
     */
    public static function getWilayahVariants($val): array
    {
        $val = trim((string)$val);
        if ($val === '') {
            return [];
        }

        $res = [$val];
        if (is_numeric($val)) {
            $num = (int)$val;
            $res[] = (string)$num;
            $res[] = str_pad((string)$num, 2, '0', STR_PAD_LEFT);
            $res[] = str_pad((string)$num, 3, '0', STR_PAD_LEFT);
        }

        return array_values(array_unique($res));
    }

    /**
     * Parsing string wilayah_tugas menjadi array terstruktur
     * Format contoh: "001:005,007|004:001" atau "001:007"
     * Output: ['001' => ['005', '007'], '004' => ['001']]
     */
    public static function parseWilayahTugasMap(?string $wilayahTugas): array
    {
        $result = [];
        $wilayahTugas = trim((string)$wilayahTugas);
        if ($wilayahTugas === '') {
            return $result;
        }

        // Bersihkan prefix RW: jika ada
        $clean = trim(str_ireplace('RW:', '', $wilayahTugas));
        $bloks = preg_split('/[|;]/', $clean);

        foreach ($bloks as $blok) {
            $blok = trim($blok);
            if ($blok === '') continue;

            [$rw, $rtCSV] = array_pad(explode(':', $blok), 2, '');
            $rw = trim($rw);
            if ($rw === '') continue;

            $rts = [];
            if ($rtCSV !== '') {
                foreach (explode(',', $rtCSV) as $rt) {
                    $rt = trim($rt);
                    if ($rt !== '') {
                        $rts[] = $rt;
                    }
                }
            }

            if (!isset($result[$rw])) {
                $result[$rw] = [];
            }
            if (!empty($rts)) {
                $result[$rw] = array_values(array_unique(array_merge($result[$rw], $rts)));
            }
        }

        return $result;
    }

    public static function parseWilayahTugas(?string $wilayahTugas): array
    {
        return self::parseWilayahTugasMap($wilayahTugas);
    }

    /**
     * Mendapatkan daftar RW yang diizinkan untuk user
     */
    public static function getAllowedRwList(?string $wilayahTugas, ?string $level = null): array
    {
        $parsed = self::parseWilayahTugasMap($wilayahTugas);
        if (!empty($parsed)) {
            return array_keys($parsed);
        }
        if (!empty($level)) {
            return [trim($level)];
        }
        return [];
    }

    /**
     * Mendapatkan daftar RT yang diizinkan untuk RW tertentu
     */
    public static function getAllowedRtList(?string $wilayahTugas, string $rw, ?string $level = null): array
    {
        $parsed = self::parseWilayahTugasMap($wilayahTugas);
        if (!empty($parsed)) {
            $rwVariants = self::getWilayahVariants($rw);
            foreach ($parsed as $assignedRw => $rtList) {
                if (in_array((string)$assignedRw, $rwVariants, true)) {
                    return $rtList;
                }
            }
            return []; // RW ini tidak ditugaskan
        }

        return []; // Tidak ada batasan spesifik RT (berarti seluruh RT di RW tersebut)
    }

    /**
     * Terapkan filter wilayah komprehensif pada Query Builder
     * Mencegah data berceceran ke petugas lain untuk role_id >= 4
     *
     * @param \CodeIgniter\Database\BaseBuilder $builder
     * @param array $filter [
     *      'kode_desa'     => string,
     *      'rw'            => string|array,
     *      'rt'            => string|array,
     *      'wilayah_tugas' => string,
     *      'level'         => string,
     * ]
     * @param int $userRole
     * @return \CodeIgniter\Database\BaseBuilder
     */
    protected function applyWilayahFilter(\CodeIgniter\Database\BaseBuilder $builder, array $filter, int $userRole)
    {
        $alias = $this->getRtAlias($builder);

        // 1. Filter Kode Desa (jika ada)
        if (!empty($filter['kode_desa'])) {
            $builder->where("$alias.kode_desa", $filter['kode_desa']);
        }

        // 2. Filter Manual RW (dari input UI / dropdown / parameter)
        if (!empty($filter['rw']) && $filter['rw'] !== 'all') {
            $rws = is_array($filter['rw']) ? $filter['rw'] : [$filter['rw']];
            $allRwVariants = [];
            foreach ($rws as $rw) {
                $allRwVariants = array_merge($allRwVariants, self::getWilayahVariants($rw));
            }
            if (!empty($allRwVariants)) {
                $builder->whereIn("$alias.rw", array_values(array_unique($allRwVariants)));
            }
        }

        // 3. Filter Manual RT (dari input UI / dropdown / parameter)
        if (!empty($filter['rt']) && $filter['rt'] !== 'all') {
            $rts = is_array($filter['rt']) ? $filter['rt'] : [$filter['rt']];
            $allRtVariants = [];
            foreach ($rts as $rt) {
                $allRtVariants = array_merge($allRtVariants, self::getWilayahVariants($rt));
            }
            if (!empty($allRtVariants)) {
                $builder->whereIn("$alias.rt", array_values(array_unique($allRtVariants)));
            }
        }

        // 4. Karantina Wilayah Tugas Khusus Role Kewilayahan (role_id >= 4 / Petugas RT-RW)
        if ($userRole >= 4) {
            $wilayahTugas = trim((string)($filter['wilayah_tugas'] ?? ''));
            $levelUser    = trim((string)($filter['level'] ?? $filter['rw_petugas'] ?? ''));

            if ($wilayahTugas !== '') {
                $parsed = self::parseWilayahTugasMap($wilayahTugas);

                if (!empty($parsed)) {
                    $builder->groupStart();
                    $first = true;

                    foreach ($parsed as $rw => $rtList) {
                        $rwVariants = self::getWilayahVariants($rw);

                        if ($first) {
                            $builder->groupStart();
                            $first = false;
                        } else {
                            $builder->orGroupStart();
                        }

                        $builder->whereIn("$alias.rw", $rwVariants);

                        if (!empty($rtList)) {
                            $rtVariants = [];
                            foreach ($rtList as $rt) {
                                $rtVariants = array_merge($rtVariants, self::getWilayahVariants($rt));
                            }
                            $builder->whereIn("$alias.rt", array_values(array_unique($rtVariants)));
                        }

                        $builder->groupEnd();
                    }

                    $builder->groupEnd();
                } else {
                    // String wilayah_tugas ada tapi format tidak valid -> kunci aman
                    $builder->where('1 = 0', null, false);
                }
            } elseif ($levelUser !== '') {
                // Fallback akun petugas yang hanya memiliki 'level' RW
                $levelVariants = self::getWilayahVariants($levelUser);
                $builder->whereIn("$alias.rw", $levelVariants);
            } else {
                // Petugas tanpa wilayah penugasan -> kunci aman agar data tidak berceceran
                $builder->where('1 = 0', null, false);
            }
        }

        return $builder;
    }

    /**
     * Memeriksa apakah suatu baris data (record) diizinkan diakses oleh user tertentu
     * Mencegah IDOR (Insecure Direct Object Reference)
     *
     * @param array $filterUser ['kode_desa' => ..., 'wilayah_tugas' => ..., 'level' => ...]
     * @param int $userRole
     * @param string|null $recordKodeDesa
     * @param string|null $recordRw
     * @param string|null $recordRt
     * @return bool
     */
    public static function isRecordAllowed(array $filterUser, int $userRole, ?string $recordKodeDesa, ?string $recordRw, ?string $recordRt): bool
    {
        // 1. Role 1 & 2 (Super Admin / Admin) bebas akses semua data
        if ($userRole < 3) {
            return true;
        }

        // 2. Role 3 (Pendamping Desa / Desa): Cek kesesuaian Kode Desa
        if ($userRole == 3) {
            $userDesa = trim((string)($filterUser['kode_desa'] ?? ''));
            if ($userDesa === '') {
                return true;
            }
            return trim((string)$recordKodeDesa) === $userDesa;
        }

        // 3. Role 4+ (Petugas RT/RW): Karantina mutlak wilayah kerja
        if ($userRole >= 4) {
            $userDesa = trim((string)($filterUser['kode_desa'] ?? ''));
            if ($userDesa !== '' && !empty($recordKodeDesa) && trim((string)$recordKodeDesa) !== $userDesa) {
                return false;
            }

            $wilayahTugas = trim((string)($filterUser['wilayah_tugas'] ?? ''));
            $levelUser    = trim((string)($filterUser['level'] ?? ''));

            if ($wilayahTugas !== '') {
                $parsed = self::parseWilayahTugasMap($wilayahTugas);
                if (empty($parsed)) {
                    return false;
                }

                $recordRwVariants = self::getWilayahVariants($recordRw);
                $matchedRwKey = null;

                foreach (array_keys($parsed) as $assignedRw) {
                    if (in_array((string)$assignedRw, $recordRwVariants, true)) {
                        $matchedRwKey = $assignedRw;
                        break;
                    }
                }

                if ($matchedRwKey === null) {
                    return false; // RW tidak masuk wilayah tugas
                }

                $assignedRts = $parsed[$matchedRwKey] ?? [];
                if (empty($assignedRts)) {
                    // Berhak atas seluruh RT di RW tersebut
                    return true;
                }

                $recordRtVariants = self::getWilayahVariants($recordRt);
                foreach ($assignedRts as $rt) {
                    if (in_array((string)$rt, $recordRtVariants, true)) {
                        return true;
                    }
                }

                return false; // RT tidak cocok
            }

            if ($levelUser !== '') {
                $levelVariants = self::getWilayahVariants($levelUser);
                $recordRwVariants = self::getWilayahVariants($recordRw);
                return !empty(array_intersect($levelVariants, $recordRwVariants));
            }

            return false;
        }

        return true;
    }
}
