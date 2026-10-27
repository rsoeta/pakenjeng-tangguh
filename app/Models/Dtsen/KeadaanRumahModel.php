<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class KeadaanRumahModel extends Model
{
    use WilayahFilterTrait;

    protected $table            = 'dtsen_keadaan_rumah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_kk',
        'status_kepemilikan',
        'jenis_lantai',
        'jenis_dinding',
        'jenis_atap',
        'kategori_rumah',
        'sumber_penilaian',
        'catatan',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps    = true;

    /**
     * 🧠 LOGIKA UTAMA KLASIFIKASI KEADAAN RUMAH
     * Menghasilkan: Numpang, Panggung, Permanen, Semi Permanen, atau null (Belum Dinilai)
     */
    public static function determineKategori($statusKepemilikan, $jenisLantai, $jenisDinding, $jenisAtap, $isTinggalBersama = null)
    {
        $statusKepemilikan = trim((string)$statusKepemilikan);
        $jenisLantai       = trim((string)$jenisLantai);
        $jenisDinding      = trim((string)$jenisDinding);
        $jenisAtap         = trim((string)$jenisAtap);

        // 1. Kriteria NUMPANG
        if (
            strcasecmp($statusKepemilikan, 'Bebas Sewa') === 0 ||
            strcasecmp($statusKepemilikan, 'Menumpang') === 0 ||
            strcasecmp($statusKepemilikan, 'Numpang') === 0 ||
            ($isTinggalBersama === 'Ya' && strcasecmp($statusKepemilikan, 'Milik Sendiri') !== 0 && !empty($statusKepemilikan))
        ) {
            return 'Numpang';
        }

        // Jika data fisik belum ada sama sekali dan bukan numpang
        if (empty($jenisLantai) && empty($jenisDinding) && empty($jenisAtap)) {
            return null;
        }

        // 2. Kriteria PANGGUNG
        // Konstruksi bertiang/kolong dengan lantai papan kayu atau bambu (palupuh)
        $lantaiPanggung  = ['Kayu', 'Bambu'];
        $dindingPanggung = ['Bambu', 'Anyaman bambu', 'Kayu/papan/gypsum/GRC/calciboard', 'Batang kayu'];

        if (in_array($jenisLantai, $lantaiPanggung) && (in_array($jenisDinding, $dindingPanggung) || empty($jenisDinding) || $jenisDinding !== 'Tembok')) {
            return 'Panggung';
        }

        // 3. Kriteria PERMANEN
        // Dinding tembok + lantai semen/keramik/marmer/ubin + atap beton/genteng/seng/asbes
        $lantaiPermanen = ['Marmer/granit', 'Keramik', 'Parket/vinil/karpet', 'Ubin/tegal/teraso', 'Semen'];
        $atapPermanen   = ['Beton', 'Genteng', 'Seng', 'Asbes'];

        if (
            $jenisDinding === 'Tembok' &&
            (in_array($jenisLantai, $lantaiPermanen) || empty($jenisLantai)) &&
            (in_array($jenisAtap, $atapPermanen) || empty($jenisAtap))
        ) {
            return 'Permanen';
        }

        // 4. Kriteria SEMI PERMANEN
        // Konstruksi campuran (setengah tembok, plesteran anyaman bambu, atau dinding bilik/kayu tapak semen)
        if (!empty($jenisDinding) || !empty($jenisLantai) || !empty($jenisAtap)) {
            return 'Semi Permanen';
        }

        return null;
    }

    /**
     * 🔍 Query Builder Dasar untuk DataTables & Export
     * Dilengkapi Karantina Wilayah Petugas (role_id >= 4) via WilayahFilterTrait
     */
    private function getBaseQuery(array $filter, int $userRole = 1)
    {
        $roleId = (int)($filter['role_id'] ?? $userRole);

        $builder = $this->db->table('dtsen_kk kk')
            ->select('
                kk.id_kk,
                kk.no_kk,
                kk.kepala_keluarga,
                kk.nik_kepala_keluarga,
                kk.alamat,
                rt.rt,
                rt.rw,
                rt.kode_desa,
                kr.id as id_keadaan,
                COALESCE(kr.status_kepemilikan, rt.kepemilikan_rumah, kk.status_kepemilikan_rumah) as status_kepemilikan,
                kr.jenis_lantai,
                kr.jenis_dinding,
                kr.jenis_atap,
                kr.kategori_rumah,
                kr.sumber_penilaian,
                kr.catatan,
                kr.updated_at as tanggal_penilaian
            ', false)
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_keadaan_rumah kr', 'kr.id_kk = kk.id_kk', 'left')
            ->groupStart()
            ->where('kk.deleted_at', null)
            ->orWhere('kk.deleted_at', '0000-00-00 00:00:00')
            ->groupEnd();

        // 🔐 Terapkan Filter Wilayah Komprehensif (kode_desa, rw, rt, wilayah_tugas, level)
        $this->applyWilayahFilter($builder, $filter, $roleId);

        // Filter Kategori Rumah
        if (!empty($filter['kategori']) && $filter['kategori'] !== 'all') {
            if ($filter['kategori'] === 'belum_dinilai') {
                $builder->where('kr.kategori_rumah IS NULL');
            } else {
                $builder->where('kr.kategori_rumah', $filter['kategori']);
            }
        }

        // Filter Sumber Penilaian
        if (!empty($filter['sumber']) && $filter['sumber'] !== 'all') {
            $builder->where('kr.sumber_penilaian', $filter['sumber']);
        }

        // Filter Search Text (Nama / No KK / Alamat)
        if (!empty($filter['search'])) {
            $s = trim($filter['search']);
            $builder->groupStart()
                ->like('kk.kepala_keluarga', $s)
                ->orLike('kk.no_kk', $s)
                ->orLike('kk.nik_kepala_keluarga', $s)
                ->orLike('kk.alamat', $s)
                ->groupEnd();
        }

        return $builder;
    }

    /**
     * 📊 Ambil Data untuk DataTables Server-Side
     */
    public function getDatatableData(array $filter, int $start = 0, int $length = 10, string $orderCol = 'kk.id_kk', string $orderDir = 'DESC', int $userRole = 1)
    {
        $builder = $this->getBaseQuery($filter, $userRole);

        if ($length > 0) {
            $builder->limit($length, $start);
        }

        $allowedCols = [
            'kk.id_kk'            => 'kk.id_kk',
            'kk.kepala_keluarga'  => 'kk.kepala_keluarga',
            'kk.no_kk'            => 'kk.no_kk',
            'rt.rw'               => 'rt.rw',
            'rt.rt'               => 'rt.rt',
            'kr.kategori_rumah'   => 'kr.kategori_rumah',
            'kr.sumber_penilaian' => 'kr.sumber_penilaian',
        ];

        $col = $allowedCols[$orderCol] ?? 'kk.id_kk';
        $dir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $builder->orderBy($col, $dir);

        return $builder->get()->getResultArray();
    }

    /**
     * 🔢 Hitung Total Filtered Data
     */
    public function countFiltered(array $filter, int $userRole = 1)
    {
        $builder = $this->getBaseQuery($filter, $userRole);
        return $builder->countAllResults();
    }

    /**
     * 🔢 Hitung Total Semua Data Sesuai Wilayah Petugas
     */
    public function countTotal(array $filter, int $userRole = 1)
    {
        $roleId = (int)($filter['role_id'] ?? $userRole);

        $builder = $this->db->table('dtsen_kk kk')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->groupStart()
            ->where('kk.deleted_at', null)
            ->orWhere('kk.deleted_at', '0000-00-00 00:00:00')
            ->groupEnd();

        // 🔐 Terapkan karantina wilayah untuk total record
        $this->applyWilayahFilter($builder, $filter, $roleId);

        return $builder->countAllResults();
    }

    /**
     * 📥 Ambil Semua Data untuk Ekspor Excel
     */
    public function getAllForExport(array $filter, int $userRole = 1)
    {
        $builder = $this->getBaseQuery($filter, $userRole);
        $builder->orderBy('rt.rw', 'ASC')->orderBy('rt.rt', 'ASC')->orderBy('kk.kepala_keluarga', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * 🥧 DATA RINGKASAN PIE GRAPH (5 KATEGORI IDENTIK TEMPLATE)
     */
    public function getGrafikSummary(array $filter, int $userRole = 1)
    {
        $roleId = (int)($filter['role_id'] ?? $userRole);

        $builder = $this->db->table('dtsen_kk kk')
            ->select('
                COUNT(*) as total_kk,
                SUM(CASE WHEN kr.kategori_rumah = "Permanen" THEN 1 ELSE 0 END) as jml_permanen,
                SUM(CASE WHEN kr.kategori_rumah = "Semi Permanen" THEN 1 ELSE 0 END) as jml_semi_permanen,
                SUM(CASE WHEN kr.kategori_rumah = "Panggung" THEN 1 ELSE 0 END) as jml_panggung,
                SUM(CASE WHEN kr.kategori_rumah = "Numpang" THEN 1 ELSE 0 END) as jml_numpang,
                SUM(CASE WHEN kr.kategori_rumah IS NULL THEN 1 ELSE 0 END) as jml_belum_dinilai
            ', false)
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_keadaan_rumah kr', 'kr.id_kk = kk.id_kk', 'left')
            ->groupStart()
            ->where('kk.deleted_at', null)
            ->orWhere('kk.deleted_at', '0000-00-00 00:00:00')
            ->groupEnd();

        // 🔐 Terapkan karantina wilayah untuk grafik summary
        $this->applyWilayahFilter($builder, $filter, $roleId);

        $row = $builder->get()->getRowArray() ?? [];

        $total = (int)($row['total_kk'] ?? 0);
        $p   = (int)($row['jml_permanen'] ?? 0);
        $sp  = (int)($row['jml_semi_permanen'] ?? 0);
        $pg  = (int)($row['jml_panggung'] ?? 0);
        $np  = (int)($row['jml_numpang'] ?? 0);
        $bl  = (int)($row['jml_belum_dinilai'] ?? 0);

        $pct = function ($jml) use ($total) {
            return $total > 0 ? round(($jml / $total) * 100, 1) : 0;
        };

        return [
            'total'          => $total,
            'permanen'       => ['count' => $p,  'percent' => $pct($p)],
            'semi_permanen'  => ['count' => $sp, 'percent' => $pct($sp)],
            'panggung'       => ['count' => $pg, 'percent' => $pct($pg)],
            'numpang'        => ['count' => $np, 'percent' => $pct($np)],
            'belum_dinilai'  => ['count' => $bl, 'percent' => $pct($bl)],
        ];
    }

    /**
     * ⚡ SINKRONISASI / AUTO-DETECT KELAS RUMAH SECARA MASAL DARI USULAN & RT
     */
    public function autoDetectAll($kodeDesa = null)
    {
        $db = $this->db;

        // Ambil usulan terbaru per dtsen_kk_id yang memiliki data perumahan
        $usulans = $db->query("
            SELECT u.dtsen_kk_id, u.payload, kk.id_rt, rt.kepemilikan_rumah, kk.status_kepemilikan_rumah as kk_pemilik
            FROM dtsen_usulan u
            INNER JOIN (
                SELECT dtsen_kk_id, MAX(id) as max_id
                FROM dtsen_usulan
                WHERE dtsen_kk_id IS NOT NULL AND payload LIKE '%perumahan%'
                GROUP BY dtsen_kk_id
            ) latest ON u.id = latest.max_id
            INNER JOIN dtsen_kk kk ON kk.id_kk = u.dtsen_kk_id
            LEFT JOIN dtsen_rt rt ON rt.id_rt = kk.id_rt
            WHERE kk.deleted_at IS NULL
        ")->getResultArray();

        $countUpdated = 0;

        foreach ($usulans as $u) {
            $idKk = $u['dtsen_kk_id'];
            $payload = json_decode($u['payload'] ?? '{}', true);
            if (!is_array($payload)) continue;

            $perumahan = $payload['perumahan'] ?? [];
            $kondisi   = $perumahan['kondisi'] ?? [];

            $statusKepemilikan =
                $kondisi['status_kepemilikan'] ??
                $perumahan['status_kepemilikan'] ??
                $perumahan['kepemilikan_rumah'] ??
                $u['kepemilikan_rumah'] ??
                $u['kk_pemilik'] ??
                '';

            $jenisLantai  = $kondisi['jenis_lantai'] ?? '';
            $jenisDinding = $kondisi['jenis_dinding'] ?? '';
            $jenisAtap    = $kondisi['jenis_atap'] ?? '';
            $isTinggal    = $kondisi['is_tinggal_bersama'] ?? '';

            $kategori = self::determineKategori($statusKepemilikan, $jenisLantai, $jenisDinding, $jenisAtap, $isTinggal);

            if ($kategori) {
                // Upsert ke dtsen_keadaan_rumah jika belum manual
                $existing = $db->table('dtsen_keadaan_rumah')->where('id_kk', $idKk)->get()->getRowArray();
                if (!$existing) {
                    $db->table('dtsen_keadaan_rumah')->insert([
                        'id_kk'              => $idKk,
                        'status_kepemilikan' => $statusKepemilikan ?: null,
                        'jenis_lantai'       => $jenisLantai ?: null,
                        'jenis_dinding'      => $jenisDinding ?: null,
                        'jenis_atap'         => $jenisAtap ?: null,
                        'kategori_rumah'     => $kategori,
                        'sumber_penilaian'   => 'otomatis',
                        'catatan'            => 'Auto-detect sistem dari Tab Rumah',
                        'created_by'         => session()->get('nik') ?? 'system',
                        'updated_by'         => session()->get('nik') ?? 'system',
                        'created_at'         => date('Y-m-d H:i:s'),
                        'updated_at'         => date('Y-m-d H:i:s')
                    ]);
                    $countUpdated++;
                } elseif ($existing['sumber_penilaian'] === 'otomatis') {
                    $db->table('dtsen_keadaan_rumah')->where('id_kk', $idKk)->update([
                        'status_kepemilikan' => $statusKepemilikan ?: $existing['status_kepemilikan'],
                        'jenis_lantai'       => $jenisLantai ?: $existing['jenis_lantai'],
                        'jenis_dinding'      => $jenisDinding ?: $existing['jenis_dinding'],
                        'jenis_atap'         => $jenisAtap ?: $existing['jenis_atap'],
                        'kategori_rumah'     => $kategori,
                        'updated_by'         => session()->get('nik') ?? 'system',
                        'updated_at'         => date('Y-m-d H:i:s')
                    ]);
                    $countUpdated++;
                }
            }
        }

        return $countUpdated;
    }
}
