<?php

namespace App\Models\Dtsen; // 🚀 Direvisi menjadi Dtsen

use CodeIgniter\Model;

class AnomaliModel extends Model
{
    protected $table            = 'dtsen_anomali';
    protected $primaryKey       = 'id_anomali';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'nik',
        'no_kk',
        'nama_lengkap',
        'ibu_kandung',
        'tempat_lahir',
        'tanggal_lahir',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'desa',
        'alamat',
        'rt',
        'rw',
        'shdk',
        'jenis_kelamin',
        'status_kawin',
        'pekerjaan',
        'bukti_siksng',
        'foto_kk_baru',
        'petugas_entri_id',
        'status_anomali',
        'catatan_penolakan',
        'created_by',
        'updated_by'
    ];

    /**
     * 🚀 Fungsi Super Join
     */
    public function searchPendudukByNik($nik)
    {
        $builder = $this->db->table('dtsen_art art');

        $builder->select('
            art.nik, art.nama as nama_lengkap, art.ibu_kandung, art.tempat_lahir, 
            art.tanggal_lahir, art.jenis_kelamin,
            kk.no_kk, kk.alamat,
            rt.rt, rt.rw,
            
            rt.kode_desa as desa,
            vill.province_id as provinsi,
            vill.regency_id as kabupaten,
            vill.district_id as kecamatan,
            art.shdk as shdk,
            art.status_kawin as status_kawin,
            art.pekerjaan as pekerjaan,
            
            prov.name as provinsi_nama,
            reg.name as kabupaten_nama,
            dist.name as kecamatan_nama,
            vill.name as desa_nama,
            shdk.jenis_shdk as shdk_nama,
            kawin.StatusKawin as status_kawin_nama,
            pek.pk_nama as pekerjaan_nama
        ');

        $builder->join('dtsen_kk kk', 'art.id_kk = kk.id_kk', 'left');
        $builder->join('dtsen_rt rt', 'kk.id_rt = rt.id_rt', 'left');
        $builder->join('tb_villages vill', 'rt.kode_desa = vill.id', 'left');
        $builder->join('tb_districts dist', 'vill.district_id = dist.id', 'left');
        $builder->join('tb_regencies reg', 'vill.regency_id = reg.id', 'left');
        $builder->join('tb_provinces prov', 'vill.province_id = prov.id', 'left');
        $builder->join('tb_shdk shdk', 'art.shdk = shdk.id', 'left');
        $builder->join('tb_status_kawin kawin', 'art.status_kawin = kawin.idStatus', 'left');
        $builder->join('tb_penduduk_pekerjaan pek', 'art.pekerjaan = pek.pk_id', 'left');

        $builder->where('art.nik', $nik);

        return $builder->get()->getRowArray();
    }

    /**
     * 🚀 Fungsi Pencari Petugas Entri berdasarkan String wilayah_tugas
     */
    public function findPetugasByWilayah($rw, $rt)
    {
        if (empty($rw) || empty($rt)) return 0;

        // Ambil semua user yang memiliki wilayah_tugas
        $users = $this->db->table('dtks_users')
            ->select('id, wilayah_tugas')
            ->where('wilayah_tugas !=', '')
            ->where('wilayah_tugas IS NOT NULL')
            ->get()->getResultArray();

        foreach ($users as $user) {
            $wilayah = $user['wilayah_tugas']; // Contoh: "002:004|003:001"
            $parts = explode('|', $wilayah);

            foreach ($parts as $part) {
                $rw_rt = explode(':', $part); // Pecah jadi RW dan kumpulan RT
                if (count($rw_rt) == 2) {
                    $u_rw = trim($rw_rt[0]);
                    $u_rts = explode(',', trim($rw_rt[1])); // Kumpulan RT (bisa > 1)

                    // Jika RW cocok, dan RT ada di dalam array RT petugas tersebut
                    if ($u_rw === $rw && in_array($rt, $u_rts)) {
                        return (int) $user['id'];
                    }
                }
            }
        }

        return 0; // Jika tidak ada petugas yang cocok
    }
}
