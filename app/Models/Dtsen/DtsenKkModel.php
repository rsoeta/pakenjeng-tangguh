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
        'updated_by',
        'deleted_at',
        'delete_reason'

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

    public function getFilteredData(array $filter)
    {
        $db = $this->db;

        /**
         * ======================================================
         * 1️⃣ AMBIL DATA KK DASAR (AMAN & STABIL)
         * ======================================================
         */
        $builder = $db->table('dtsen_kk kk')
            ->select('
            kk.id_kk,
            kk.no_kk,
            kk.kepala_keluarga,
            kk.alamat,
            rt.rw,
            rt.rt,
            se.kategori_desil
        ')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtsen_se se', 'se.id_kk = kk.id_kk', 'left')
            ->where('kk.deleted_at', null);

        // 🔐 Filter desa
        if (!empty($filter['kode_desa'])) {
            $builder->where('rt.kode_desa', $filter['kode_desa']);
        }

        // 🔐 Filter wilayah tugas (LOGIKA LAMA — JANGAN DIUBAH)
        if (!empty($filter['wilayah_tugas'])) {
            $wilayahTugas = str_replace('RW:', '', trim($filter['wilayah_tugas']));
            $blokRW = preg_split('/[|;]/', $wilayahTugas);

            $builder->groupStart();
            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if (!$blok) continue;

                [$rw, $rtStr] = array_pad(explode(':', $blok), 2, null);
                $rtList = $rtStr ? explode(',', $rtStr) : [];

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

        $keluarga = $builder->orderBy('kk.no_kk', 'ASC')->get()->getResultArray();
        if (empty($keluarga)) return [];

        /**
         * ======================================================
         * 2️⃣ AMBIL USULAN TERBARU (1x QUERY)
         * ======================================================
         */
        $ids = array_column($keluarga, 'id_kk');

        $usulanRows = $db->table('dtsen_usulan')
            ->select('id, dtsen_kk_id, status, payload')
            ->whereIn('dtsen_kk_id', $ids)
            ->whereIn('status', ['draft', 'submitted', 'verified', 'diverifikasi'])
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $usulanMap = [];
        foreach ($usulanRows as $u) {
            if (!isset($usulanMap[$u['dtsen_kk_id']])) {
                $usulanMap[$u['dtsen_kk_id']] = $u;
            }
        }

        /**
         * ======================================================
         * 3️⃣ HITUNG STATUS FINAL
         * ======================================================
         */
        foreach ($keluarga as &$row) {
            $row['usulan_status'] = null;
            $row['is_submitted_ready'] = 0;

            if (!isset($usulanMap[$row['id_kk']])) continue;

            $u = $usulanMap[$row['id_kk']];
            $row['usulan_status'] = $u['status'];

            if ($u['status'] === 'draft') {
                $payload = json_decode($u['payload'], true);
                if ($this->isPayloadLengkap($payload)) {
                    $row['is_submitted_ready'] = 1;
                }
            }
        }
        unset($row);

        /**
         * ======================================================
         * 3.5️⃣ FILTER RW / RT / DESIL (BARU)
         * ======================================================
         */
        $keluarga = array_values(array_filter($keluarga, function ($row) use ($filter) {

            // RW
            if (!empty($filter['rw']) && $filter['rw'] !== 'all') {
                if ((string)$row['rw'] !== (string)$filter['rw']) {
                    return false;
                }
            }

            // RT
            if (!empty($filter['rt']) && $filter['rt'] !== 'all') {
                if ((string)$row['rt'] !== (string)$filter['rt']) {
                    return false;
                }
            }

            // DESIL
            if (!empty($filter['desil']) && $filter['desil'] !== 'all') {
                if ($filter['desil'] === 'none') {
                    return empty($row['kategori_desil']);
                }
                if ((int)$row['kategori_desil'] !== (int)$filter['desil']) {
                    return false;
                }
            }

            return true;
        }));

        /**
         * ======================================================
         * 4️⃣ FILTER STATUS (SUDAH STABIL — JANGAN DIUBAH)
         * ======================================================
         */
        if (!empty($filter['status']) && $filter['status'] !== 'all') {

            $keluarga = array_values(array_filter($keluarga, function ($row) use ($filter) {

                $status = $filter['status'];

                if ($status === 'none') {
                    return empty($row['usulan_status']);
                }

                if ($status === 'draft') {
                    return $row['usulan_status'] === 'draft'
                        && (int)$row['is_submitted_ready'] === 0;
                }

                if ($status === 'submitted') {
                    return $row['usulan_status'] === 'draft'
                        && (int)$row['is_submitted_ready'] === 1;
                }

                if ($status === 'verified') {
                    return in_array($row['usulan_status'], ['verified', 'diverifikasi']);
                }

                return true;
            }));
        }

        return $keluarga;
    }

    private function isPayloadLengkap(array $payload): bool
    {
        return !empty($payload['perumahan']['no_kk'])
            && !empty($payload['perumahan']['kepala_keluarga'])
            && !empty($payload['perumahan']['alamat'])
            && !empty($payload['perumahan']['wilayah'])
            && !empty($payload['perumahan']['kondisi'])
            && !empty($payload['perumahan']['sanitasi'])
            && !empty($payload['foto']['ktp_kk'])
            && !empty($payload['foto']['depan'])
            && !empty($payload['foto']['dalam']);
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
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('kk.deleted_at', null);

        // panggil trait helper
        $this->applyWilayahFilter($builder, $filter, $userRole);

        return (int) $builder->countAllResults();
    }
}
