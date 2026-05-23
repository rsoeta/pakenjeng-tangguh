<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class Pdtt2025Model extends Model
{
    protected $table            = 'dtsen_pdtt_2025';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'nik',
        'no_kk',
        'no_rekening',
        'nama_pengurus',
        'lembaga_penyalur',
        'kode_wilayah',
        'prov',
        'kab',
        'kec',
        'kel',
        'alamat',
        'rt',
        'rw',
        'keterangan',
        'kesesuaian',
        'penjelasan',
        'foto_listrik',
        'pekerjaan',
        'jenis_usaha',
        'jumlah_penghasilan',
        'foto_slip_gaji',
        'status_verifikasi',
        'verified_by',
        'verified_at'
    ];
    protected $useTimestamps    = true;

    // ========================================================
    // 📊 DATA TABLES: JOIN KE DATA MASTER SINDEN
    // ========================================================
    public function getDatatablesQuery($filters = [])
    {
        $builder = $this->db->table($this->table . ' p')
            ->select("
                p.*, 
                kks.foto_kks,
                kks.foto_kepemilikan,
                r.kepemilikan_rumah,
                r.foto_rumah,
                se.kepemilikan_aset,
                GROUP_CONCAT(DISTINCT kam.label SEPARATOR ', ') as kondisi_rumah,
                GROUP_CONCAT(DISTINCT a.disabilitas SEPARATOR ', ') as disabilitas_keluarga
            ")
            // ... (sisanya tetap sama)
            ->join('dtsen_kk k', 'k.no_kk = p.no_kk AND k.deleted_at IS NULL', 'left')
            ->join('dtsen_art a', 'a.id_kk = k.id_kk AND a.deleted_at IS NULL', 'left')
            ->join('dtsen_rt r', 'r.id_rt = k.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = k.id_kk', 'left')
            ->join('dtsen_master_kks kks', 'kks.nik = p.nik', 'left')
            ->join('dtsen_penentuan_kemiskinan pk', 'pk.dtsen_kk_id = k.id_kk', 'left')
            ->join('dtsen_penentuan_kemiskinan_alasan pka', 'pka.penentuan_id = pk.id', 'left')
            // ... (Join tetap sama)
            ->join('dtsen_kemiskinan_alasan_master kam', 'kam.id = pka.alasan_id AND kam.kategori = \'Rumah\'', 'left')
            ->groupBy('p.nik'); // 🚀 Ganti p.id dengan p.nik untuk kestabilan Grouping

        // =======================================================
        // 🔐 SOP SINDEN: FILTER KODE DESA & WILAYAH TUGAS
        // =======================================================
        if (!empty($filters['kode_desa'])) {
            $kd = str_replace('.', '', $filters['kode_desa']);
            $builder->where("REPLACE(p.kode_wilayah, '.', '') =", $kd);
        }

        // 🚀 ADAPTASI LOGIKA WILAYAH FILTER TRAIT SINDEN
        if (isset($filters['role_id']) && $filters['role_id'] == 4 && !empty($filters['wilayah_tugas'])) {
            $wilayah = trim(str_replace('RW:', '', $filters['wilayah_tugas']));
            $blokRW = preg_split('/[|;]/', $wilayah);

            $builder->groupStart();

            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if ($blok === '') continue;

                [$rw, $rtCSV] = array_pad(explode(':', $blok), 2, '');
                $rtList = $rtCSV ? explode(',', $rtCSV) : [];

                // Konversi RW ke integer dasar (misal '003' -> 3)
                $baseRw = (string)(int)$rw;

                $builder->orGroupStart()
                    ->groupStart()
                    ->where('p.rw', $baseRw) // Format 1 digit: '3'
                    ->orWhere('p.rw', str_pad($baseRw, 2, '0', STR_PAD_LEFT)) // Format 2 digit: '03'
                    ->orWhere('p.rw', str_pad($baseRw, 3, '0', STR_PAD_LEFT)) // Format 3 digit: '003'
                    ->groupEnd();

                if (!empty($rtList)) {
                    $rtVariants = [];
                    foreach ($rtList as $rt) {
                        $rt = trim($rt);
                        if ($rt === '') continue;

                        $baseRt = (string)(int)$rt;
                        $rtVariants[] = $baseRt; // '4'
                        $rtVariants[] = str_pad($baseRt, 2, '0', STR_PAD_LEFT); // '04'
                        $rtVariants[] = str_pad($baseRt, 3, '0', STR_PAD_LEFT); // '004'
                    }
                    $builder->whereIn('p.rt', $rtVariants);
                }

                $builder->groupEnd();
            }

            $builder->groupEnd();
        }

        // =======================================================
        // 🔍 FILTER DINAMIS DARI FRONTEND
        // =======================================================
        if (!empty($filters['rw'])) {
            $builder->where('p.rw', str_pad($filters['rw'], 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filters['rt'])) {
            $builder->where('p.rt', str_pad($filters['rt'], 3, '0', STR_PAD_LEFT));
        }
        if (!empty($filters['status_verifikasi'])) {
            $builder->where('p.status_verifikasi', $filters['status_verifikasi']);
        }
        // ... (Kode filter dinamis Search Frontend) ...
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('p.nama_pengurus', $filters['search'])
                ->orLike('p.nik', $filters['search'])
                ->orLike('p.no_kk', $filters['search'])
                ->groupEnd();
        }

        // =======================================================
        // 🔄 LOGIKA SORTING TINGKAT TINGGI
        // =======================================================
        $columnOrder = [
            null,
            'p.nama_pengurus',
            'p.nik',
            'p.no_kk',
            'p.alamat',
            'p.keterangan',
            "IF(COALESCE(kks.foto_kepemilikan, kks.foto_kks) != '' AND COALESCE(kks.foto_kepemilikan, kks.foto_kks) NOT LIKE '%noimage%', 1, 0)",
            'r.kepemilikan_rumah',
            'kondisi_rumah',
            "IF(r.foto_rumah != '' AND r.foto_rumah NOT LIKE '%noimage%', 1, 0)",
            "CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(se.kepemilikan_aset, '$.mobil')), '0') AS UNSIGNED)",
            "CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(se.kepemilikan_aset, '$.sepeda_motor')), '0') AS UNSIGNED)",
            'disabilitas_keluarga',
            'p.status_verifikasi',
            null
        ];

        // Reset Order By sebelum menerapkan sort baru agar tidak numpuk
        $builder->orderBy('');

        if (isset($filters['order'])) {
            $orderIdx = $filters['order'][0]['column'];
            $orderDir = $filters['order'][0]['dir'];
            if (isset($columnOrder[$orderIdx]) && $columnOrder[$orderIdx] !== null) {
                $builder->orderBy($columnOrder[$orderIdx], $orderDir, false);
            }
        } else {
            // Default Sorting: Pending di atas, lalu Nama
            $builder->orderBy('p.status_verifikasi', 'ASC');
            $builder->orderBy('p.nama_pengurus', 'ASC');
        }

        return $builder;
    }
}
