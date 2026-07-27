<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;

class AnalisisDesil extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // 🚀 Ambil semua label periode yang ada di database agar future-proof
        $periodes = $this->db->table('dtsen_desil_history')
            ->select('periode_label, tahun, triwulan')
            ->groupBy('periode_label')
            ->orderBy('tahun', 'DESC')
            ->orderBy('triwulan', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title'    => 'Dashboard Analisis Perubahan Desil',
            'periodes' => $periodes,
        ];

        return view('dtsen/analisis_desil/v_analisis_desil', $data);
    }

    public function datatable()
    {
        try {
            $post = $this->request->getPost();

            $periodeAwal = $post['periode_awal'] ?? '';
            $periodeAkhir = $post['periode_akhir'] ?? '';

            if (empty($periodeAwal) || empty($periodeAkhir)) {
                return $this->response->setJSON(['data' => [], 'summary' => []]);
            }

            // 🚀 GEMBOK WILAYAH (Hanya Tarik Data Desa Pasirlangu)
            $kodeDesa = session()->get('kode_desa');

            // 🚀 STEP 1: QUERY UTAMA
            $builder = $this->db->table('dtsen_kk k')
                ->select('k.id_kk, k.no_kk, a_kepala.nama as kepala_keluarga, rt.rt, rt.rw,
                          d1.desil as desil_awal, d2.desil as desil_akhir')
                ->join('dtsen_desil_history d1', "d1.id_kk = k.id_kk AND d1.periode_label = '{$periodeAwal}'", 'left')
                ->join('dtsen_desil_history d2', "d2.id_kk = k.id_kk AND d2.periode_label = '{$periodeAkhir}'", 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
                ->join('dtsen_art a_kepala', 'a_kepala.id_kk = k.id_kk AND a_kepala.hubungan_keluarga = 1 AND a_kepala.deleted_at IS NULL', 'left')
                ->where('k.deleted_at IS NULL');

            // Terapkan Filter Desa dari Session
            if (!empty($kodeDesa)) {
                // 🚀 PERBAIKAN: Ubah alias dari k menjadi rt
                $builder->where('rt.kode_desa', $kodeDesa);
            }

            // Terapkan Filter RW / RT dari Dropdown
            if (!empty($post['rw'])) {
                $builder->where('rt.rw', str_pad($post['rw'], 3, '0', STR_PAD_LEFT));
            }
            if (!empty($post['rt'])) {
                $builder->where('rt.rt', str_pad($post['rt'], 3, '0', STR_PAD_LEFT));
            }

            $results = $builder->groupBy('k.id_kk')->get()->getResultArray();

            // 🚀 STEP 2: MAPPING BANSOS VIA PHP (Sekarang Jauh Lebih Ringan!)
            $bansosMap = [];
            $idKkList = array_column($results, 'id_kk');

            if (!empty($idKkList)) {
                $chunks = array_chunk($idKkList, 500);
                foreach ($chunks as $chunk) {
                    $bansosQuery = $this->db->table('dtsen_bansos_kks b')
                        ->select('art.id_kk, b.jenis_bansos')
                        ->join('dtsen_art art', 'art.nik = b.nik_kpm', 'inner')
                        // Gunakan TRIM untuk mencegah error karena spasi tidak sengaja saat entri
                        ->where('TRIM(b.status_salur)', 'Sukses Salur')
                        ->where('art.deleted_at IS NULL') // Pastikan NIK anggota keluarga masih aktif
                        ->whereIn('art.id_kk', $chunk)
                        ->get()->getResultArray();

                    foreach ($bansosQuery as $bq) {
                        $kk = $bq['id_kk'];
                        if (!isset($bansosMap[$kk])) {
                            $bansosMap[$kk] = [];
                        }
                        $bansosMap[$kk][] = $bq['jenis_bansos'];
                    }
                }
            }

            // 🚀 STEP 3: OLAH DATA & LOGIKA POTENSI CORET
            $data = [];
            $no = 1;

            $total = count($results);
            $naik = 0;
            $turun = 0;
            $tetap = 0;
            $potensiCoret = 0;

            foreach ($results as $row) {
                // Rakit string bansos jika keluarga ini ada di dalam map
                $bansosAktif = '';
                if (isset($bansosMap[$row['id_kk']])) {
                    $bansosAktif = implode(', ', array_unique($bansosMap[$row['id_kk']]));
                }

                // Logika Presisi untuk Desil Kosong / NULL
                $desilAwalVal = ($row['desil_awal'] !== null && $row['desil_awal'] !== '') ? (int)$row['desil_awal'] : 99;
                $desilAkhirVal = ($row['desil_akhir'] !== null && $row['desil_akhir'] !== '') ? (int)$row['desil_akhir'] : 99;

                $strAwal = ($desilAwalVal == 99) ? 'Tidak Ada' : $desilAwalVal;
                $strAkhir = ($desilAkhirVal == 99) ? 'Tidak Ada' : $desilAkhirVal;

                $isNaik = false;
                $statusBadge = '';
                $prediksiNasib = '<span class="badge bg-success">Aman</span>';

                // Kesejahteraan Naik = Angka Desil Membesar
                if ($desilAkhirVal > $desilAwalVal) {
                    $naik++;
                    $isNaik = true;
                    $statusBadge = '<span class="badge bg-danger"><i class="fas fa-arrow-up"></i> Naik (Ke ' . $strAkhir . ')</span>';
                }
                // Kesejahteraan Turun = Angka Desil Mengecil
                elseif ($desilAkhirVal < $desilAwalVal) {
                    $turun++;
                    $statusBadge = '<span class="badge bg-success"><i class="fas fa-arrow-down"></i> Turun (Ke ' . $strAkhir . ')</span>';
                } else {
                    $tetap++;
                    $statusBadge = '<span class="badge bg-secondary">Tetap (' . $strAkhir . ')</span>';
                }

                // 🚨 LOGIKA EMAS POTENSI CORET BANSOS
                if ($isNaik && $desilAkhirVal > 4 && $bansosAktif !== '') {
                    $potensiCoret++;
                    $prediksiNasib = '<span class="badge bg-danger pulse-danger"><i class="fas fa-exclamation-triangle"></i> Rawan Coret Pusat</span>';
                }

                $data[] = [
                    $no++,
                    '<b>' . esc($row['kepala_keluarga'] ?? 'Tanpa Nama') . '</b><br>' .
                        '<small class="text-muted">KK: ' . $row['no_kk'] . ' ' .
                        '<a href="javascript:void(0)" onclick="copyKK(\'' . $row['no_kk'] . '\')" class="text-primary ms-1" title="Salin No. KK"><i class="far fa-copy"></i></a></small>',
                    'RT ' . str_pad($row['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($row['rw'] ?? '0', 3, '0', STR_PAD_LEFT),
                    // ... sisa kolom lainnya ...
                    '<span class="badge bg-dark fs-6">' . $strAwal . '</span>',
                    '<span class="badge bg-primary fs-6">' . $strAkhir . '</span>',
                    $statusBadge,
                    ($bansosAktif !== '') ? '<span class="badge bg-info text-dark">' . $bansosAktif . '</span>' : '-',
                    $prediksiNasib
                ];
            }

            $summary = [
                'total' => $total,
                'naik'  => $total > 0 ? round(($naik / $total) * 100, 1) : 0,
                'turun' => $total > 0 ? round(($turun / $total) * 100, 1) : 0,
                'tetap' => $total > 0 ? round(($tetap / $total) * 100, 1) : 0,
                'val_naik' => $naik,
                'val_turun' => $turun,
                'val_tetap' => $tetap,
                'potensi_coret' => $potensiCoret
            ];

            return $this->response->setJSON(['data' => $data, 'summary' => $summary]);
        } catch (\Throwable $th) {
            return $this->response->setJSON(['error' => 'Gagal memuat data: ' . $th->getMessage()]);
        }
    }
}
