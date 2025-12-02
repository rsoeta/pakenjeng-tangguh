<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class DtsenKkModel extends Model
{
    use WilayahFilterTrait; // ✅ panggil trait-nya

    protected $table            = 'dtsen_kk';
    protected $primaryKey       = 'id_kk';
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields    = [
        'id_rt',
        'no_kk',
        'kepala_keluarga',
        'alamat',
        'status_kepemilikan_rumah',
        'jumlah_anggota',
        'program_bansos',
        'kategori_adat',
        'nama_suku',
        'foto_kk',
        'foto_rumah',
        'foto_rumah_dalam',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;

    // 🔍 Ambil semua KK berdasarkan RT
    public function getByRt($id_rt)
    {
        return $this->where('id_rt', $id_rt)->findAll();
    }

    // 🧱 Ambil data keluarga (untuk datatables)
    public function get_datatables($filter1 = null, $filter2 = null, $filter3 = null, $filter4 = null, $filter0 = null, $filterKepala = null)
    {
        $db = db_connect();
        $builder = $db->table('dtsen_kk kk');
        $builder->select('kk.*, se.kategori_desil, se.status_kks, se.status_bpjs, se.status_kip');

        // join ke dtsen_se untuk ambil desil
        $builder->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left');

        // FIX WAJIB
        $builder->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');

        // filter berdasarkan wilayah (opsional)
        if (!empty($filter1)) $builder->where('kk.id_rt', $filter1);   // contoh filter RT
        if (!empty($filter2)) $builder->like('kk.alamat', $filter2);   // contoh filter alamat
        if (!empty($filter3)) $builder->where('kk.kategori_adat', $filter3);

        // filter hanya kepala keluarga
        if ($filterKepala) {
            $builder->join('dtsen_art art', 'art.id_kk = kk.id_kk AND art.hubungan_keluarga = "Kepala Keluarga"', 'left');
        }

        // pencarian DataTables
        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart()
                ->like('kk.no_kk', $search)
                ->orLike('kk.kepala_keluarga', $search)
                ->orLike('kk.alamat', $search)
                ->groupEnd();
        }

        // sorting
        if (isset($_POST['order'])) {
            $columnIndex = $_POST['order'][0]['column'];
            $sortDir = $_POST['order'][0]['dir'];
            $columns = ['kk.no_kk', 'kk.kepala_keluarga', 'kk.alamat', 'se.kategori_desil'];
            $builder->orderBy($columns[$columnIndex] ?? 'kk.id_kk', $sortDir);
        } else {
            $builder->orderBy('kk.id_kk', 'ASC');
        }

        // pagination
        if ($_POST['length'] != -1) {
            $builder->limit($_POST['length'], $_POST['start']);
        }

        return $builder->get()->getResultArray();
    }

    // 🔢 Hitung total data (untuk datatables)
    public function count_all()
    {
        return $this->countAllResults();
    }

    // 🔢 Hitung total dengan filter
    public function count_filtered($filter1 = null, $filter2 = null, $filter3 = null, $filter4 = null, $filter0 = null, $filterKepala = null)
    {
        $db = db_connect();
        $builder = $db->table('dtsen_kk kk');
        $builder->select('kk.id_kk');
        $builder->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left');

        // FIX WAJIB
        $builder->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');

        if (!empty($filter1)) $builder->where('kk.id_rt', $filter1);
        if ($filterKepala) {
            $builder->join('dtsen_art art', 'art.id_kk = kk.id_kk AND art.hubungan_keluarga = "Kepala Keluarga"', 'left');
        }

        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $builder->groupStart()
                ->like('kk.no_kk', $search)
                ->orLike('kk.kepala_keluarga', $search)
                ->orLike('kk.alamat', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    public function getFilteredData($filter)
    {
        $builder = $this->db->table('dtsen_kk kk')
            ->select('
                kk.id_kk,
                kk.no_kk,
                kk.kepala_keluarga,
                kk.alamat,
                rt.rw,
                rt.rt,
                se.kategori_desil,
                kk.program_bansos,
                kk.kategori_adat,
                kk.jumlah_anggota,
                kk.created_at
            ')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left'); // 🟢 tambahan penting

        // filter wilayah
        if (!empty($filter['kode_desa'])) {
            $builder->where('rt.kode_desa', $filter['kode_desa']);
        }

        // filter wilayah_tugas tetap seperti sebelumnya (kode terakhir yang sudah berfungsi)
        if (!empty($filter['wilayah_tugas'])) {
            $wilayahTugas = trim($filter['wilayah_tugas']);
            $wilayahTugas = str_replace('RW:', '', $wilayahTugas);
            $blokRW = preg_split('/[|;]/', $wilayahTugas);

            $builder->groupStart();
            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if (!$blok) continue;

                $parts = explode(':', $blok);
                $rw = trim($parts[0]);
                $rtList = isset($parts[1]) ? explode(',', $parts[1]) : [];

                $builder->orGroupStart()
                    ->groupStart()
                    ->where('rt.rw', $rw)
                    ->orWhere('rt.rw', str_pad($rw, 2, '0', STR_PAD_LEFT))
                    ->groupEnd();

                if (!empty($rtList)) {
                    $rtVariants = [];
                    foreach ($rtList as $rt) {
                        $rtVariants[] = $rt;
                        $rtVariants[] = str_pad($rt, 2, '0', STR_PAD_LEFT);
                    }
                    $builder->whereIn('rt.rt', $rtVariants);
                }

                $builder->groupEnd();
            }
            $builder->groupEnd();
        }

        $builder->orderBy('kk.no_kk', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Hitung total pembaruan keluarga (yang sudah diverifikasi)
     */
    public function countVerified()
    {
        return $this->selectCount('id')
            // ->where('status', 'diverifikasi')
            ->countAllResults();
    }

    /**
     * Count total KK (BNBA) pada dtsen_kk sesuai filter.
     * $filter sama formatnya dengan getFilteredData
     */
    public function countVerifiedByUser(int $userRole, array $filter = [])
    {
        $builder = $this->db->table('dtsen_kk kk')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');

        // panggil trait helper
        $this->applyWilayahFilter($builder, $filter, $userRole);

        return (int) $builder->countAllResults();
    }
}
