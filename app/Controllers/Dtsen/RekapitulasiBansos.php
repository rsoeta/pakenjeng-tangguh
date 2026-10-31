<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Traits\WilayahFilterTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapitulasiBansos extends BaseController
{
    use WilayahFilterTrait;

    protected $db;
    protected $authModel;
    protected $effectiveCategoryCache = [];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->authModel = new AuthModel();
    }

    /**
     * Mengambil data master untuk filter dropdown (Petugas & Tahap Salur)
     */
    protected function getFilterMasterData(): array
    {
        $user = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? ($user['role_id'] ?? 4);
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        // 🚀 Ambil daftar petugas entri yang aktif di desa ini
        $petugasList = $this->db->table('dtks_users')
            ->select('id, fullname, wilayah_tugas')
            ->where('kode_desa', $kodeDesa)
            ->where('role_id >=', 4)
            ->where('status', 1)
            ->orderBy('fullname', 'ASC')
            ->get()->getResultArray();

        // 🚀 Ambil daftar tahap salur yang tersedia dari database
        $tahapListQuery = $this->db->table('dtsen_bansos_kks')
            ->select('tahap_salur')
            ->distinct()
            ->where('tahap_salur IS NOT NULL')
            ->where('tahap_salur !=', '')
            ->orderBy('tahap_salur', 'DESC')
            ->get()->getResultArray();

        $tahapList = array_filter(array_column($tahapListQuery, 'tahap_salur'));

        // Tahap standar tahun berjalan dan sebelumnya jika belum ada di DB
        $currentYear = date('Y');
        $currentTahapNum = ceil(date('n') / 3);
        $defaultTahap = "Tahap {$currentTahapNum} Tahun {$currentYear}";

        $standarTahap = [];
        foreach ([$currentYear, $currentYear - 1] as $y) {
            $maxTahap = ($y == $currentYear) ? $currentTahapNum : 4;
            for ($t = $maxTahap; $t >= 1; $t--) {
                $standarTahap[] = "Tahap $t Tahun $y";
            }
        }
        $allTahapList = array_values(array_unique(array_merge($tahapList, $standarTahap)));

        // Resolusi Kode Desa & Nama Desa untuk Batas Wilayah Peta
        $kodeDesaNorm = trim((string)$kodeDesa);
        if (strlen($kodeDesaNorm) === 10 && strpos($kodeDesaNorm, '.') === false) {
            // Format 3205332006 -> 32.05.33.2006
            $kodeDesaNorm = substr($kodeDesaNorm, 0, 2) . '.' . substr($kodeDesaNorm, 2, 2) . '.' . substr($kodeDesaNorm, 4, 2) . '.' . substr($kodeDesaNorm, 6, 4);
        }

        if (empty($kodeDesaNorm)) {
            // Fallback jika superadmin: ambil kode_desa dominan dari penerima bansos
            $domDesa = $this->db->table('dtsen_bansos_kks b')
                ->select('rt.kode_desa, COUNT(*) as cnt')
                ->join('dtsen_art a', 'a.nik = b.nik_kpm AND a.deleted_at IS NULL', 'inner')
                ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'inner')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'inner')
                ->where('rt.kode_desa IS NOT NULL')
                ->where('rt.kode_desa !=', '')
                ->groupBy('rt.kode_desa')
                ->orderBy('cnt', 'DESC')
                ->limit(1)
                ->get()->getRowArray();
            $kodeDesaNorm = $domDesa['kode_desa'] ?? '32.05.33.2006';
        }

        $vilRow = $this->db->table('tb_villages')->select('name')->where('id', $kodeDesaNorm)->get()->getRowArray();
        $namaDesa = $vilRow['name'] ?? 'PASIRLANGU';

        return [
            'user'          => session()->get(),
            'role_id'       => $roleId,
            'kode_desa'     => $kodeDesaNorm,
            'nama_desa'     => $namaDesa,
            'petugas_list'  => $petugasList,
            'tahap_list'    => $allTahapList,
            'default_tahap' => $defaultTahap
        ];
    }

    /**
     * Validasi apakah koordinat latitude & longitude valid
     */
    protected function isValidCoord($lat, $lng): bool
    {
        if ($lat === null || $lng === null) return false;
        $latStr = trim((string)$lat);
        $lngStr = trim((string)$lng);
        if ($latStr === '' || $lngStr === '' || !is_numeric($latStr) || !is_numeric($lngStr)) {
            return false;
        }
        $latVal = (float)$latStr;
        $lngVal = (float)$lngStr;
        if ($latVal == 0.0 || $lngVal == 0.0) {
            return false;
        }
        return ($latVal >= -90.0 && $latVal <= 90.0 && $lngVal >= -180.0 && $lngVal <= 180.0);
    }

    /**
     * Halaman Utama Rekapitulasi Bansos
     */
    public function index()
    {
        $data = $this->getFilterMasterData();
        $data['title'] = 'Rekapitulasi Bantuan Sosial';

        return view('dtsen/bansos_kks/v_rekapitulasi', $data);
    }

    /**
     * Halaman Mapping Sebaran Bansos di Atas Peta Satelit Desa
     */
    public function mapping()
    {
        $data = $this->getFilterMasterData();
        $data['title'] = 'Mapping Sebaran Bantuan Sosial';

        return view('dtsen/bansos_kks/v_mapping_bansos', $data);
    }

    /**
     * Mengambil dan mengelompokkan data penerima bansos (1 KPM / 1 KK per tahap salur = 1 rekaman)
     * Dilengkapi resolusi koordinat:
     * - Prioritas 1: Koordinat rumah dari dtsen_kk (melalui dtsen_rt / dtsen_usulan)
     * - Prioritas 2: Fallback koordinat dari dtsen_bansos_kks
     *
     * @param array $filters
     * @return array
     */
    protected function getGroupedRecipients(array $filters): array
    {
        $user = $this->authModel->getUserId();
        $roleId = session()->get('role_id') ?? ($user['role_id'] ?? 4);
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        $filterPetugas = $filters['filter_petugas'] ?? null;
        $filterRw      = $filters['filter_rw'] ?? null;
        $filterRt      = $filters['filter_rt'] ?? null;
        $filterTahap   = $filters['filter_tahap'] ?? null;
        $filterJenis   = $filters['filter_jenis'] ?? null;
        $filterStatus  = $filters['filter_status'] ?? null;

        $builder = $this->db->table('dtsen_bansos_kks b')
            ->select('b.id, b.nik_kpm, 
                      COALESCE(NULLIF(b.nama_kpm, \'\'), NULLIF(m.nama_penerima, \'\'), a.nama, \'-\') as nama_kpm,
                      b.jenis_bansos, b.nomor_kks, b.tahap_salur, b.nominal_cair, 
                      b.status_salur, b.status_kelengkapan, b.foto_kpm_kks, b.foto_bukti_transaksi, b.latitude, b.longitude, 
                      b.created_by, b.created_at, b.updated_at,
                      MAX(k.id_kk) as id_kk, MAX(k.no_kk) as no_kk, MAX(k.kepala_keluarga) as kepala_keluarga,
                      MAX(rt.latitude) as rt_lat, MAX(rt.longitude) as rt_lng,
                      COALESCE(NULLIF(m.alamat, \'\'), MAX(rt.alamat), MAX(k.alamat), \'-\') as alamat,
                      COALESCE(NULLIF(m.rw, \'\'), MAX(rt.rw), \'-\') as rw,
                      COALESCE(NULLIF(m.rt, \'\'), MAX(rt.rt), \'-\') as rt,
                      u.fullname as petugas_nama', false)
            ->join('dtsen_master_kks m', 'm.nik = b.nik_kpm', 'left')
            ->join('dtsen_art a', 'a.nik = b.nik_kpm AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            ->join('dtks_users u', 'u.id = b.created_by', 'left');

        // Terapkan wilayah filter trait
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');
        $roleTarget = $roleId;

        if ($roleId <= 3 && !empty($filterPetugas)) {
            $petugas = $this->db->table('dtks_users')->select('wilayah_tugas')->where('id', $filterPetugas)->get()->getRowArray();
            if ($petugas && !empty($petugas['wilayah_tugas'])) {
                $wilayahTugas = trim($petugas['wilayah_tugas']);
                $roleTarget = 4;
            }
        }

        $filterData = [
            'kode_desa'     => $kodeDesa,
            'wilayah_tugas' => $wilayahTugas
        ];
        $this->applyWilayahFilter($builder, $filterData, $roleTarget);

        // Filter Petugas jika dipilih oleh Admin namun wilayah tugas kosong (fallback ke created_by)
        if ($roleId <= 3 && !empty($filterPetugas)) {
            $petugas = $this->db->table('dtks_users')->select('wilayah_tugas')->where('id', $filterPetugas)->get()->getRowArray();
            if (!$petugas || empty($petugas['wilayah_tugas'])) {
                $builder->where('b.created_by', (int)$filterPetugas);
            }
        }

        // Filter RW & RT dinamis
        if (!empty($filterRw)) {
            $rwPad = str_pad($filterRw, 3, '0', STR_PAD_LEFT);
            $builder->groupStart()
                ->where('m.rw', $rwPad)
                ->orWhere('rt.rw', $rwPad)
            ->groupEnd();
        }
        if (!empty($filterRt)) {
            $rtPad = str_pad($filterRt, 3, '0', STR_PAD_LEFT);
            $builder->groupStart()
                ->where('m.rt', $rtPad)
                ->orWhere('rt.rt', $rtPad)
            ->groupEnd();
        }

        // Filter Tahap Salur
        if (!empty($filterTahap)) {
            $builder->where('b.tahap_salur', $filterTahap);
        }

        $builder->groupBy('b.id');
        $rawRows = $builder->get()->getResultArray();

        // 🚀 RESOLUSI RELASI KELUARGA (dtsen_kk) & KOORDINAT RUMAH (BATCH CEPAT)
        $unmatchedNiks = [];
        foreach ($rawRows as $r) {
            if (empty($r['id_kk']) && !empty($r['nik_kpm'])) {
                $unmatchedNiks[] = trim((string)$r['nik_kpm']);
            }
        }
        $unmatchedNiks = array_values(array_unique($unmatchedNiks));

        $nikFallbackMap = [];
        if (!empty($unmatchedNiks)) {
            // 1. Cek di dtsen_kk.nik_kepala_keluarga
            $kkByNik = $this->db->table('dtsen_kk k')
                ->select('k.nik_kepala_keluarga, k.id_kk, k.no_kk, k.kepala_keluarga, rt.latitude as rt_lat, rt.longitude as rt_lng')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->whereIn('k.nik_kepala_keluarga', $unmatchedNiks)
                ->where('k.deleted_at', null)
                ->get()->getResultArray();
            foreach ($kkByNik as $kr) {
                $nikKey = trim((string)$kr['nik_kepala_keluarga']);
                $nikFallbackMap[$nikKey] = [
                    'id_kk'           => (int)$kr['id_kk'],
                    'no_kk'           => $kr['no_kk'],
                    'kepala_keluarga' => $kr['kepala_keluarga'],
                    'rt_lat'          => $kr['rt_lat'],
                    'rt_lng'          => $kr['rt_lng'],
                ];
            }

            // 2. Cek di dtsen_usulan_art (jika anggota keluarga ada di draft/usulan pembaruan)
            $uaRows = $this->db->table('dtsen_usulan_art ua')
                ->select("ua.nik, u.dtsen_kk_id, u.no_kk_target, k.no_kk, k.kepala_keluarga, rt.latitude as rt_lat, rt.longitude as rt_lng, JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.geo.lat')) as u_lat, JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.geo.lng')) as u_lng", false)
                ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'inner')
                ->join('dtsen_kk k', 'k.id_kk = u.dtsen_kk_id AND k.deleted_at IS NULL', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->whereIn('ua.nik', $unmatchedNiks)
                ->where('ua.deleted_at', null)
                ->whereIn('u.status', ['draft', 'submitted', 'verified', 'diverifikasi'])
                ->orderBy('u.id', 'ASC')
                ->get()->getResultArray();
            foreach ($uaRows as $uar) {
                $nikKey = trim((string)$uar['nik']);
                $nikFallbackMap[$nikKey] = [
                    'id_kk'           => !empty($uar['dtsen_kk_id']) ? (int)$uar['dtsen_kk_id'] : ($nikFallbackMap[$nikKey]['id_kk'] ?? null),
                    'no_kk'           => !empty($uar['no_kk']) ? $uar['no_kk'] : (!empty($uar['no_kk_target']) ? $uar['no_kk_target'] : ($nikFallbackMap[$nikKey]['no_kk'] ?? null)),
                    'kepala_keluarga' => $uar['kepala_keluarga'] ?? ($nikFallbackMap[$nikKey]['kepala_keluarga'] ?? null),
                    'rt_lat'          => $uar['rt_lat'] ?? ($nikFallbackMap[$nikKey]['rt_lat'] ?? null),
                    'rt_lng'          => $uar['rt_lng'] ?? ($nikFallbackMap[$nikKey]['rt_lng'] ?? null),
                    'u_lat'           => $uar['u_lat'] ?? null,
                    'u_lng'           => $uar['u_lng'] ?? null,
                ];
            }
        }

        $allIdKks = [];
        foreach ($rawRows as &$r) {
            $nikKey = trim((string)$r['nik_kpm']);
            if (empty($r['id_kk']) && isset($nikFallbackMap[$nikKey])) {
                $fb = $nikFallbackMap[$nikKey];
                $r['id_kk']           = $fb['id_kk'] ?? null;
                $r['no_kk']           = !empty($r['no_kk']) ? $r['no_kk'] : ($fb['no_kk'] ?? null);
                $r['kepala_keluarga'] = !empty($r['kepala_keluarga']) ? $r['kepala_keluarga'] : ($fb['kepala_keluarga'] ?? null);
                $r['rt_lat']          = !empty($r['rt_lat']) ? $r['rt_lat'] : ($fb['rt_lat'] ?? null);
                $r['rt_lng']          = !empty($r['rt_lng']) ? $r['rt_lng'] : ($fb['rt_lng'] ?? null);
                $r['u_lat']           = $fb['u_lat'] ?? null;
                $r['u_lng']           = $fb['u_lng'] ?? null;
            }
            if (!empty($r['id_kk'])) {
                $allIdKks[] = (int)$r['id_kk'];
            }
        }
        unset($r);

        // Batch query koordinat rumah terbaru dari dtsen_usulan per id_kk
        $kkGeoMap = [];
        $allIdKks = array_values(array_unique($allIdKks));
        if (!empty($allIdKks)) {
            $usulanGeos = $this->db->table('dtsen_usulan')
                ->select("dtsen_kk_id, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.geo.lat')) as u_lat, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.geo.lng')) as u_lng", false)
                ->whereIn('dtsen_kk_id', $allIdKks)
                ->whereIn('status', ['draft', 'submitted', 'verified', 'diverifikasi'])
                ->orderBy('id', 'ASC')
                ->get()->getResultArray();
            foreach ($usulanGeos as $ug) {
                $kkId = (int)$ug['dtsen_kk_id'];
                if ($this->isValidCoord($ug['u_lat'] ?? null, $ug['u_lng'] ?? null)) {
                    $kkGeoMap[$kkId] = [
                        'lat' => (float)$ug['u_lat'],
                        'lng' => (float)$ug['u_lng']
                    ];
                }
            }
        }

        // Pengelompokan 1 KPM / 1 KK per tahap salur
        $grouped = [];
        foreach ($rawRows as $r) {
            $tahap = trim((string)$r['tahap_salur']);
            $noKk = trim((string)($r['no_kk'] ?? ''));
            if ($noKk === '-' || $noKk === '0') {
                $noKk = '';
            }
            $famKey = !empty($noKk) ? $noKk : ('NIK_' . trim((string)$r['nik_kpm']));
            $groupKey = $tahap . '___' . $famKey;

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'id'              => (int)$r['id'],
                    'ids'             => [],
                    'id_kk'           => !empty($r['id_kk']) ? (int)$r['id_kk'] : null,
                    'nik_kpm'         => $r['nik_kpm'],
                    'nama_kpm'        => $r['nama_kpm'],
                    'no_kk'           => $r['no_kk'],
                    'kepala_keluarga' => $r['kepala_keluarga'],
                    'alamat'          => $r['alamat'],
                    'rw'              => $r['rw'],
                    'rt'              => $r['rt'],
                    'nomor_kks'       => $r['nomor_kks'],
                    'tahap_salur'     => $r['tahap_salur'],
                    'programs'        => [],
                    'nominal_cair'    => 0,
                    'nominal_sukses'  => 0,
                    'status_summary'  => 'belum',
                    'status_label'    => 'Belum Transaksi',
                    'petugas_nama'    => $r['petugas_nama'],
                    'created_at'      => $r['created_at'],
                    'updated_at'      => $r['updated_at'],
                    'rt_lat'          => $r['rt_lat'] ?? null,
                    'rt_lng'          => $r['rt_lng'] ?? null,
                    'u_lat'           => $r['u_lat'] ?? null,
                    'u_lng'           => $r['u_lng'] ?? null,
                    'entries'         => []
                ];
            } else {
                if (empty($grouped[$groupKey]['id_kk']) && !empty($r['id_kk'])) {
                    $grouped[$groupKey]['id_kk'] = (int)$r['id_kk'];
                }
                if (empty($grouped[$groupKey]['rt_lat']) && !empty($r['rt_lat'])) {
                    $grouped[$groupKey]['rt_lat'] = $r['rt_lat'];
                    $grouped[$groupKey]['rt_lng'] = $r['rt_lng'];
                }
            }

            $jb = strtoupper(trim((string)$r['jenis_bansos']));
            $grouped[$groupKey]['ids'][] = (int)$r['id'];
            $grouped[$groupKey]['programs'][] = $jb;
            $grouped[$groupKey]['nominal_cair'] += (int)$r['nominal_cair'];
            $grouped[$groupKey]['entries'][] = $r;

            // Evaluasi nominal sukses salur
            $stAsli = trim((string)($r['status_salur'] ?? ''));
            $stLower = strtolower($stAsli);
            if ($stLower === 'sukses salur' || (empty($stAsli) && (!empty($r['foto_kpm_kks']) || (int)($r['status_kelengkapan'] ?? 0) === 1))) {
                $grouped[$groupKey]['nominal_sukses'] += (int)$r['nominal_cair'];
            }
        }

        // Tentukan Jenis Bansos Efektif, Status Penyaluran, dan Titik Koordinat (Prioritas 1 -> Prioritas 2)
        $result = [];
        $filterJenisUpper = !empty($filterJenis) ? strtoupper(trim($filterJenis)) : null;
        $filterStatusLower = !empty($filterStatus) ? strtolower(trim($filterStatus)) : null;

        foreach ($grouped as $k => $g) {
            $hasPkh = in_array('PKH', $g['programs']);
            $hasSembako = in_array('SEMBAKO', $g['programs']);
            $hasCombo = in_array('PKH + SEMBAKO', $g['programs']);

            if (($hasPkh && $hasSembako) || $hasCombo) {
                $g['jenis_bansos'] = 'PKH + SEMBAKO';
            } elseif ($hasPkh) {
                $g['jenis_bansos'] = 'PKH';
            } else {
                $g['jenis_bansos'] = 'SEMBAKO';
            }

            // Evaluasi Status Penyaluran Gabungan
            $suksesCount = 0;
            $gagalCount = 0;
            $gagalLabel = '';
            $fotoKpmThumb = null;

            foreach ($g['entries'] as $e) {
                $st = trim((string)($e['status_salur'] ?? ''));
                $stLow = strtolower($st);
                if ($stLow === 'sukses salur' || (empty($st) && (!empty($e['foto_kpm_kks']) || (int)($e['status_kelengkapan'] ?? 0) === 1))) {
                    $suksesCount++;
                } elseif (!empty($st)) {
                    $gagalCount++;
                    $gagalLabel = $st;
                }
                if ($fotoKpmThumb === null && !empty($e['foto_kpm_kks'])) {
                    $fotoKpmThumb = $e['foto_kpm_kks'];
                }
            }
            $g['foto_kpm_kks'] = $fotoKpmThumb;

            if ($gagalCount > 0) {
                $g['status_summary'] = 'gagal';
                $g['status_label'] = !empty($gagalLabel) ? $gagalLabel : 'Gagal Salur';
            } elseif ($suksesCount > 0) {
                $g['status_summary'] = 'sukses';
                $g['status_label'] = 'Sukses Salur';
            } else {
                $g['status_summary'] = 'belum';
                $g['status_label'] = 'Belum Transaksi';
            }

            // Filter Jenis Bansos
            if ($filterJenisUpper !== null && $g['jenis_bansos'] !== $filterJenisUpper) {
                continue;
            }

            // Filter Status Penyaluran (Opsional, digunakan di Mapping)
            if ($filterStatusLower !== null && $g['status_summary'] !== $filterStatusLower) {
                continue;
            }

            // =========================================================================
            // 🌍 RESOLUSI KOORDINAT PETA
            // Prioritas 1: Koordinat Rumah dari tabel dtsen_kk (relasi dtsen_rt / dtsen_usulan)
            // Prioritas 2: Fallback latitude & longitude dari tabel dtsen_bansos_kks
            // =========================================================================
            $idKk = !empty($g['id_kk']) ? (int)$g['id_kk'] : null;
            $rumahLat = null;
            $rumahLng = null;

            if ($idKk && isset($kkGeoMap[$idKk])) {
                $rumahLat = $kkGeoMap[$idKk]['lat'];
                $rumahLng = $kkGeoMap[$idKk]['lng'];
            } elseif ($this->isValidCoord($g['rt_lat'] ?? null, $g['rt_lng'] ?? null)) {
                $rumahLat = (float)$g['rt_lat'];
                $rumahLng = (float)$g['rt_lng'];
            } elseif ($this->isValidCoord($g['u_lat'] ?? null, $g['u_lng'] ?? null)) {
                $rumahLat = (float)$g['u_lat'];
                $rumahLng = (float)$g['u_lng'];
            }

            if ($this->isValidCoord($rumahLat, $rumahLng)) {
                $g['latitude']           = (float)$rumahLat;
                $g['longitude']          = (float)$rumahLng;
                $g['coord_source']       = 'rumah_kk';
                $g['coord_source_label'] = 'Titik Lokasi Rumah Warga';
            } else {
                // Fallback ke koordinat dokumentasi di dtsen_bansos_kks
                $bLat = null;
                $bLng = null;
                foreach ($g['entries'] as $e) {
                    if ($this->isValidCoord($e['latitude'] ?? null, $e['longitude'] ?? null)) {
                        $bLat = (float)$e['latitude'];
                        $bLng = (float)$e['longitude'];
                        break;
                    }
                }

                if ($this->isValidCoord($bLat, $bLng)) {
                    $g['latitude']           = $bLat;
                    $g['longitude']          = $bLng;
                    $g['coord_source']       = 'bansos_kks';
                    $g['coord_source_label'] = 'Titik Lokasi Dokumentasi Salur';
                } else {
                    $g['latitude']           = null;
                    $g['longitude']          = null;
                    $g['coord_source']       = null;
                    $g['coord_source_label'] = 'Belum Memiliki Titik Lokasi';
                }
            }

            $result[] = $g;
        }

        return $result;
    }

    /**
     * Data Donut Chart dan Statistik KPI (AJAX POST)
     */
    public function chartData()
    {
        try {
            $filters = [
                'filter_petugas' => $this->request->getPost('filter_petugas'),
                'filter_rw'      => $this->request->getPost('filter_rw'),
                'filter_rt'      => $this->request->getPost('filter_rt'),
                'filter_tahap'   => $this->request->getPost('filter_tahap'),
                'filter_jenis'   => $this->request->getPost('filter_jenis')
            ];

            $groups = $this->getGroupedRecipients($filters);
            $totalKpm = count($groups);

            $countSukses = 0;
            $countGagal = 0;
            $countBelum = 0;
            $totalNominalSukses = 0;

            $countPkh = 0;
            $countSembako = 0;
            $countPkhSembako = 0;

            foreach ($groups as $g) {
                $totalNominalSukses += (int)($g['nominal_sukses'] ?? 0);

                // Hitung Jenis Bansos (1 KK = 1 nilai)
                if ($g['jenis_bansos'] === 'PKH') {
                    $countPkh++;
                } elseif ($g['jenis_bansos'] === 'SEMBAKO') {
                    $countSembako++;
                } elseif ($g['jenis_bansos'] === 'PKH + SEMBAKO') {
                    $countPkhSembako++;
                }

                // Status Realisasi Salur
                if ($g['status_summary'] === 'sukses') {
                    $countSukses++;
                } elseif ($g['status_summary'] === 'gagal') {
                    $countGagal++;
                } else {
                    $countBelum++;
                }
            }

            $persenSukses = $totalKpm > 0 ? round(($countSukses / $totalKpm) * 100, 1) : 0;
            $persenBelum  = $totalKpm > 0 ? round(($countBelum / $totalKpm) * 100, 1) : 0;
            $persenGagal  = $totalKpm > 0 ? round(($countGagal / $totalKpm) * 100, 1) : 0;

            // Donut 1: Status Penyaluran
            $donutStatus = [
                [
                    'name'  => 'Sukses Salur',
                    'y'     => $countSukses,
                    'color' => '#28a745' // Hijau
                ],
                [
                    'name'  => 'Belum Salur / Draft',
                    'y'     => $countBelum,
                    'color' => '#ffc107' // Kuning
                ],
                [
                    'name'  => 'Gagal Salur / Kendala',
                    'y'     => $countGagal,
                    'color' => '#dc3545' // Merah
                ]
            ];

            // Donut 2: Komposisi Jenis Bansos
            $donutJenis = [
                [
                    'name'  => 'PKH',
                    'y'     => $countPkh,
                    'color' => '#007bff' // Biru
                ],
                [
                    'name'  => 'SEMBAKO',
                    'y'     => $countSembako,
                    'color' => '#20c997' // Teal
                ],
                [
                    'name'  => 'PKH + SEMBAKO',
                    'y'     => $countPkhSembako,
                    'color' => '#6f42c1' // Ungu
                ]
            ];

            return $this->response->setJSON([
                'status'         => true,
                'total_kpm'      => $totalKpm,
                'sukses_salur'   => $countSukses,
                'persen_sukses'  => $persenSukses,
                'belum_salur'    => $countBelum,
                'persen_belum'   => $persenBelum,
                'gagal_salur'    => $countGagal,
                'persen_gagal'   => $persenGagal,
                'nominal_sukses' => 'Rp ' . number_format($totalNominalSukses, 0, ',', '.'),
                'nominal_raw'    => $totalNominalSukses,
                'donut_status'   => $donutStatus,
                'donut_jenis'    => $donutJenis
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Server-side DataTables Handler untuk BNBA (AJAX POST)
     */
    public function datatableBnba()
    {
        $request = \Config\Services::request();

        $draw   = (int)$request->getPost('draw');
        $start  = (int)($request->getPost('start') ?? 0);
        $length = (int)($request->getPost('length') ?? 10);
        $search = strtolower(trim($request->getPost('search')['value'] ?? ''));

        $filters = [
            'filter_petugas' => $request->getPost('filter_petugas'),
            'filter_rw'      => $request->getPost('filter_rw'),
            'filter_rt'      => $request->getPost('filter_rt'),
            'filter_tahap'   => $request->getPost('filter_tahap'),
            'filter_jenis'   => $request->getPost('filter_jenis')
        ];

        // 1. Ambil data yang sudah dikelompokkan (1 KPM / 1 KK per tahap = 1 baris)
        $groups = $this->getGroupedRecipients($filters);
        $totalRecords = count($groups);

        // 2. Filter Pencarian DataTables
        if (!empty($search)) {
            $filteredGroups = array_values(array_filter($groups, function ($item) use ($search) {
                return (strpos(strtolower((string)$item['nama_kpm']), $search) !== false) ||
                       (strpos(strtolower((string)$item['nik_kpm']), $search) !== false) ||
                       (strpos(strtolower((string)$item['no_kk']), $search) !== false) ||
                       (strpos(strtolower((string)$item['nomor_kks']), $search) !== false) ||
                       (strpos(strtolower((string)$item['alamat']), $search) !== false) ||
                       (strpos(strtolower((string)$item['petugas_nama']), $search) !== false);
            }));
        } else {
            $filteredGroups = $groups;
        }

        $recordsFiltered = count($filteredGroups);

        // 3. Sorting Dinamis
        $orderColIdx = (int)($request->getPost('order')[0]['column'] ?? 2);
        $orderDir    = strtolower($request->getPost('order')[0]['dir'] ?? 'asc');

        usort($filteredGroups, function ($a, $b) use ($orderColIdx, $orderDir) {
            $valA = '';
            $valB = '';
            switch ($orderColIdx) {
                case 1: $valA = $a['nik_kpm']; $valB = $b['nik_kpm']; break;
                case 2: $valA = $a['nama_kpm']; $valB = $b['nama_kpm']; break;
                case 3: $valA = $a['no_kk']; $valB = $b['no_kk']; break;
                case 4: $valA = $a['alamat']; $valB = $b['alamat']; break;
                case 5: $valA = $a['nomor_kks']; $valB = $b['nomor_kks']; break;
                case 6: $valA = $a['jenis_bansos']; $valB = $b['jenis_bansos']; break;
                case 7: $valA = $a['tahap_salur']; $valB = $b['tahap_salur']; break;
                case 8: $valA = (int)$a['nominal_cair']; $valB = (int)$b['nominal_cair']; break;
                case 9: $valA = $a['status_label']; $valB = $b['status_label']; break;
                case 10: $valA = $a['petugas_nama']; $valB = $b['petugas_nama']; break;
                default: $valA = $a['nama_kpm']; $valB = $b['nama_kpm']; break;
            }

            if ($orderColIdx === 8) {
                return ($orderDir === 'asc') ? ($valA <=> $valB) : ($valB <=> $valA);
            }
            return ($orderDir === 'asc') ? strnatcasecmp((string)$valA, (string)$valB) : strnatcasecmp((string)$valB, (string)$valA);
        });

        // 4. Pagination
        if ($length > 0) {
            $pagedGroups = array_slice($filteredGroups, $start, $length);
        } else {
            $pagedGroups = $filteredGroups;
        }

        // 5. Helper Masking NIK / No KK
        $maskNumber = function ($number, $type) {
            $number = trim((string)$number);
            if (empty($number) || $number === '-' || $number === 'NOKKS') return esc($number);

            $full = esc($number);
            $len = strlen($full);
            $btnClass = ($type === 'nik') ? 'btnCopyNik' : 'btnCopyNoKK';
            $btnTitle = ($type === 'nik') ? 'Salin NIK' : 'Salin No KK';

            if ($len <= 8) {
                $masked = $full;
                $hoverEffect = '';
            } else {
                $masked = substr($full, 0, 8) . str_repeat('*', $len - 8);
                $hoverEffect = ' onmouseenter="this.innerText=\'' . $full . '\'" onmouseleave="this.innerText=\'' . $masked . '\'" ontouchstart="this.innerText=\'' . $full . '\'" ontouchend="this.innerText=\'' . $masked . '\'" title="Arahkan kursor untuk melihat utuh" ';
            }

            return '
            <div class="d-inline-flex align-items-center" style="gap: 5px;">
                <span class="fw-bold text-primary font-monospace" style="cursor:pointer;"' . $hoverEffect . '>' . $masked . '</span>
                <button type="button" class="btn btn-outline-secondary btn-xs ' . $btnClass . ' py-0 px-1" data-value="' . $full . '" title="' . $btnTitle . '">
                    <i class="fas fa-copy"></i>
                </button>
            </div>';
        };

        // Format data untuk respons DataTables
        $data = [];
        $no = $start + 1;

        foreach ($pagedGroups as $row) {
            // NIK & No KK
            $nikFormatted = $maskNumber($row['nik_kpm'], 'nik');
            $noKkFormatted = !empty($row['no_kk']) ? $maskNumber($row['no_kk'], 'kk') : '<span class="text-muted">-</span>';

            // Nama KPM
            $namaKpm = '<span class="fw-bold text-dark">' . esc(strtoupper($row['nama_kpm'])) . '</span>';

            // Alamat Lengkap + RT / RW
            $rwVal = str_pad(trim((string)$row['rw']), 3, '0', STR_PAD_LEFT);
            $rtVal = str_pad(trim((string)$row['rt']), 3, '0', STR_PAD_LEFT);
            $alamatText = esc($row['alamat']);
            if ($alamatText === '-' || empty($alamatText)) {
                $alamatText = 'RT ' . $rtVal . ' / RW ' . $rwVal;
            } else {
                $alamatText .= ' <br><small class="badge badge-light border text-muted">RT ' . $rtVal . ' / RW ' . $rwVal . '</small>';
            }

            // Nomor KKS
            $noKks = trim((string)($row['nomor_kks'] ?? ''));
            if (!empty($noKks)) {
                $noKksHtml = '
                <div class="d-inline-flex align-items-center" style="gap: 5px;">
                    <span class="font-monospace small fw-bold">' . esc($noKks) . '</span>
                    <button type="button" class="btn btn-outline-secondary btn-xs btnCopyNoKK py-0 px-1" data-value="' . esc($noKks) . '" title="Salin No KKS">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>';
            } else {
                $noKksHtml = '<span class="text-muted small">-</span>';
            }

            // Jenis Bansos: Bersih tanpa sub-label komponen bantuan
            $jenis = $row['jenis_bansos'];
            if ($jenis === 'PKH') {
                $badgeJenis = '<span class="badge badge-primary px-2 py-1"><i class="fas fa-hand-holding-heart mr-1"></i> PKH</span>';
            } elseif ($jenis === 'SEMBAKO') {
                $badgeJenis = '<span class="badge badge-info px-2 py-1" style="background-color: #20c997; color: white;"><i class="fas fa-box-open mr-1"></i> SEMBAKO</span>';
            } else {
                $badgeJenis = '<span class="badge badge-purple px-2 py-1" style="background-color: #6f42c1; color: white;"><i class="fas fa-layer-group mr-1"></i> PKH + SEMBAKO</span>';
            }

            // Tahap Salur
            $tahapSalur = '<span class="badge badge-secondary px-2 py-1">' . esc($row['tahap_salur']) . '</span>';

            // Nominal Cair (Total Akumulasi)
            $nominalVal = (int)($row['nominal_cair'] ?? 0);
            $nominalHtml = ($nominalVal > 0)
                ? '<span class="fw-bold text-success">Rp ' . number_format($nominalVal, 0, ',', '.') . '</span>'
                : '<span class="text-muted">-</span>';

            // Status Salur Badge
            if ($row['status_summary'] === 'sukses') {
                $statusBadge = '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sukses Salur</span>';
            } elseif ($row['status_summary'] === 'gagal') {
                $statusBadge = '<span class="badge badge-danger px-2 py-1 text-wrap" style="max-width: 180px;"><i class="fas fa-times-circle mr-1"></i> ' . esc($row['status_label']) . '</span>';
            } else {
                $statusBadge = '<span class="badge badge-warning px-2 py-1 text-dark"><i class="fas fa-clock mr-1"></i> Belum Transaksi</span>';
            }

            // Petugas Entri
            $petugasNama = !empty($row['petugas_nama']) 
                ? '<span class="badge badge-light border text-dark"><i class="fas fa-user-edit mr-1"></i> ' . esc(ucwords(strtolower($row['petugas_nama']))) . '</span>'
                : '<span class="badge badge-light border text-muted">Admin / Sistem</span>';

            // Tombol Aksi (Detail Modal)
            $aksi = '
            <button type="button" class="btn btn-sm btn-info btn-detail-bnba rounded-pill px-3 shadow-sm" data-id="' . $row['id'] . '" title="Lihat Detail BNBA">
                <i class="fas fa-eye mr-1"></i> Detail
            </button>';

            $data[] = [
                $no++,
                $nikFormatted,
                $namaKpm,
                $noKkFormatted,
                $alamatText,
                $noKksHtml,
                $badgeJenis,
                $tahapSalur,
                $nominalHtml,
                $statusBadge,
                $petugasNama,
                $aksi
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
    }

    /**
     * Data Titik Sebaran Bansos untuk Peta Satelit (AJAX POST)
     * Prioritas 1: Koordinat Rumah (dtsen_kk via dtsen_rt / dtsen_usulan)
     * Prioritas 2: Fallback Koordinat Dokumentasi Penyaluran (dtsen_bansos_kks)
     */
    public function mappingData()
    {
        try {
            $filters = [
                'filter_petugas' => $this->request->getPost('filter_petugas'),
                'filter_rw'      => $this->request->getPost('filter_rw'),
                'filter_rt'      => $this->request->getPost('filter_rt'),
                'filter_tahap'   => $this->request->getPost('filter_tahap'),
                'filter_jenis'   => $this->request->getPost('filter_jenis'),
                'filter_status'  => $this->request->getPost('filter_status')
            ];

            $groups = $this->getGroupedRecipients($filters);
            $totalKpm = count($groups);

            $markers = [];
            $unmapped = [];

            $countMapped = 0;
            $countUnmapped = 0;
            $countSourceRumah = 0;
            $countSourceBansos = 0;

            $countPkh = 0;
            $countSembako = 0;
            $countPkhSembako = 0;

            $countSukses = 0;
            $countBelum = 0;
            $countGagal = 0;

            foreach ($groups as $g) {
                $jb = $g['jenis_bansos'];
                if ($jb === 'PKH') {
                    $countPkh++;
                } elseif ($jb === 'SEMBAKO') {
                    $countSembako++;
                } else {
                    $countPkhSembako++;
                }

                $stSum = $g['status_summary'];
                if ($stSum === 'sukses') {
                    $countSukses++;
                } elseif ($stSum === 'gagal') {
                    $countGagal++;
                } else {
                    $countBelum++;
                }

                $nikFull = trim((string)($g['nik_kpm'] ?? ''));
                $nikMasked = strlen($nikFull) > 8
                    ? substr($nikFull, 0, 8) . str_repeat('*', strlen($nikFull) - 8)
                    : $nikFull;

                $noKkFull = trim((string)($g['no_kk'] ?? ''));
                $noKkMasked = (!empty($noKkFull) && strlen($noKkFull) > 8)
                    ? substr($noKkFull, 0, 8) . str_repeat('*', strlen($noKkFull) - 8)
                    : ($noKkFull ?: '-');

                $rwFormatted = str_pad(trim((string)($g['rw'] ?? '')), 3, '0', STR_PAD_LEFT);
                $rtFormatted = str_pad(trim((string)($g['rt'] ?? '')), 3, '0', STR_PAD_LEFT);
                $nominalVal  = (int)($g['nominal_cair'] ?? 0);
                $nominalFmt  = $nominalVal > 0 ? ('Rp ' . number_format($nominalVal, 0, ',', '.')) : '-';

                $fotoKpmUrl = !empty($g['foto_kpm_kks'])
                    ? base_url('uploads/bansos/' . $g['foto_kpm_kks'])
                    : null;

                $itemData = [
                    'id'                 => (int)$g['id'],
                    'id_kk'              => !empty($g['id_kk']) ? (int)$g['id_kk'] : null,
                    'id_kk_token'        => (!empty($g['id_kk']) && function_exists('encode_id')) ? encode_id($g['id_kk']) : null,
                    'nama_kpm'           => strtoupper(trim((string)$g['nama_kpm'])),
                    'nik_kpm'            => $nikFull,
                    'nik_masked'         => $nikMasked,
                    'no_kk'              => $noKkFull ?: '-',
                    'no_kk_masked'       => $noKkMasked,
                    'kepala_keluarga'    => !empty($g['kepala_keluarga']) ? strtoupper(trim((string)$g['kepala_keluarga'])) : '-',
                    'alamat'             => !empty($g['alamat']) ? $g['alamat'] : '-',
                    'rt'                 => $rtFormatted,
                    'rw'                 => $rwFormatted,
                    'nomor_kks'          => !empty($g['nomor_kks']) ? $g['nomor_kks'] : '-',
                    'jenis_bansos'       => $jb,
                    'tahap_salur'        => $g['tahap_salur'] ?? '-',
                    'nominal_cair'       => $nominalVal,
                    'nominal_formatted'  => $nominalFmt,
                    'status_summary'     => $stSum,
                    'status_label'       => $g['status_label'],
                    'petugas_nama'       => !empty($g['petugas_nama']) ? ucwords(strtolower($g['petugas_nama'])) : 'Admin / Sistem',
                    'foto_kpm_url'       => $fotoKpmUrl,
                    'latitude'           => $g['latitude'],
                    'longitude'          => $g['longitude'],
                    'coord_source'       => $g['coord_source'],
                    'coord_source_label' => $g['coord_source_label']
                ];

                if ($g['coord_source'] !== null && $g['latitude'] !== null && $g['longitude'] !== null) {
                    $countMapped++;
                    if ($g['coord_source'] === 'rumah_kk') {
                        $countSourceRumah++;
                    } else {
                        $countSourceBansos++;
                    }
                    $markers[] = $itemData;
                } else {
                    $countUnmapped++;
                    $unmapped[] = $itemData;
                }
            }

            $persenMapped = $totalKpm > 0 ? round(($countMapped / $totalKpm) * 100, 1) : 0;

            return $this->response->setJSON([
                'status' => true,
                'summary' => [
                    'total_kpm'          => $totalKpm,
                    'total_mapped'       => $countMapped,
                    'total_unmapped'     => $countUnmapped,
                    'persen_mapped'      => $persenMapped,
                    'source_rumah_kk'    => $countSourceRumah,
                    'source_bansos_kks'  => $countSourceBansos,
                    'count_pkh'          => $countPkh,
                    'count_sembako'      => $countSembako,
                    'count_pkh_sembako'  => $countPkhSembako,
                    'count_sukses'       => $countSukses,
                    'count_belum'        => $countBelum,
                    'count_gagal'        => $countGagal,
                ],
                'markers'  => $markers,
                'unmapped' => $unmapped
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mengambil Detail Lengkap 1 Penerima BNBA (AJAX GET)
     */
    public function detailBnba($id)
    {
        try {
            $baseRow = $this->db->table('dtsen_bansos_kks b')
                ->select('b.*, 
                          COALESCE(NULLIF(b.nama_kpm, \'\'), NULLIF(m.nama_penerima, \'\'), a.nama, \'-\') as nama_kpm,
                          COALESCE(NULLIF(m.alamat, \'\'), rt.alamat, k.alamat, \'-\') as alamat,
                          COALESCE(NULLIF(m.rw, \'\'), rt.rw, \'-\') as rw,
                          COALESCE(NULLIF(m.rt, \'\'), rt.rt, \'-\') as rt,
                          m.no_wa, m.status_kks,
                          k.id_kk, k.no_kk, k.kepala_keluarga,
                          k.foto_rumah as kk_foto_depan, rt.foto_rumah as rt_foto_depan,
                          rt.latitude as rt_lat, rt.longitude as rt_lng,
                          u.fullname as petugas_nama', false)
                ->join('dtsen_master_kks m', 'm.nik = b.nik_kpm', 'left')
                ->join('dtsen_art a', 'a.nik = b.nik_kpm AND a.deleted_at IS NULL', 'left')
                ->join('dtsen_kk k', 'k.id_kk = a.id_kk AND k.deleted_at IS NULL', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->join('dtks_users u', 'u.id = b.created_by', 'left')
                ->where('b.id', $id)
                ->get()->getRowArray();

            if (!$baseRow) {
                return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
            }

            $nik = $baseRow['nik_kpm'];
            $tahap = $baseRow['tahap_salur'];
            $fotoDepanUsulan = null;
            $fotoDepanMaster = $baseRow['kk_foto_depan'] ?: ($baseRow['rt_foto_depan'] ?? null);

            // Cari koordinat rumah dari dtsen_kk / dtsen_usulan (Prioritas 1)
            $idKk = !empty($baseRow['id_kk']) ? (int)$baseRow['id_kk'] : null;
            $rumahLat = $baseRow['rt_lat'] ?? null;
            $rumahLng = $baseRow['rt_lng'] ?? null;

            if (!$idKk && !empty($nik)) {
                $uaRow = $this->db->table('dtsen_usulan_art ua')
                    ->select("u.dtsen_kk_id, u.no_kk_target, k.no_kk, k.kepala_keluarga, k.foto_rumah as kk_foto_depan, rt.foto_rumah as rt_foto_depan, rt.latitude as rt_lat, rt.longitude as rt_lng, JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.foto.depan')) as u_foto_depan, JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.geo.lat')) as u_lat, JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.geo.lng')) as u_lng", false)
                    ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'inner')
                    ->join('dtsen_kk k', 'k.id_kk = u.dtsen_kk_id AND k.deleted_at IS NULL', 'left')
                    ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                    ->where('ua.nik', $nik)
                    ->where('ua.deleted_at', null)
                    ->whereIn('u.status', ['draft', 'submitted', 'verified', 'diverifikasi'])
                    ->orderBy('u.id', 'DESC')
                    ->get()->getRowArray();
                if ($uaRow) {
                    $idKk = !empty($uaRow['dtsen_kk_id']) ? (int)$uaRow['dtsen_kk_id'] : null;
                    $fotoDepanUsulan = $uaRow['u_foto_depan'] ?? null;
                    $fotoDepanMaster = $uaRow['kk_foto_depan'] ?: ($uaRow['rt_foto_depan'] ?? $fotoDepanMaster);
                    if (empty($baseRow['no_kk'])) {
                        $baseRow['no_kk'] = $uaRow['no_kk'] ?: $uaRow['no_kk_target'];
                    }
                    if (empty($baseRow['kepala_keluarga'])) {
                        $baseRow['kepala_keluarga'] = $uaRow['kepala_keluarga'];
                    }
                    $rumahLat = !empty($uaRow['u_lat']) ? $uaRow['u_lat'] : ($uaRow['rt_lat'] ?? null);
                    $rumahLng = !empty($uaRow['u_lng']) ? $uaRow['u_lng'] : ($uaRow['rt_lng'] ?? null);
                }
            }

            if ($idKk) {
                $ugRow = $this->db->table('dtsen_usulan')
                    ->select("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.foto.depan')) as u_foto_depan, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.geo.lat')) as u_lat, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.geo.lng')) as u_lng", false)
                    ->where('dtsen_kk_id', $idKk)
                    ->whereIn('status', ['draft', 'submitted', 'verified', 'diverifikasi'])
                    ->orderBy('id', 'DESC')
                    ->get()->getResultArray();
                $usulanCoordFound = false;
                foreach ($ugRow as $ug) {
                    if (empty($fotoDepanUsulan) && !empty($ug['u_foto_depan'])) {
                        $fotoDepanUsulan = $ug['u_foto_depan'];
                    }
                    if (!$usulanCoordFound && $this->isValidCoord($ug['u_lat'] ?? null, $ug['u_lng'] ?? null)) {
                        $rumahLat = $ug['u_lat'];
                        $rumahLng = $ug['u_lng'];
                        $usulanCoordFound = true;
                    }
                    if (!empty($fotoDepanUsulan) && $usulanCoordFound) break;
                }
            }

            $fotoDepan = $fotoDepanUsulan ?: $fotoDepanMaster;
            $baseRow['foto_depan_url'] = !empty($fotoDepan)
                ? base_url($fotoDepan)
                : base_url('data/usulan/foto_rumah/noimage.png');

            // Ambil seluruh bantuan KPM ini pada tahap salur terkait (bisa 1 bansos atau 2 bansos PKH + SEMBAKO)
            $allEntries = $this->db->table('dtsen_bansos_kks b')
                ->select('b.*, u.fullname as petugas_nama', false)
                ->join('dtks_users u', 'u.id = b.created_by', 'left')
                ->where('b.nik_kpm', $nik)
                ->where('b.tahap_salur', $tahap)
                ->orderBy('b.jenis_bansos', 'ASC')
                ->get()->getResultArray();

            if (empty($allEntries)) {
                $allEntries = [$baseRow];
            }

            $items = [];
            $totalNominal = 0;
            $programs = [];

            foreach ($allEntries as $entry) {
                $nominal = (int)($entry['nominal_cair'] ?? 0);
                $totalNominal += $nominal;
                $jb = strtoupper(trim($entry['jenis_bansos']));
                $programs[] = $jb;

                $fotoKpmUrl = !empty($entry['foto_kpm_kks']) 
                    ? base_url('uploads/bansos/' . $entry['foto_kpm_kks']) 
                    : base_url('assets/img/no-image.svg');

                $fotoStrukUrl = !empty($entry['foto_bukti_transaksi']) 
                    ? base_url('uploads/bansos/' . $entry['foto_bukti_transaksi']) 
                    : base_url('assets/img/no-image.svg');

                // Resolusi koordinat: Prioritas 1 Rumah KK -> Prioritas 2 Dokumentasi Bansos KKS
                $effLat = null;
                $effLng = null;
                $coordSourceLabel = null;
                if ($this->isValidCoord($rumahLat, $rumahLng)) {
                    $effLat = $rumahLat;
                    $effLng = $rumahLng;
                    $coordSourceLabel = 'Koordinat Rumah (DTSEN KK)';
                } elseif ($this->isValidCoord($entry['latitude'] ?? null, $entry['longitude'] ?? null)) {
                    $effLat = $entry['latitude'];
                    $effLng = $entry['longitude'];
                    $coordSourceLabel = 'Dokumentasi Salur (Bansos KKS)';
                }

                $mapsUrl = ($effLat !== null && $effLng !== null)
                    ? "https://www.google.com/maps?q={$effLat},{$effLng}"
                    : null;

                $items[] = [
                    'id'                   => (int)$entry['id'],
                    'jenis_bansos'         => $jb,
                    'nominal_cair'         => $nominal,
                    'nominal_formatted'    => 'Rp ' . number_format($nominal, 0, ',', '.'),
                    'status_salur'         => $entry['status_salur'] ?? '',
                    'status_kelengkapan'   => (int)($entry['status_kelengkapan'] ?? 0),
                    'foto_kpm_kks'         => $entry['foto_kpm_kks'] ?? null,
                    'foto_kpm_url'         => $fotoKpmUrl,
                    'foto_bukti_transaksi' => $entry['foto_bukti_transaksi'] ?? null,
                    'foto_struk_url'       => $fotoStrukUrl,
                    'latitude'             => $effLat,
                    'longitude'            => $effLng,
                    'coord_source_label'   => $coordSourceLabel,
                    'maps_url'             => $mapsUrl,
                    'petugas_nama'         => $entry['petugas_nama'] ?? 'Admin / Sistem',
                    'created_at'           => $entry['created_at'] ?? '-',
                ];
            }

            $hasPkh = in_array('PKH', $programs);
            $hasSembako = in_array('SEMBAKO', $programs);
            $hasCombo = in_array('PKH + SEMBAKO', $programs);

            if (($hasPkh && $hasSembako) || $hasCombo) {
                $kategoriJenis = 'PKH + SEMBAKO';
            } elseif ($hasPkh) {
                $kategoriJenis = 'PKH';
            } else {
                $kategoriJenis = 'SEMBAKO';
            }

            $baseRow['jenis_bansos_kategori'] = $kategoriJenis;
            $baseRow['total_nominal_formatted'] = 'Rp ' . number_format($totalNominal, 0, ',', '.');
            $baseRow['rw_formatted'] = str_pad(trim((string)$baseRow['rw']), 3, '0', STR_PAD_LEFT);
            $baseRow['rt_formatted'] = str_pad(trim((string)$baseRow['rt']), 3, '0', STR_PAD_LEFT);
            $baseRow['items'] = $items;

            return $this->response->setJSON(['status' => true, 'data' => $baseRow]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX Dropdown RW
     */
    public function get_rw_ajax()
    {
        $user = $this->authModel->getUserId();
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');
        $roleId = session()->get('role_id') ?? 4;
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        if ($roleId <= 3 || empty($wilayahTugas)) {
            if (empty($kodeDesa)) {
                return $this->response->setJSON([]);
            }
            $query = $this->db->table('dtsen_rt')
                ->select('rw')
                ->distinct()
                ->where('kode_desa', $kodeDesa)
                ->orderBy('rw', 'ASC')
                ->get()->getResultArray();
            $rwList = array_column($query, 'rw');
        } else {
            $rwList = [];
            $blocks = explode('|', $wilayahTugas);
            foreach ($blocks as $block) {
                [$rw,] = array_pad(explode(':', $block), 2, '');
                if (!empty(trim($rw))) {
                    $rwList[] = str_pad(trim($rw), 3, '0', STR_PAD_LEFT);
                }
            }
        }

        $rwList = array_values(array_unique(array_filter($rwList)));
        return $this->response->setJSON($rwList);
    }

    /**
     * AJAX Dropdown RT
     */
    public function get_rt_ajax()
    {
        $rw = $this->request->getGet('rw');
        if (empty($rw)) {
            return $this->response->setJSON([]);
        }

        $user = $this->authModel->getUserId();
        $wilayahTugas = trim($user['wilayah_tugas'] ?? '');
        $roleId = session()->get('role_id') ?? 4;
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        if ($roleId <= 3 || empty($wilayahTugas)) {
            $query = $this->db->table('dtsen_rt')
                ->select('rt')
                ->distinct()
                ->where('kode_desa', $kodeDesa)
                ->where('rw', str_pad($rw, 3, '0', STR_PAD_LEFT))
                ->orderBy('rt', 'ASC')
                ->get()->getResultArray();
            $rtList = array_column($query, 'rt');
        } else {
            $rtList = [];
            $blocks = explode('|', $wilayahTugas);
            foreach ($blocks as $block) {
                [$rwP, $rtsP] = array_pad(explode(':', $block), 2, '');
                if (str_pad(trim($rwP), 3, '0', STR_PAD_LEFT) === str_pad($rw, 3, '0', STR_PAD_LEFT)) {
                    if (!empty(trim($rtsP))) {
                        $exploded = explode(',', $rtsP);
                        foreach ($exploded as $r) {
                            if (!empty(trim($r))) $rtList[] = str_pad(trim($r), 3, '0', STR_PAD_LEFT);
                        }
                    }
                }
            }
        }

        $rtList = array_values(array_unique(array_filter($rtList)));
        return $this->response->setJSON($rtList);
    }

    /**
     * Export Excel Data BNBA Rekapitulasi
     */
    public function exportExcel()
    {
        $filters = [
            'filter_petugas' => $this->request->getGet('petugas'),
            'filter_rw'      => $this->request->getGet('rw'),
            'filter_rt'      => $this->request->getGet('rt'),
            'filter_tahap'   => $this->request->getGet('tahap'),
            'filter_jenis'   => $this->request->getGet('jenis')
        ];

        $groups = $this->getGroupedRecipients($filters);

        // Sorting Excel: RW -> RT -> Nama
        usort($groups, function ($a, $b) {
            $rwA = str_pad(trim((string)$a['rw']), 3, '0', STR_PAD_LEFT);
            $rwB = str_pad(trim((string)$b['rw']), 3, '0', STR_PAD_LEFT);
            if ($rwA !== $rwB) return strcmp($rwA, $rwB);

            $rtA = str_pad(trim((string)$a['rt']), 3, '0', STR_PAD_LEFT);
            $rtB = str_pad(trim((string)$b['rt']), 3, '0', STR_PAD_LEFT);
            if ($rtA !== $rtB) return strcmp($rtA, $rtB);

            return strnatcasecmp($a['nama_kpm'], $b['nama_kpm']);
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('BNBA Bansos');

        // Header Judul
        $sheet->setCellValue('A1', 'REKAPITULASI BY NAME BY ADDRESS (BNBA) BANTUAN SOSIAL');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $subtitle = 'Filter: ' . (!empty($filters['filter_jenis']) ? $filters['filter_jenis'] : 'Semua Bansos');
        if (!empty($filters['filter_tahap'])) $subtitle .= ' | ' . $filters['filter_tahap'];
        if (!empty($filters['filter_rw'])) $subtitle .= ' | RW ' . str_pad($filters['filter_rw'], 3, '0', STR_PAD_LEFT);
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Kolom Tabel
        $headers = [
            'A4' => 'NO',
            'B4' => 'NIK KPM',
            'C4' => 'NO KK',
            'D4' => 'NAMA KPM',
            'E4' => 'ALAMAT',
            'F4' => 'RT',
            'G4' => 'RW',
            'H4' => 'NO KKS',
            'I4' => 'JENIS BANSOS',
            'J4' => 'TAHAP SALUR',
            'K4' => 'NOMINAL CAIR (RP)',
            'L4' => 'STATUS PENYALURAN',
            'M4' => 'PETUGAS ENTRI'
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E7E34'] // Green primary
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A4:M4')->applyFromArray($headerStyle);

        $rowIdx = 5;
        $no = 1;
        foreach ($groups as $item) {
            $sheet->setCellValue('A' . $rowIdx, $no++);
            $sheet->setCellValueExplicit('B' . $rowIdx, $item['nik_kpm'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $rowIdx, $item['no_kk'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $rowIdx, strtoupper($item['nama_kpm']));
            $sheet->setCellValue('E' . $rowIdx, $item['alamat']);
            $sheet->setCellValueExplicit('F' . $rowIdx, str_pad(trim((string)$item['rt']), 3, '0', STR_PAD_LEFT), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G' . $rowIdx, str_pad(trim((string)$item['rw']), 3, '0', STR_PAD_LEFT), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H' . $rowIdx, $item['nomor_kks'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            // Jenis Bansos (bersih tanpa sub-label)
            $sheet->setCellValue('I' . $rowIdx, $item['jenis_bansos']);
            $sheet->setCellValue('J' . $rowIdx, $item['tahap_salur']);
            $sheet->setCellValue('K' . $rowIdx, (int)$item['nominal_cair']);
            $sheet->getStyle('K' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('L' . $rowIdx, $item['status_label'] ?? 'Belum Transaksi');
            $sheet->setCellValue('M' . $rowIdx, !empty($item['petugas_nama']) ? $item['petugas_nama'] : 'Admin / Sistem');

            $rowIdx++;
        }

        // Border seluruh tabel
        $lastRow = $rowIdx - 1;
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:M' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A5:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F5:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I5:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L5:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Auto width untuk seluruh kolom
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'BNBA_Bansos_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
