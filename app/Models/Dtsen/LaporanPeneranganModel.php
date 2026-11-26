<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class LaporanPeneranganModel extends Model
{
    use WilayahFilterTrait;

    protected $db;

    public const KATEGORI_LIST = [
        'Listrik PLN dengan meteran',
        'Listrik PLN tanpa meteran',
        'Listrik non-PLN',
        'Bukan listrik',
        'Belum Terdata',
    ];

    public const DAYA_GROUPS = [
        '450 WATT',
        '900 WATT',
        '1.300 WATT',
        '2.200 WATT+',
        'Belum Diisi / Lainnya',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Normalisasi label Sumber Penerangan Utama ke 4 kategori standar DTSEN + Belum Terdata.
     */
    public static function normalizeSumberPenerangan(?string $raw): string
    {
        $val = trim((string) $raw);
        if ($val === '' || strcasecmp($val, 'null') === 0) {
            return 'Belum Terdata';
        }

        foreach (self::KATEGORI_LIST as $kat) {
            if (strcasecmp($val, $kat) === 0) {
                return $kat;
            }
        }

        $lower = strtolower($val);
        if (str_contains($lower, 'tanpa meteran') || str_contains($lower, 'nyalur') || str_contains($lower, 'bersama')) {
            return 'Listrik PLN tanpa meteran';
        }
        if (str_contains($lower, 'non-pln') || str_contains($lower, 'non pln') || str_contains($lower, 'generator') || str_contains($lower, 'solar')) {
            return 'Listrik non-PLN';
        }
        if (str_contains($lower, 'bukan listrik') || str_contains($lower, 'minyak') || str_contains($lower, 'lilin') || str_contains($lower, 'tidak ada')) {
            return 'Bukan listrik';
        }
        if (str_contains($lower, 'pln')) {
            return 'Listrik PLN dengan meteran';
        }

        return 'Belum Terdata';
    }

    /**
     * Parsing daftar daya listrik (bisa multi-meteran dipisah koma) menjadi string rapi & grup standar.
     *
     * @return array{raw:string, items:array<int,string>, primary_group:string}
     */
    public static function parseDayaListrik(?string $rawDaya, string $kategori): array
    {
        $raw = trim((string) $rawDaya);
        if ($raw === '' || strcasecmp($raw, 'null') === 0) {
            return [
                'raw'           => '',
                'items'         => [],
                'primary_group' => ($kategori === 'Listrik PLN dengan meteran') ? 'Belum Diisi / Lainnya' : '-',
            ];
        }

        $parts = array_filter(array_map('trim', explode(',', $raw)), static function ($v) {
            return $v !== '' && strcasecmp($v, 'null') !== 0;
        });

        $normalizedItems = [];
        $rankMap = [
            '450 WATT'              => 1,
            '900 WATT'              => 2,
            '1.300 WATT'            => 3,
            '2.200 WATT+'           => 4,
            'Belum Diisi / Lainnya' => 0,
        ];
        $bestGroup = ($kategori === 'Listrik PLN dengan meteran') ? 'Belum Diisi / Lainnya' : '-';
        $bestRank  = 0;

        foreach ($parts as $item) {
            $clean = preg_replace('/\s+/', ' ', strtoupper($item));
            $clean = str_replace('< =', '<=', $clean);

            // Standarisasi angka polos seperti "450" -> "450 WATT"
            if ($clean === '450') {
                $clean = '450 WATT';
            } elseif ($clean === '900') {
                $clean = '900 WATT';
            } elseif ($clean === '1300' || $clean === '1.300') {
                $clean = '1.300 WATT';
            } elseif ($clean === '2200' || $clean === '2.200') {
                $clean = '2.200 WATT';
            }

            $normalizedItems[] = $clean;

            $grp = 'Belum Diisi / Lainnya';
            if (str_contains($clean, '450')) {
                $grp = '450 WATT';
            } elseif (str_contains($clean, '<= 900') || $clean === '900 WATT') {
                $grp = '900 WATT';
            } elseif (str_contains($clean, '1.300') || str_contains($clean, '1300')) {
                $grp = '1.300 WATT';
            } elseif (str_contains($clean, '2.200') || str_contains($clean, '2200') || str_contains($clean, '> 900')) {
                $grp = '2.200 WATT+';
            }

            $rank = $rankMap[$grp] ?? 0;
            if ($rank > $bestRank) {
                $bestRank  = $rank;
                $bestGroup = $grp;
            }
        }

        if (empty($normalizedItems)) {
            return [
                'raw'           => '',
                'items'         => [],
                'primary_group' => ($kategori === 'Listrik PLN dengan meteran') ? 'Belum Diisi / Lainnya' : '-',
            ];
        }

        return [
            'raw'           => implode(', ', $normalizedItems),
            'items'         => $normalizedItems,
            'primary_group' => ($kategori === 'Listrik PLN dengan meteran') ? $bestGroup : ($bestRank > 0 ? $bestGroup : '-'),
        ];
    }

    private function getTahapScore(?string $tahapStr): int
    {
        if (empty($tahapStr)) {
            return 0;
        }
        if (preg_match('/Tahap\s*(\d+)\s*Tahun\s*(\d+)/i', $tahapStr, $m)) {
            return ((int) $m[2] * 10) + (int) $m[1];
        }
        if (preg_match('/(\d{4})/', $tahapStr, $m)) {
            return (int) $m[1] * 10;
        }
        return 0;
    }

    private function normalizeWilayahCode(?string $val): string
    {
        $val = trim((string) $val);
        if ($val === '') {
            return '';
        }
        return is_numeric($val) ? str_pad((string) (int) $val, 3, '0', STR_PAD_LEFT) : $val;
    }

    /**
     * Ambil daftar Desa (khusus Super Admin / Admin) & daftar RW/RT untuk dropdown filter.
     */
    public function getFilterMaster(int $roleId, ?string $sessionKodeDesa = null): array
    {
        // Daftar Desa yang memiliki data di dtsen_rt
        $desaBuilder = $this->db->table('dtsen_rt rt')
            ->select('rt.kode_desa, v.name as nama_desa')
            ->join('tb_villages v', 'v.id = rt.kode_desa', 'left')
            ->where('rt.kode_desa IS NOT NULL')
            ->where("rt.kode_desa != ''");

        if ($roleId >= 3 && !empty($sessionKodeDesa)) {
            $desaBuilder->where('rt.kode_desa', $sessionKodeDesa);
        }

        $desaList = $desaBuilder
            ->groupBy('rt.kode_desa, v.name')
            ->orderBy('v.name', 'ASC')
            ->orderBy('rt.kode_desa', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($desaList as &$d) {
            $d['nama_desa_label'] = !empty($d['nama_desa'])
                ? ucwords(strtolower($d['nama_desa']))
                : $d['kode_desa'];
        }
        unset($d);

        // Daftar RW awal
        $rwTargetDesa = ($roleId >= 3) ? $sessionKodeDesa : null;
        $rwList = $this->getRwListByDesa($rwTargetDesa);

        return [
            'desa_list' => $desaList,
            'rw_list'   => $rwList,
        ];
    }

    public function getRwListByDesa(?string $kodeDesa = null): array
    {
        $builder = $this->db->table('dtsen_rt')
            ->select('rw')
            ->where('rw IS NOT NULL')
            ->where("rw != ''");

        if (!empty($kodeDesa) && $kodeDesa !== 'all') {
            $builder->where('kode_desa', $kodeDesa);
        }

        $rows = $builder->groupBy('rw')->orderBy('rw', 'ASC')->get()->getResultArray();
        $list = [];
        foreach ($rows as $r) {
            $norm = $this->normalizeWilayahCode($r['rw']);
            if ($norm !== '') {
                $list[$norm] = $norm;
            }
        }
        ksort($list);
        return array_values($list);
    }

    public function getRtListByDesaRw(?string $kodeDesa, string $rw): array
    {
        $rwVariants = self::getWilayahVariants($rw);
        if (empty($rwVariants)) {
            return [];
        }

        $builder = $this->db->table('dtsen_rt')
            ->select('rt')
            ->whereIn('rw', $rwVariants)
            ->where('rt IS NOT NULL')
            ->where("rt != ''");

        if (!empty($kodeDesa) && $kodeDesa !== 'all') {
            $builder->where('kode_desa', $kodeDesa);
        }

        $rows = $builder->groupBy('rt')->orderBy('rt', 'ASC')->get()->getResultArray();
        $list = [];
        foreach ($rows as $r) {
            $norm = $this->normalizeWilayahCode($r['rt']);
            if ($norm !== '') {
                $list[$norm] = $norm;
            }
        }
        ksort($list);
        return array_values($list);
    }

    /**
     * 🚀 Bangun dataset lengkap (Smart Merge Usulan + Fallback Master RT + Desil + Bansos)
     * Sekali kueri batch cepat, lalu difilter di memori agar konsisten antara Grafik, Matriks, BNBA, Excel & Cetak.
     */
    public function getDataset(array $filter, int $roleId = 3): array
    {
        $db = $this->db;

        // Kunci kode_desa wajib untuk Operator (role_id = 3)
        $kodeDesa = trim((string) ($filter['kode_desa'] ?? ''));
        if ($roleId >= 3) {
            $kodeDesa = trim((string) ($filter['session_kode_desa'] ?? $kodeDesa));
        } elseif ($kodeDesa === 'all') {
            $kodeDesa = '';
        }

        $sumberMode = trim((string) ($filter['sumber_mode'] ?? 'all'));
        if (!in_array($sumberMode, ['all', 'verified', 'draft_only', 'master_only'], true)) {
            $sumberMode = 'all';
        }

        // 1️⃣ Query dasar KK + RT + SE + Desa
        // Gunakan MAX/subquery ringan hanya jika perlu, dtsen_se unik per id_kk (kita jamin dengan subquery aman)
        $builder = $db->table('dtsen_kk kk')
            ->select('
                kk.id_kk,
                kk.no_kk,
                kk.kepala_keluarga,
                kk.nik_kepala_keluarga,
                kk.alamat,
                kk.dusun,
                rt.id_rt,
                rt.kode_desa,
                rt.rw,
                rt.rt,
                rt.sumber_listrik as rt_sumber_listrik,
                rt.jumlah_meteran_listrik as rt_jml_meteran,
                rt.nomor_pelanggan as rt_no_pelanggan,
                rt.nomor_meter as rt_no_meter,
                rt.daya_listrik as rt_daya_listrik,
                v.name as nama_desa,
                se.kategori_desil
            ', false)
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left')
            ->join('tb_villages v', 'v.id = rt.kode_desa', 'left')
            ->groupStart()
            ->where('kk.deleted_at', null)
            ->orWhere('kk.deleted_at', '0000-00-00 00:00:00')
            ->groupEnd();

        if ($kodeDesa !== '') {
            $builder->where('rt.kode_desa', $kodeDesa);
        }

        $rows = $builder->orderBy('rt.rw', 'ASC')
            ->orderBy('rt.rt', 'ASC')
            ->orderBy('kk.kepala_keluarga', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($rows)) {
            return [];
        }

        // Deduplikasi defensif berdasarkan id_kk
        $uniqueRows = [];
        foreach ($rows as $r) {
            $kkId = (int) $r['id_kk'];
            if (!isset($uniqueRows[$kkId])) {
                $uniqueRows[$kkId] = $r;
            }
        }
        $rows = array_values($uniqueRows);
        $kkIds = array_column($rows, 'id_kk');
        $kkSet = array_fill_keys($kkIds, true);

        // 2️⃣ Batch fetch dtsen_usulan terbaru (jika bukan master_only)
        $usulanMap = [];          // usulan terbaru secara umum (utk override alamat/rw/rt)
        $usulanListrikMap = [];   // usulan terbaru yg memiliki data sumber_listrik tidak kosong

        if ($sumberMode !== 'master_only') {
            $statuses = match ($sumberMode) {
                'verified'   => ['submitted', 'verified', 'diverifikasi'],
                'draft_only' => ['draft'],
                default      => ['draft', 'submitted', 'verified', 'diverifikasi'],
            };

            $uBuilder = $db->table('dtsen_usulan u')
                ->select('u.id, u.dtsen_kk_id, u.status, u.payload, u.updated_at')
                ->where('u.dtsen_kk_id IS NOT NULL', null, false)
                ->whereIn('u.status', $statuses)
                ->orderBy('u.id', 'DESC');

            if ($kodeDesa !== '') {
                $uBuilder->join('dtsen_kk kk', 'kk.id_kk = u.dtsen_kk_id', 'inner')
                    ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'inner')
                    ->where('rt.kode_desa', $kodeDesa);
            }

            $usulanRows = $uBuilder->get()->getResultArray();

            foreach ($usulanRows as $u) {
                $kkId = (int) $u['dtsen_kk_id'];
                if (!isset($kkSet[$kkId])) {
                    continue;
                }

                $payload = json_decode($u['payload'] ?? '{}', true);
                if (!is_array($payload)) {
                    continue;
                }

                if (!isset($usulanMap[$kkId])) {
                    $usulanMap[$kkId] = [
                        'id'         => $u['id'],
                        'status'     => strtolower(trim((string) $u['status'])),
                        'payload'    => $payload,
                        'updated_at' => $u['updated_at'],
                    ];
                }

                // Catat usulan terbaru yang benar-benar mengisi sumber_listrik agar tidak tertimpa draft kosong
                $sl = trim((string) ($payload['perumahan']['kondisi']['sumber_listrik'] ?? ''));
                if ($sl !== '' && strcasecmp($sl, 'null') !== 0 && !isset($usulanListrikMap[$kkId])) {
                    $usulanListrikMap[$kkId] = [
                        'id'         => $u['id'],
                        'status'     => strtolower(trim((string) $u['status'])),
                        'payload'    => $payload,
                        'updated_at' => $u['updated_at'],
                    ];
                }
            }
        }

        // 3️⃣ Batch fetch Bansos aktif pada tahap salur terbaru (identik dengan DtsenSe::tabel_data)
        $bansosMap = [];
        $bansosBuilder = $db->table('dtsen_bansos_kks b')
            ->select('a.id_kk, b.jenis_bansos, b.tahap_salur, b.status_salur, b.status_kelengkapan, b.foto_kpm_kks')
            ->join('dtsen_art a', 'a.nik = CONVERT(b.nik_kpm USING utf8mb3) AND a.deleted_at IS NULL', 'inner', false)
            ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'inner')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'inner')
            ->groupStart()
            ->where('kk.deleted_at', null)
            ->orWhere('kk.deleted_at', '0000-00-00 00:00:00')
            ->groupEnd();

        if ($kodeDesa !== '') {
            $bansosBuilder->where('rt.kode_desa', $kodeDesa);
        }

        $bansosQuery = $bansosBuilder->get()->getResultArray();
        // Ambil tahap salur terbaru PER KK agar penerima aktif pada Tahap 2/3 tidak tereliminasi
        // hanya karena sebagian kecil data Tahap 4 sudah masuk lebih dulu.
        $latestScorePerKk = [];
        $validBansosRows  = [];

        foreach ($bansosQuery as $bq) {
            $statusSalur = strtoupper(trim((string) $bq['status_salur']));
            $isDraftMenungguFoto = ($statusSalur === '')
                && (int) ($bq['status_kelengkapan'] ?? 0) === 0
                && empty($bq['foto_kpm_kks']);

            if ($statusSalur !== 'SUKSES SALUR' && !$isDraftMenungguFoto) {
                continue;
            }

            $kkId = (int) $bq['id_kk'];
            if (!isset($kkSet[$kkId])) {
                continue;
            }

            $score = $this->getTahapScore($bq['tahap_salur']);
            $bq['_score'] = $score;
            $validBansosRows[] = $bq;

            if (!isset($latestScorePerKk[$kkId]) || $score > $latestScorePerKk[$kkId]) {
                $latestScorePerKk[$kkId] = $score;
            }
        }

        foreach ($validBansosRows as $bq) {
            $kkId = (int) $bq['id_kk'];
            if ($bq['_score'] !== ($latestScorePerKk[$kkId] ?? null)) {
                continue;
            }

            if (!isset($bansosMap[$kkId])) {
                $bansosMap[$kkId] = ['jenis' => [], 'tahap' => []];
            }
            $bansosMap[$kkId]['jenis'][] = $bq['jenis_bansos'];
            $bansosMap[$kkId]['tahap'][] = $bq['tahap_salur'];
        }

        // 4️⃣ Smart Merge & Enrich setiap KK
        $dataset = [];
        foreach ($rows as $row) {
            $kkId = (int) $row['id_kk'];

            $rw     = $this->normalizeWilayahCode($row['rw']);
            $rt     = $this->normalizeWilayahCode($row['rt']);
            $alamat = trim((string) ($row['alamat'] ?? ''));

            // Override wilayah/alamat dari usulan terbaru jika tersedia
            if (isset($usulanMap[$kkId])) {
                $pPer = $usulanMap[$kkId]['payload']['perumahan'] ?? [];
                if (!empty($pPer['rw'])) {
                    $rw = $this->normalizeWilayahCode((string) $pPer['rw']);
                }
                if (!empty($pPer['rt'])) {
                    $rt = $this->normalizeWilayahCode((string) $pPer['rt']);
                }
                if (!empty($pPer['alamat'])) {
                    $alamat = trim((string) $pPer['alamat']);
                }
            }

            // Smart Merge Sumber Penerangan: Usulan Terbaru -> Fallback Master RT
            $rawSumber     = '';
            $rawDaya       = '';
            $jmlMeteran    = '';
            $noPelanggan   = '';
            $noMeter       = '';
            $pengListrik   = '';
            $sumberData    = 'Belum Terdata';
            $usulanStatus  = $usulanMap[$kkId]['status'] ?? '';

            if (isset($usulanListrikMap[$kkId])) {
                $uWin    = $usulanListrikMap[$kkId];
                $kond    = $uWin['payload']['perumahan']['kondisi'] ?? [];
                $sePay   = $uWin['payload']['sosial_ekonomi'] ?? [];

                $rawSumber   = trim((string) ($kond['sumber_listrik'] ?? ''));
                $rawDaya     = trim((string) ($kond['daya_listrik'] ?? ''));
                $jmlMeteran  = trim((string) ($kond['jumlah_meteran_listrik'] ?? ''));
                $noPelanggan = trim((string) ($kond['nomor_pelanggan'] ?? ''));
                $noMeter     = trim((string) ($kond['nomor_meter'] ?? ''));
                $pengListrik = trim((string) ($sePay['pengeluaran_listrik'] ?? ''));

                // Fallback detail meteran ke RT jika di usulan kosong tapi kategorinya PLN dengan meteran
                if ($rawDaya === '' && !empty($row['rt_daya_listrik'])) {
                    $rawDaya = trim((string) $row['rt_daya_listrik']);
                }
                if ($noPelanggan === '' && !empty($row['rt_no_pelanggan'])) {
                    $noPelanggan = trim((string) $row['rt_no_pelanggan']);
                }
                if ($noMeter === '' && !empty($row['rt_no_meter'])) {
                    $noMeter = trim((string) $row['rt_no_meter']);
                }
                if ($jmlMeteran === '' && !empty($row['rt_jml_meteran'])) {
                    $jmlMeteran = trim((string) $row['rt_jml_meteran']);
                }

                $sumberData = match ($uWin['status']) {
                    'verified', 'diverifikasi' => 'Usulan Verified',
                    'submitted'                => 'Usulan Submitted',
                    'draft'                    => 'Usulan Draft',
                    default                    => 'Usulan',
                };
            } elseif ($sumberMode !== 'draft_only' && !empty(trim((string) ($row['rt_sumber_listrik'] ?? '')))) {
                $rawSumber   = trim((string) $row['rt_sumber_listrik']);
                $rawDaya     = trim((string) ($row['rt_daya_listrik'] ?? ''));
                $jmlMeteran  = trim((string) ($row['rt_jml_meteran'] ?? ''));
                $noPelanggan = trim((string) ($row['rt_no_pelanggan'] ?? ''));
                $noMeter     = trim((string) ($row['rt_no_meter'] ?? ''));
                $sumberData  = 'Master RT';
            }

            $kategori   = self::normalizeSumberPenerangan($rawSumber);
            $dayaParsed = self::parseDayaListrik($rawDaya, $kategori);

            if ($kategori === 'Belum Terdata') {
                $sumberData = 'Belum Terdata';
            }

            // Desil
            $desilRaw = $row['kategori_desil'];
            $desilInt = ($desilRaw !== null && $desilRaw !== '') ? (int) $desilRaw : null;
            if ($desilInt === null || $desilInt === 0) {
                $desilGroup = 'Belum Ada / Desil 0';
            } elseif ($desilInt >= 1 && $desilInt <= 4) {
                $desilGroup = 'Desil 1–4';
            } else {
                $desilGroup = 'Desil 5–10';
            }

            // Bansos
            $bansos = '-';
            $bansosTahap = '';
            if (isset($bansosMap[$kkId])) {
                $jenisList = array_unique($bansosMap[$kkId]['jenis']);
                if (in_array('PKH', $jenisList, true) && in_array('SEMBAKO', $jenisList, true)) {
                    $bansos = 'PKH + SEMBAKO';
                } elseif (in_array('PKH + SEMBAKO', $jenisList, true)) {
                    $bansos = 'PKH + SEMBAKO';
                } else {
                    $bansos = implode(', ', $jenisList);
                }
                $bansosTahap = implode(', ', array_unique($bansosMap[$kkId]['tahap']));
            }

            $item = [
                'id_kk'               => $kkId,
                'no_kk'               => (string) ($row['no_kk'] ?? ''),
                'kepala_keluarga'     => (string) ($row['kepala_keluarga'] ?? '-'),
                'nik_kepala_keluarga' => (string) ($row['nik_kepala_keluarga'] ?? ''),
                'alamat'              => $alamat !== '' ? $alamat : '-',
                'dusun'               => trim((string) ($row['dusun'] ?? '')),
                'kode_desa'           => (string) ($row['kode_desa'] ?? ''),
                'nama_desa'           => !empty($row['nama_desa']) ? ucwords(strtolower($row['nama_desa'])) : '-',
                'rw'                  => $rw !== '' ? $rw : '-',
                'rt'                  => $rt !== '' ? $rt : '-',
                'sumber_penerangan'   => $kategori,
                'daya_raw'            => $dayaParsed['raw'],
                'daya_items'          => $dayaParsed['items'],
                'daya_group'          => $dayaParsed['primary_group'],
                'jumlah_meteran'      => $jmlMeteran !== '' ? (int) $jmlMeteran : (count($dayaParsed['items']) > 0 ? count($dayaParsed['items']) : 0),
                'nomor_pelanggan'     => $noPelanggan,
                'nomor_meter'         => $noMeter,
                'pengeluaran_listrik' => $pengListrik,
                'sumber_data'         => $sumberData,
                'usulan_status'       => $usulanStatus,
                'desil'               => $desilInt,
                'desil_group'         => $desilGroup,
                'bansos'              => $bansos,
                'bansos_tahap'        => $bansosTahap,
            ];

            if (!$this->matchesFilters($item, $filter)) {
                continue;
            }

            $dataset[] = $item;
        }

        return $dataset;
    }

    private function matchesFilters(array $item, array $filter): bool
    {
        // RW
        $fRw = trim((string) ($filter['rw'] ?? ''));
        if ($fRw !== '' && $fRw !== 'all') {
            if ($item['rw'] !== $this->normalizeWilayahCode($fRw)) {
                return false;
            }
        }

        // RT
        $fRt = trim((string) ($filter['rt'] ?? ''));
        if ($fRt !== '' && $fRt !== 'all') {
            if ($item['rt'] !== $this->normalizeWilayahCode($fRt)) {
                return false;
            }
        }

        // Kategori Sumber Penerangan
        $fKategori = trim((string) ($filter['kategori'] ?? ''));
        if ($fKategori !== '' && $fKategori !== 'all') {
            if ($fKategori === 'non_pln_atau_gelap') {
                if (!in_array($item['sumber_penerangan'], ['Listrik non-PLN', 'Bukan listrik'], true)) {
                    return false;
                }
            } elseif (strcasecmp($item['sumber_penerangan'], $fKategori) !== 0) {
                return false;
            }
        }

        // Filter khusus mode 'draft_only' (hanya tampilkan yg benar-benar berasal dari draft)
        $sumberMode = trim((string) ($filter['sumber_mode'] ?? 'all'));
        if ($sumberMode === 'draft_only' && $item['usulan_status'] !== 'draft') {
            return false;
        }

        // Daya Listrik PLN
        $fDaya = trim((string) ($filter['daya'] ?? ''));
        if ($fDaya !== '' && $fDaya !== 'all') {
            if ($fDaya === 'kosong') {
                if ($item['sumber_penerangan'] !== 'Listrik PLN dengan meteran' || $item['daya_raw'] !== '') {
                    return false;
                }
            } elseif ($fDaya === 'multi') {
                if ($item['jumlah_meteran'] <= 1) {
                    return false;
                }
            } elseif ($fDaya === '1300_plus') {
                if ($item['sumber_penerangan'] !== 'Listrik PLN dengan meteran' || !in_array($item['daya_group'], ['1.300 WATT', '2.200 WATT+'], true)) {
                    return false;
                }
            } elseif (in_array($fDaya, self::DAYA_GROUPS, true)) {
                if ($item['daya_group'] !== $fDaya) {
                    return false;
                }
            } else {
                // Exact match di salah satu item daya (mis: "450 WATT", "> 2.200 WATT")
                $targetUpper = strtoupper($fDaya);
                $matched = false;
                foreach ($item['daya_items'] as $dItem) {
                    if ($dItem === $targetUpper) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    return false;
                }
            }
        }

        // Desil
        $fDesil = trim((string) ($filter['desil'] ?? ''));
        if ($fDesil !== '' && $fDesil !== 'all') {
            $d = $item['desil'];
            if ($fDesil === '1_4') {
                if ($d === null || $d < 1 || $d > 4) {
                    return false;
                }
            } elseif ($fDesil === '5_10') {
                if ($d === null || $d < 5 || $d > 10) {
                    return false;
                }
            } elseif ($fDesil === 'belum') {
                if ($d !== null && $d !== 0) {
                    return false;
                }
            } else {
                if ($d === null || $d !== (int) $fDesil) {
                    return false;
                }
            }
        }

        // Bansos
        $fBansos = trim((string) ($filter['bansos'] ?? ''));
        if ($fBansos !== '' && $fBansos !== 'all') {
            $bUpper = strtoupper($item['bansos']);
            $targetUpper = strtoupper($fBansos);
            if ($targetUpper === 'NONE') {
                if ($bUpper !== '-' && $bUpper !== '') {
                    return false;
                }
            } elseif ($targetUpper === 'PENERIMA') {
                if ($bUpper === '-' || $bUpper === '') {
                    return false;
                }
            } else {
                if ($bUpper !== $targetUpper) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 🔍 Filter search Datatables di atas dataset hasil filter
     */
    public function applySearch(array $dataset, string $search): array
    {
        $s = trim(strtolower($search));
        if ($s === '') {
            return $dataset;
        }

        return array_values(array_filter($dataset, static function ($item) use ($s) {
            $haystack = strtolower(implode(' ', [
                $item['no_kk'],
                $item['kepala_keluarga'],
                $item['nik_kepala_keluarga'],
                $item['alamat'],
                'rw ' . $item['rw'],
                'rt ' . $item['rt'],
                $item['sumber_penerangan'],
                $item['daya_raw'],
                $item['nomor_pelanggan'],
                $item['nomor_meter'],
                $item['bansos'],
                $item['sumber_data'],
            ]));
            return str_contains($haystack, $s);
        }));
    }

    /**
     * 📊 Hitung seluruh ringkasan laporan: KPI, Breakdown Daya, Rekap Wilayah, Korelasi Desil & Bansos
     */
    public function buildAnalytics(array $dataset, array $filter = []): array
    {
        $total = count($dataset);
        $pct = static fn(int $n, int $den = 0): float => ($den > 0) ? round(($n / $den) * 100, 1) : 0.0;

        // 1. KPI Kategori Sumber Penerangan
        $katCounts = array_fill_keys(self::KATEGORI_LIST, 0);
        // 2. Breakdown Daya PLN (hanya untuk Listrik PLN dengan meteran)
        $dayaCounts = array_fill_keys(self::DAYA_GROUPS, 0);
        $totalPlnMeteran = 0;
        $multiMeterCount = 0;

        // 3. Rekap Wilayah (RW, atau RT jika RW tertentu dipilih, atau Desa jika multi-desa tanpa filter desa)
        $selectedDesa = trim((string) ($filter['kode_desa'] ?? ''));
        $selectedRw   = trim((string) ($filter['rw'] ?? ''));

        $distinctDesa = [];
        foreach ($dataset as $it) {
            if ($it['kode_desa'] !== '') {
                $distinctDesa[$it['kode_desa']] = true;
            }
        }

        if (($selectedDesa === '' || $selectedDesa === 'all') && count($distinctDesa) > 1 && ($selectedRw === '' || $selectedRw === 'all')) {
            $wilayahLevel = 'Desa';
        } elseif ($selectedRw !== '' && $selectedRw !== 'all') {
            $wilayahLevel = 'RT';
        } else {
            $wilayahLevel = 'RW';
        }
        $wilayahBuckets = [];

        // 4. Matriks Korelasi Desil
        $desilCols = ['Desil 1–4', 'Desil 5–10', 'Belum Ada / Desil 0'];
        $korelasiDesil = [];
        foreach (self::KATEGORI_LIST as $k) {
            $korelasiDesil[$k] = array_fill_keys($desilCols, 0);
            $korelasiDesil[$k]['Total'] = 0;
        }

        // 5. Matriks Korelasi Bansos
        $bansosCols = ['PKH', 'SEMBAKO', 'PKH + SEMBAKO', 'Non-Bansos'];
        $korelasiBansos = [];
        foreach (self::KATEGORI_LIST as $k) {
            $korelasiBansos[$k] = array_fill_keys($bansosCols, 0);
            $korelasiBansos[$k]['Total Penerima'] = 0;
            $korelasiBansos[$k]['Total'] = 0;
        }

        // Insight khusus prioritas (Desil 1-4 belum PLN meteran mandiri / penerima bansos daya tinggi)
        $prioritasTanpaMeteranDesil14 = 0;
        $prioritasNonPlnAtauGelapDesil14 = 0;
        $bansosDayaTinggi = 0;

        foreach ($dataset as $item) {
            $k = $item['sumber_penerangan'];
            $katCounts[$k] = ($katCounts[$k] ?? 0) + 1;

            // Daya PLN
            if ($k === 'Listrik PLN dengan meteran') {
                $totalPlnMeteran++;
                $dg = $item['daya_group'];
                $dayaCounts[$dg] = ($dayaCounts[$dg] ?? 0) + 1;
                if ((int) $item['jumlah_meteran'] > 1) {
                    $multiMeterCount++;
                }
            }

            // Rekap Wilayah
            if ($wilayahLevel === 'Desa') {
                $wKey   = $item['kode_desa'] ?: '-';
                $wLabel = $item['nama_desa'] !== '-' ? $item['nama_desa'] : ($wKey !== '-' ? $wKey : 'Tanpa Desa');
            } elseif ($wilayahLevel === 'RT') {
                $wKey   = $item['rt'];
                $wLabel = 'RT ' . $item['rt'] . ' / RW ' . $item['rw'];
            } else {
                $wKey   = $item['rw'];
                $wLabel = 'RW ' . $item['rw'];
            }

            if (!isset($wilayahBuckets[$wKey])) {
                $wilayahBuckets[$wKey] = [
                    'key'              => $wKey,
                    'label'            => $wLabel,
                    'total'            => 0,
                    'pln_meteran'      => 0,
                    'pln_tanpa'        => 0,
                    'non_pln'          => 0,
                    'bukan_listrik'    => 0,
                    'belum_terdata'    => 0,
                    'daya_450'         => 0,
                    'daya_900'         => 0,
                    'daya_1300_plus'   => 0,
                    'terdata'          => 0,
                ];
            }

            $wilayahBuckets[$wKey]['total']++;
            if ($k === 'Listrik PLN dengan meteran') {
                $wilayahBuckets[$wKey]['pln_meteran']++;
                $wilayahBuckets[$wKey]['terdata']++;
                if ($item['daya_group'] === '450 WATT') {
                    $wilayahBuckets[$wKey]['daya_450']++;
                } elseif ($item['daya_group'] === '900 WATT') {
                    $wilayahBuckets[$wKey]['daya_900']++;
                } elseif (in_array($item['daya_group'], ['1.300 WATT', '2.200 WATT+'], true)) {
                    $wilayahBuckets[$wKey]['daya_1300_plus']++;
                }
            } elseif ($k === 'Listrik PLN tanpa meteran') {
                $wilayahBuckets[$wKey]['pln_tanpa']++;
                $wilayahBuckets[$wKey]['terdata']++;
            } elseif ($k === 'Listrik non-PLN') {
                $wilayahBuckets[$wKey]['non_pln']++;
                $wilayahBuckets[$wKey]['terdata']++;
            } elseif ($k === 'Bukan listrik') {
                $wilayahBuckets[$wKey]['bukan_listrik']++;
                $wilayahBuckets[$wKey]['terdata']++;
            } else {
                $wilayahBuckets[$wKey]['belum_terdata']++;
            }

            // Korelasi Desil
            $dgLabel = $item['desil_group'];
            $korelasiDesil[$k][$dgLabel]++;
            $korelasiDesil[$k]['Total']++;

            if ($dgLabel === 'Desil 1–4') {
                if ($k === 'Listrik PLN tanpa meteran') {
                    $prioritasTanpaMeteranDesil14++;
                } elseif ($k === 'Listrik non-PLN' || $k === 'Bukan listrik') {
                    $prioritasNonPlnAtauGelapDesil14++;
                }
            }

            // Korelasi Bansos
            $b = $item['bansos'];
            if ($b === 'PKH' || $b === 'SEMBAKO' || $b === 'PKH + SEMBAKO') {
                $korelasiBansos[$k][$b]++;
                $korelasiBansos[$k]['Total Penerima']++;
                if ($k === 'Listrik PLN dengan meteran' && in_array($item['daya_group'], ['1.300 WATT', '2.200 WATT+'], true)) {
                    $bansosDayaTinggi++;
                }
            } else {
                $korelasiBansos[$k]['Non-Bansos']++;
            }
            $korelasiBansos[$k]['Total']++;
        }

        // Format KPI cards
        $summary = [
            'total'          => $total,
            'total_terdata'  => $total - $katCounts['Belum Terdata'],
            'pct_terdata'    => $pct($total - $katCounts['Belum Terdata'], $total),
            'pln_meteran'    => ['label' => 'Listrik PLN dengan meteran', 'count' => $katCounts['Listrik PLN dengan meteran'], 'percent' => $pct($katCounts['Listrik PLN dengan meteran'], $total)],
            'pln_tanpa'      => ['label' => 'Listrik PLN tanpa meteran', 'count' => $katCounts['Listrik PLN tanpa meteran'], 'percent' => $pct($katCounts['Listrik PLN tanpa meteran'], $total)],
            'non_pln'        => ['label' => 'Listrik non-PLN', 'count' => $katCounts['Listrik non-PLN'], 'percent' => $pct($katCounts['Listrik non-PLN'], $total)],
            'bukan_listrik'  => ['label' => 'Bukan listrik', 'count' => $katCounts['Bukan listrik'], 'percent' => $pct($katCounts['Bukan listrik'], $total)],
            'belum_terdata'  => ['label' => 'Belum Terdata', 'count' => $katCounts['Belum Terdata'], 'percent' => $pct($katCounts['Belum Terdata'], $total)],
        ];

        // Format Daya Breakdown
        $dayaBreakdown = [];
        foreach ($dayaCounts as $label => $cnt) {
            $dayaBreakdown[] = [
                'label'   => $label,
                'count'   => $cnt,
                'percent' => $pct($cnt, $totalPlnMeteran),
            ];
        }

        // Urutkan Wilayah Rekap alami
        uksort($wilayahBuckets, static fn($a, $b) => strnatcasecmp((string) $a, (string) $b));
        $wilayahRekap = [];
        foreach ($wilayahBuckets as $w) {
            $w['pct_terdata']     = $pct($w['terdata'], $w['total']);
            $w['pct_pln_meteran'] = $pct($w['pln_meteran'], $w['total']);
            $wilayahRekap[] = $w;
        }

        return [
            'summary'          => $summary,
            'daya_total'       => $totalPlnMeteran,
            'multi_meter_cnt'  => $multiMeterCount,
            'daya_breakdown'   => $dayaBreakdown,
            'wilayah_level'    => $wilayahLevel,
            'wilayah_rekap'    => $wilayahRekap,
            'korelasi_desil'   => $korelasiDesil,
            'korelasi_bansos'  => $korelasiBansos,
            'insights'         => [
                'desil14_tanpa_meteran'    => $prioritasTanpaMeteranDesil14,
                'desil14_non_pln_gelap'    => $prioritasNonPlnAtauGelapDesil14,
                'bansos_daya_1300_plus'    => $bansosDayaTinggi,
            ],
        ];
    }
}
