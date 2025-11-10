<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Sinkronisasi penuh dari dtks_usulan_caridata ke DTSEN
 * Menyimpan nilai mentah tanpa konversi kode.
 * By Rian x Katie ❤️ (Final v2)
 */
class DtsenSyncUsulanView extends BaseCommand
{
    protected $group       = 'SINDEN';
    protected $name        = 'dtsen:sync-usulan-view';
    protected $description = 'Sinkronisasi penuh data dtks_usulan_caridata ke tabel DTSEN (rt, kk, art, se) tanpa konversi kode';

    public function run(array $params)
    {
        $db = db_connect();

        CLI::write("\n=== 🚀 Mulai Sinkronisasi Data dtks_usulan_caridata ke DTSEN ===", 'green');

        $dataView = $db->table('dtks_usulan_caridata')->get()->getResultArray();
        $total = count($dataView);
        CLI::write("📦 Total data ditemukan: {$total}\n", 'yellow');

        $countRT = 0;
        $countKK = 0;
        $countART = 0;
        $countSE = 0;

        foreach ($dataView as $i => $row) {
            $noKK = trim($row['nokk'] ?? '');
            $nik  = trim($row['du_nik'] ?? '');
            $rw   = trim($row['rw'] ?? '');
            $rt   = trim($row['rt'] ?? '');
            $kodeDesa = trim($row['kelurahan'] ?? '');

            if (!$noKK || !$nik) continue;

            // =====================
            // 🔹 1. Pastikan data RT ada
            // =====================
            $rtData = $db->table('dtsen_rt')
                ->where([
                    'kode_desa' => $kodeDesa,
                    'rw' => $rw,
                    'rt' => $rt
                ])
                ->get()
                ->getRowArray();

            if (!$rtData) {
                $db->table('dtsen_rt')->insert([
                    'kode_desa' => $kodeDesa,
                    'rw' => $rw,
                    'rt' => $rt,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $idRT = $db->insertID();
                $countRT++;
            } else {
                $idRT = $rtData['id_rt'];
            }

            // =====================
            // 🔹 2. Sinkronisasi Data KK
            // =====================
            $kkData = $db->table('dtsen_kk')->where('no_kk', $noKK)->get()->getRowArray();

            if (!$kkData) {
                $kepalaKeluarga = strtoupper($row['nama'] ?? '');
                if (($row['shdk'] ?? 0) != 1) {
                    $kepala = $db->table('dtks_usulan_caridata')
                        ->where('nokk', $noKK)
                        ->where('shdk', 1)
                        ->get()
                        ->getRowArray();
                    if ($kepala) {
                        $kepalaKeluarga = strtoupper($kepala['nama']);
                    }
                }

                $db->table('dtsen_kk')->insert([
                    'id_rt' => $idRT,
                    'no_kk' => $noKK,
                    'kepala_keluarga' => $kepalaKeluarga,
                    'alamat' => strtoupper($row['alamat'] ?? ''),
                    'status_kepemilikan_rumah' => $row['sk0'] ?? 0,
                    'program_bansos' => $row['program_bansos'] ?? null,
                    'foto_kk' => $row['foto_identitas'] ?? null,
                    'foto_rumah' => $row['foto_rumah'] ?? null,
                    'foto_rumah_dalam' => $row['foto_rumah_dalam'] ?? null,
                    'source_name' => 'dtks_usulan_caridata',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $row['created_by'] ?? 'system',
                ]);
                $idKK = $db->insertID();
                $countKK++;
            } else {
                $idKK = $kkData['id_kk'];
            }

            // =====================
            // 🔹 3. Sinkronisasi Data ART
            // =====================
            $artData = $db->table('dtsen_art')->where('nik', $nik)->get()->getRowArray();

            if (!$artData) {
                // 🛠 Pastikan nilai status_hamil tidak null
                $statusHamil = $row['hamil_status'] ?? 'Tidak';
                if ($statusHamil === null || $statusHamil === '' || !in_array($statusHamil, ['Ya', 'Tidak', 1, 0])) {
                    $statusHamil = 'Tidak';
                } elseif ($statusHamil == 1) {
                    $statusHamil = 'Ya';
                } elseif ($statusHamil == 0) {
                    $statusHamil = 'Tidak';
                }

                $db->table('dtsen_art')->insert([
                    'id_kk' => $idKK,
                    'nik' => $nik,
                    'nama' => strtoupper($row['nama'] ?? ''),
                    'hubungan_keluarga' => $row['shdk'] ?? null,
                    'shdk' => $row['shdk'] ?? null,
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
                    'tempat_lahir' => strtoupper($row['tempat_lahir'] ?? ''),
                    'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                    'status_kawin' => $row['status_kawin'] ?? null,
                    'pendidikan_terakhir' => $row['du_pendidikan_id'] ?? null,
                    'pekerjaan' => $row['jenis_pekerjaan'] ?? null,
                    'disabilitas' => $row['disabil_kode'] ?? null,
                    'status_hamil' => $statusHamil,
                    'tgl_hamil' => $row['hamil_tgl'] ?? null,
                    'ibu_kandung' => strtoupper($row['ibu_kandung'] ?? ''),
                    'foto_identitas' => $row['foto_identitas'] ?? null,
                    'program_bansos' => $row['program_bansos'] ?? null,
                    'source_name' => 'dtks_usulan_caridata',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $row['created_by'] ?? 'system',
                ]);
                $countART++;
            }

            // =====================
            // 🔹 4. Sinkronisasi Data SE
            // =====================
            $seData = $db->table('dtsen_se')->where('id_kk', $idKK)->get()->getRowArray();

            if (!$seData) {
                $db->table('dtsen_se')->insert([
                    'id_rt' => $idRT,
                    'id_kk' => $idKK,
                    'sumber_penghasilan' => $row['jenis_pekerjaan'] ?? null,
                    'kepemilikan_aset' => json_encode([
                        'sk3' => $row['sk3'] ?? 0,
                        'sk7' => $row['sk7'] ?? 0,
                        'sk8' => $row['sk8'] ?? 0,
                    ]),
                    'kepemilikan_bantuan' => $row['program_bansos'] ?? null,
                    'status_kks' => (($row['program_bansos'] ?? 0) == 1 || ($row['program_bansos'] ?? 0) == 2) ? 'Ya' : 'Tidak',
                    'status_bpjs' => 'Tidak',
                    'status_kip' => 'Tidak',
                    'catatan_tambahan' => json_encode([
                        'sk0' => $row['sk0'] ?? 0,
                        'sk1' => $row['sk1'] ?? 0,
                        'sk2' => $row['sk2'] ?? 0,
                        'sk3' => $row['sk3'] ?? 0,
                        'sk4' => $row['sk4'] ?? 0,
                        'sk5' => $row['sk5'] ?? 0,
                        'sk6' => $row['sk6'] ?? 0,
                        'sk7' => $row['sk7'] ?? 0,
                        'sk8' => $row['sk8'] ?? 0,
                        'sk9' => $row['sk9'] ?? 0,
                    ]),
                    'latitude' => $row['du_latitude'] ?? null,
                    'longitude' => $row['du_longitude'] ?? null,
                    'accuracy' => $row['du_accuracy'] ?? null,
                    'source_name' => 'dtks_usulan_caridata',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $row['created_by'] ?? 'system',
                ]);
                $countSE++;
            }

            if (($i + 1) % 500 == 0) {
                CLI::write("⏳ Diproses: " . ($i + 1) . " dari {$total} baris...");
            }
        }

        CLI::write("\n✅ Sinkronisasi selesai!", 'green');
        CLI::write("➡ RT baru     : {$countRT}");
        CLI::write("➡ KK baru     : {$countKK}");
        CLI::write("➡ ART baru    : {$countART}");
        CLI::write("➡ SE baru     : {$countSE}\n");
    }
}
