<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Sinkronisasi data DTKS_BNBA lama ke struktur baru DTSEN
 * Rian & Katie | SINDEN-DTSEN Project
 */
class DtsenSyncBnba extends BaseCommand
{
    protected $group       = 'SINDEN-DTSEN';
    protected $name        = 'dtsen:sync-bnba';
    protected $description = 'Sinkronisasi data lama (dtks_bnba) ke struktur baru DTSEN (dtsen_rt, dtsen_kk, dtsen_art).';

    public function run(array $params)
    {
        $db = Database::connect();

        CLI::write("\n=== 🚀 Mulai Sinkronisasi Data DTKS_BNBA ke DTSEN ===", 'green');

        $kkCount = 0;
        $artCount = 0;
        $rtInserted = [];

        // 1️⃣ Ambil semua Kepala Keluarga dari dtks_bnba
        $kkQuery = $db->table('dtks_bnba')->where('db_shdk_id', 1)->get()->getResultArray();

        if (empty($kkQuery)) {
            CLI::write("⚠ Tidak ada data Kepala Keluarga ditemukan di dtks_bnba.", 'yellow');
            return;
        }

        $totalKK = count($kkQuery);
        CLI::write("📦 Total data Kepala Keluarga ditemukan: {$totalKK}", 'cyan');

        foreach ($kkQuery as $index => $kk) {
            $progress = $index + 1;
            CLI::write("⏳ Memproses KK ke-{$progress} dari {$totalKK} ({$kk['db_nkk']})", 'light_gray');

            // --- Buat atau ambil RT
            $rtKey = "{$kk['db_village']}-{$kk['db_rw']}-{$kk['db_rt']}";

            $rtRow = $db->table('dtsen_rt')
                ->where([
                    'kode_desa' => $kk['db_village'],
                    'rw' => $kk['db_rw'],
                    'rt' => $kk['db_rt']
                ])
                ->get()
                ->getRowArray();

            if (!$rtRow) {
                $db->table('dtsen_rt')->insert([
                    'kode_desa' => $kk['db_village'],
                    'rw' => $kk['db_rw'],
                    'rt' => $kk['db_rt'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $idRt = $db->insertID();
                $rtInserted[] = $rtKey;
            } else {
                $idRt = $rtRow['id_rt'];
            }

            // --- Insert KK ke dtsen_kk
            $kkData = [
                'no_kk'           => $kk['db_nkk'],
                'kepala_keluarga' => $kk['db_nama'],
                'alamat'          => $kk['db_alamat'] ?? '',
                'id_rt'           => $idRt,
                'source_name'     => 'dtks_bnba',
                'created_at'      => date('Y-m-d H:i:s'),
            ];

            $db->table('dtsen_kk')->ignore(true)->insert($kkData);
            $kkId = $db->insertID();
            $kkCount++;

            // --- Ambil seluruh anggota keluarga berdasarkan no KK
            $artQuery = $db->table('dtks_bnba')
                ->where('db_nkk', $kk['db_nkk'])
                ->get()
                ->getResultArray();

            foreach ($artQuery as $art) {
                $artData = [
                    'id_kk'   => $kkId,
                    'nik'           => $art['db_nik'],
                    'nama'          => $art['db_nama'],
                    'jenis_kelamin' => $art['db_jenkel_id'],
                    'tempat_lahir'  => $art['db_tmp_lahir'],
                    'tanggal_lahir' => $art['db_tgl_lahir'],
                    'ibu_kandung'   => $art['db_ibu_kandung'],
                    'hubungan_keluarga'  => $art['db_shdk_id'],
                    'status_kawin'  => $art['db_status'],
                    'program_bansos' => json_encode([
                        'pkh'  => (int)($art['db_pkh'] ?? 0),
                        'bpnt' => (int)($art['db_bpnt'] ?? 0),
                        'bst'  => (int)($art['db_bst'] ?? 0),
                        'pbi'  => (int)($art['db_pbi'] ?? 0),
                    ]),
                    'source_name'   => 'dtks_bnba',
                    'created_at'    => date('Y-m-d H:i:s'),
                ];
                $db->table('dtsen_art')->ignore(true)->insert($artData);
                $artCount++;
            }
        }

        CLI::write("\n✅ Sinkronisasi selesai!", 'green');
        CLI::write("➡ Jumlah KK tersalin        : {$kkCount}", 'yellow');
        CLI::write("➡ Jumlah ART tersalin       : {$artCount}", 'yellow');
        CLI::write("➡ Jumlah RT baru dibuat     : " . count($rtInserted), 'yellow');
        CLI::write("🎯 Semua data telah disalin ke struktur baru DTSEN dengan relasi RT valid.", 'light_blue');
    }
}
