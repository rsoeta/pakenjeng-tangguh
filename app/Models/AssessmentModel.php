<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class AssessmentModel extends Model
{
    use WilayahFilterTrait;

    protected $table            = 'slrt_assessment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';

    protected $allowedFields    = [
        'nomor_asesmen',
        'tanggal_asesmen',
        'id_kk',
        'no_kk',
        'nik_sasaran',
        'nama_sasaran',
        'nik_pembuat_pernyataan',
        'nama_pembuat_pernyataan',
        'pembuat_pernyataan_hp',
        'kategori_ppks_id',
        'riwayat_penyakit',
        'permasalahan_tambahan',
        'permohonan_bantuan',
        'foto_pasien',
        'foto_depan',
        'foto_dalam',
        'foto_kamar_mandi',
        'lampiran_siks',
        'lampiran_faskes',
        'fasilitator_nama',
        'fasilitator_nik',
        'ketua_puskesos_nama',
        'supervisor_nama',
        'supervisor_niat',
        'kepala_desa_nama',
        'camat_nama',
        'status',
        'kode_desa',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * 🔢 Generator Nomor Urut Asesmen Resmi
     * Format: ASM/YYYYMM/KODEDESA/NNNN (Contoh: ASM/202610/3205332006/0001)
     */
    public function generateNomorAsesmen(string $kodeDesa): string
    {
        $periode = date('Ym');
        $kodeClean = preg_replace('/[^0-9]/', '', $kodeDesa);
        $prefix = "ASM/{$periode}/{$kodeClean}/";

        // 🛡️ WAJIB withDeleted() agar nomor dari record yang di-soft delete tetap dihitung dan tidak bentrok (UNIQUE KEY)
        $last = $this->withDeleted()
            ->select('nomor_asesmen')
            ->like('nomor_asesmen', $prefix, 'after')
            ->orderBy('nomor_asesmen', 'DESC')
            ->first();

        $nextNumber = 1;
        if ($last && !empty($last['nomor_asesmen'])) {
            $lastNumber = (int) substr($last['nomor_asesmen'], -4);
            $nextNumber = $lastNumber + 1;
        }

        // Loop verifikasi untuk memastikan nomor belum pernah ada di database (termasuk yang soft-deleted)
        do {
            $formattedNumber = str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $candidateNomor = $prefix . $formattedNumber;

            $exists = $this->withDeleted()
                ->where('nomor_asesmen', $candidateNomor)
                ->countAllResults();

            if ($exists === 0) {
                break;
            }
            $nextNumber++;
        } while ($nextNumber < 9999);

        return $candidateNomor;
    }

    /**
     * 📊 Server-side DataTables Query
     */
    public function getDatatables(array $params = [], int $userRole = 4)
    {
        $builder = $this->db->table('slrt_assessment a')
            ->select('
                a.*,
                kk.alamat,
                rt.rt,
                rt.rw,
                v.name AS nama_desa,
                pk.pk_nama_kategori,
                se.kategori_desil
            ')
            ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('tb_villages v', 'v.id = a.kode_desa', 'left')
            ->join('ppks_kategori pk', 'pk.pk_id = a.kategori_ppks_id', 'left')
            ->join('dtsen_se se', 'se.id_kk = a.id_kk', 'left')
            ->where('a.deleted_at IS NULL');

        // Filter Desa
        if (!empty($params['kode_desa'])) {
            $builder->where('a.kode_desa', $params['kode_desa']);
        }

        // Filter Wilayah Tugas untuk enumerator/petugas entri
        if (!empty($params['wilayah_tugas']) && $userRole > 3) {
            $this->applyWilayahFilter($builder, [
                'kode_desa'     => $params['kode_desa'] ?? '',
                'wilayah_tugas' => $params['wilayah_tugas']
            ], $userRole);
        }

        // Search Filter
        if (!empty($params['search'])) {
            $search = $params['search'];
            $builder->groupStart()
                ->like('a.nomor_asesmen', $search)
                ->orLike('a.no_kk', $search)
                ->orLike('a.nik_sasaran', $search)
                ->orLike('a.nama_sasaran', $search)
                ->orLike('kk.alamat', $search)
                ->orLike('pk.pk_nama_kategori', $search)
                ->groupEnd();
        }

        return $builder;
    }

    /**
     * 🔍 Mengambil Data Asesmen Lengkap untuk Lembar Cetak 1:1
     */
    public function getDetailCetak(int $id)
    {
        $asesmen = $this->db->table('slrt_assessment a')
            ->select('
                a.*,
                kk.alamat AS kk_alamat,
                kk.jumlah_anggota,
                kk.foto_rumah AS kk_foto_depan,
                kk.foto_rumah_dalam AS kk_foto_dalam,
                rt.rt,
                rt.rw,
                rt.latitude,
                rt.longitude,
                rt.foto_kamar_mandi AS rt_foto_mck,
                v.name AS nama_desa,
                d.name AS nama_kecamatan,
                reg.name AS nama_kabupaten,
                pk.pk_nama_kategori,
                se.kategori_desil,
                se.rata_penghasilan_bulanan,
                se.status_bpjs,
                se.status_kks,
                se.status_kip
            ')
            ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('tb_villages v', 'v.id = a.kode_desa', 'left')
            ->join('tb_districts d', 'd.id = v.district_id', 'left')
            ->join('tb_regencies reg', 'reg.id = v.regency_id', 'left')
            ->join('ppks_kategori pk', 'pk.pk_id = a.kategori_ppks_id', 'left')
            ->join('dtsen_se se', 'se.id_kk = a.id_kk', 'left')
            ->where('a.id', $id)
            ->where('a.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (!$asesmen) {
            return null;
        }

        // 1. Data Sasaran PMKS / Pasien (Detail ART)
        $asesmen['sasaran'] = $this->db->table('dtsen_art art')
            ->select('
                art.*,
                sk.StatusKawin AS nama_status_kawin,
                pk.pk_nama AS nama_pekerjaan,
                shdk.jenis_shdk AS nama_shdk
            ')
            ->join('tb_status_kawin sk', 'sk.idStatus = art.status_kawin', 'left')
            ->join('tb_penduduk_pekerjaan pk', 'pk.pk_id = art.pekerjaan', 'left')
            ->join('tb_shdk shdk', 'shdk.id = art.hubungan_keluarga', 'left')
            ->where('art.nik', $asesmen['nik_sasaran'])
            ->where('art.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        // 2. Data Pembuat Pernyataan Keaslian Dokumen
        $asesmen['pembuat_pernyataan'] = $this->db->table('dtsen_art art')
            ->select('
                art.*,
                sk.StatusKawin AS nama_status_kawin,
                pk.pk_nama AS nama_pekerjaan,
                shdk.jenis_shdk AS nama_shdk
            ')
            ->join('tb_status_kawin sk', 'sk.idStatus = art.status_kawin', 'left')
            ->join('tb_penduduk_pekerjaan pk', 'pk.pk_id = art.pekerjaan', 'left')
            ->join('tb_shdk shdk', 'shdk.id = art.hubungan_keluarga', 'left')
            ->where('art.nik', $asesmen['nik_pembuat_pernyataan'])
            ->where('art.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        // 3. Seluruh Anggota Keluarga (ART) dalam KK untuk Tabel Bagian III
        $asesmen['anggota_keluarga'] = $this->db->table('dtsen_art art')
            ->select('
                art.*,
                sk.StatusKawin AS nama_status_kawin,
                pk.pk_nama AS nama_pekerjaan,
                shdk.jenis_shdk AS nama_shdk
            ')
            ->join('tb_status_kawin sk', 'sk.idStatus = art.status_kawin', 'left')
            ->join('tb_penduduk_pekerjaan pk', 'pk.pk_id = art.pekerjaan', 'left')
            ->join('tb_shdk shdk', 'shdk.id = art.hubungan_keluarga', 'left')
            ->where('art.id_kk', $asesmen['id_kk'])
            ->where('art.deleted_at IS NULL')
            ->orderBy('art.hubungan_keluarga', 'ASC')
            ->orderBy('art.tanggal_lahir', 'ASC')
            ->get()
            ->getResultArray();

        // 4. Data Kop & Profil Lembaga Desa
        $asesmen['profil_desa'] = $this->db->table('lembaga_profil lp')
            ->where('lp.lp_user', $asesmen['created_by'])
            ->orWhere('lp.lp_kode', $asesmen['kode_desa'])
            ->get()
            ->getRowArray();

        if (empty($asesmen['profil_desa'])) {
            $asesmen['profil_desa'] = $this->db->table('lembaga_profil')->orderBy('lp_id', 'ASC')->first();
        }

        return $asesmen;
    }

    /**
     * 📋 Mengambil Daftar Riwayat Penyakit Unik yang Pernah Tersimpan di slrt_assessment
     */
    public function getDistinctRiwayatPenyakit(): array
    {
        $rows = $this->select('riwayat_penyakit')
            ->where('riwayat_penyakit IS NOT NULL')
            ->where('TRIM(riwayat_penyakit) !=', '')
            ->where('deleted_at IS NULL')
            ->groupBy('riwayat_penyakit')
            ->orderBy('riwayat_penyakit', 'ASC')
            ->findAll();

        $defaults = [
            'Memiliki Riwayat Penyakit Darah Tinggi',
            'Memiliki Riwayat Penyakit Muntaber',
            'Stroke / Kelumpuhan Syaraf Motorik',
            'Diabetes Melitus / Gula Darah Akut',
            'Penyakit Jantung Kronis',
            'Gagal Ginjal Kronis (Cuci Darah)',
            'Kanker / Tumor Ganas',
            'Tuberculosis (TBC / Paru-Paru)',
            'Orang Dengan Gangguan Jiwa (ODGJ)',
            'Disabilitas Fisik / Sensorik / Netra',
            'Gizi Buruk / Stunting Balita',
            'Lanjut Usia Terlantar / Sakit Menahun'
        ];

        $unique = [];
        foreach ($rows as $r) {
            $val = trim($r['riwayat_penyakit']);
            if (!empty($val)) {
                // Periksa apakah sudah ada (case-insensitive)
                $exists = false;
                foreach ($unique as $u) {
                    if (strcasecmp($u, $val) === 0) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $unique[] = $val;
                }
            }
        }

        // Tambahkan default jika belum terdapat di data unik
        foreach ($defaults as $d) {
            $exists = false;
            foreach ($unique as $u) {
                if (strcasecmp($u, $d) === 0) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $unique[] = $d;
            }
        }

        return $unique;
    }

    /**
     * 📋 Mengambil Daftar Permohonan Bantuan / Keluhan Unik yang Pernah Tersimpan di slrt_assessment
     */
    public function getDistinctPermohonanBantuan(): array
    {
        $rows = $this->select('permohonan_bantuan')
            ->where('permohonan_bantuan IS NOT NULL')
            ->where('TRIM(permohonan_bantuan) !=', '')
            ->where('deleted_at IS NULL')
            ->groupBy('permohonan_bantuan')
            ->orderBy('permohonan_bantuan', 'ASC')
            ->findAll();

        $defaults = [
            'Mendapatkan Pelayanan BPJS KIS PBI',
            'Keringanan Biaya Kesehatan Lapad Ruhama Dinas Sosial Kabupaten Garut',
            'Permohonan Rekomendasi Jaminan Kesehatan Daerah (Jamkesda)',
            'Permohonan Bantuan Alat Bantu Kesejahteraan Sosial (Kursi Roda/Kruk/Hearing Aid)',
            'Permohonan Rujukan Pemeriksaan Lanjutan Rumah Sakit',
            'Permohonan Bantuan Kedaruratan Medis dan Obat-obatan'
        ];

        $unique = [];
        foreach ($rows as $r) {
            $val = trim($r['permohonan_bantuan']);
            if (!empty($val)) {
                $exists = false;
                foreach ($unique as $u) {
                    if (strcasecmp($u, $val) === 0) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $unique[] = $val;
                }
            }
        }

        foreach ($defaults as $d) {
            $exists = false;
            foreach ($unique as $u) {
                if (strcasecmp($u, $d) === 0) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $unique[] = $d;
            }
        }

        return $unique;
    }
}

