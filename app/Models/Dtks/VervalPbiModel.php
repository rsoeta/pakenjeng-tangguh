<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class VervalPbiModel extends Model
{
    protected $table      = "dtks_pbi_jkn";
    protected $primaryKey = "id";

    protected $allowedFields = [
        "no", "noka", "ps_noka", "nama", "jenkel", "tgllhr", "tmplhr", "nik", "nik_siks", "pisat", "kdstawin", "kelas_rawat", "kkno", "alamat", "kddati2", "nmdati2", "kddesa", "nmdesa", "kdkec", "nmkec", "rt", "rw", "kodepos", "kddati2_ppk", "nmdati2_ppk", "keterangan_bayi", "nikayah", "nmayah", "nikibu", "nmibu", "ket_aktivasi", "kdkepwil", "kdkc", "nmkc", "kdprov", "nmprov", "flag_mutasi_agustus", "flag_data_sk", "ket_data_sk", "cek_kec_dan_desa", "status", "verivali_pbi", "desa_kode", "created_by", "created_at", "updated_by", "updated_at"
    ];

    protected $useTimestamps = true;

    var $column_order = array('', 'nama', 'alamat',  'kkno', 'nik', 'nik_siks', 'tmplhr', 'tgllhr', 'nmayah', 'nmibu', 'status');

    var $order = array('updated_at' => 'asc');

    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND desa_kode = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND dtks_pbi_jkn.status = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND verivali_pbi = '$filter5'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(noka LIKE '%$search%' OR ps_noka LIKE '%$search%' OR nama LIKE '%$search%' OR nik LIKE '%$search%' OR nik_siks LIKE '%$search%' OR kkno LIKE '%$search%' OR alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        }

        // order
        if (isset($_POST['order'])) {
            $result_order = $this->column_order[$_POST['order']['0']['column']];
            $result_dir = $_POST['order']['0']['dir'];
        } else if ($this->order) {
            $order = $this->order;
            $result_order = key($order);
            $result_dir = $order[key($order)];
        }

        if ($_POST['length'] != -1);
        $db = db_connect();
        $builder = $db->table('dtks_pbi_jkn');
        $query = $builder->select('*')
            ->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_pbi_jkn.kdstawin')
            // ->join('pekerjaan_kondisi_pekerjaan', 'pekerjaan_kondisi_pekerjaan.IDKondisi=individu_data.KondisiPekerjaan')
            // ->join('pendidikan_pend_tinggi', 'pendidikan_pend_tinggi.IDPendidikan=individu_data.PendTertinggi')
            // ->join('ket_verivali', 'ket_verivali.id_ketvv=individu_data.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(id) as jml FROM dtks_pbi_jkn";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND desa_kode = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND dtks_pbi_jkn.status = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND verivali_pbi = '$filter5'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (noka LIKE '%$search%' OR ps_noka LIKE '%$search%' OR nama LIKE '%$search%' OR nik LIKE '%$search%' OR nik_siks LIKE '%$search%' OR kkno LIKE '%$search%' OR alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        }

        $sQuery = "SELECT COUNT(id) as jml FROM dtks_pbi_jkn WHERE id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function get_datatables_verivali($filter1, $filter2, $filter3, $filter4, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND desa_kode = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND dtks_pbi_jkn.status = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND verivali_pbi = '$filter5'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(noka LIKE '%$search%' OR ps_noka LIKE '%$search%' OR nama LIKE '%$search%' OR nik LIKE '%$search%' OR kkno LIKE '%$search%' OR alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        }

        // order
        if (isset($_POST['order'])) {
            $result_order = $this->column_order[$_POST['order']['0']['column']];
            $result_dir = $_POST['order']['0']['dir'];
        } else if ($this->order) {
            $order = $this->order;
            $result_order = key($order);
            $result_dir = $order[key($order)];
        }

        if ($_POST['length'] != -1);
        $db = db_connect();
        $builder = $db->table('dtks_pbi_jkn');
        $query = $builder->select('*')
            ->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_pbi_jkn.kdstawin')
            ->join('dtks_verivali_pbi', 'dtks_verivali_pbi.vp_id=dtks_pbi_jkn.verivali_pbi')

            // ->join('tbl_rt', 'tbl_rt.IdJenKel=pbb_dhkp21.JKAnak')
            // ->join('pekerjaan_kondisi_pekerjaan', 'pekerjaan_kondisi_pekerjaan.IDKondisi=individu_data.KondisiPekerjaan')
            // ->join('pendidikan_pend_tinggi', 'pendidikan_pend_tinggi.IDPendidikan=individu_data.PendTertinggi')
            // ->join('ket_verivali', 'ket_verivali.id_ketvv=individu_data.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua_verivali()
    {
        $sQuery = "SELECT COUNT(id) as jml FROM dtks_pbi_jkn";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter_verivali($filter1, $filter2, $filter3, $filter4, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND desa_kode = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND dtks_pbi_jkn.status = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND dtks_pbi_jkn.status = '$filter5'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (noka LIKE '%$search%' OR ps_noka LIKE '%$search%' OR nama LIKE '%$search%' OR nik LIKE '%$search%' OR nik_siks LIKE '%$search%' OR kkno LIKE '%$search%' OR alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        }

        $sQuery = "SELECT COUNT(id) as jml FROM dtks_pbi_jkn WHERE id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function rekapVervalPbi()
    {
        // $jbt = (session()->get('kode_desa'));

        $builder = $this->db->table('dtks_pbi_jkn');
        $builder->select('nmdesa, no_rw');
        $builder->join('tbl_rw', 'tbl_rw.no_rw=dtks_pbi_jkn.rw');
        // $builder->where('kelurahan', $jbt);
        $builder->selectCount('status');
        $builder->groupBy('nmdesa, no_rw');

        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getDataLogin($email, $tbl)
    {
        $builder = $this->db->table($tbl);
        $builder->where('email', $email);
        $log = $builder->get()->getRow();
        return $log;
    }

    public function getDtks()
    {
        $builder = $this->db->table('dtks_vv06');
        $builder->select('*');
        $builder->join('dtks_status', 'dtks_status.id_status = dtks_vv06.status');
        $builder->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_vv06.ket_verivali');
        $builder->orderBy('updated_at');
        // $builder->where(['ket_verivali' => 1]);
        $query = $builder->get();

        return $query;
    }

    function getTablesDtks()
    {
        // if(isset($_POST["length"]) && $_POST["length"] != -1);
        $request = service('request');
        $post_search = $request->getPost('search');
        $post_order = $request->getPost('order');
        $post_length = $request->getPost('length');
        $post_start = $request->getPost('start');
        $db = db_connect();
        $builder = $db->table('dtks_vv06');
        $query = $builder->select('*')
            ->limit($post_length, $post_start)
            ->get();
        return $query->getResult();
    }

    public function getData()
    {
        $jbt = (session()->get('jabatan'));
        return $this->db->table('dtks_vv06')
            ->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_vv06.ket_verivali')
            ->where(['rw' => $jbt])
            ->where(['cek_update' => '0'])
            ->orderBy('rt')
            ->get()
            ->getResultArray();
    }

    public function getDataInvalid()
    {
        $jbt = (session()->get('level'));
        return $this->db->table('dtks_pbi_jkn')
            ->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_pbi_jkn.ket_verivali')
            ->where(['rw' => $jbt])
            ->where(['status <=' => 1])
            ->where(['ket_verivali <=' => 2])
            ->orderBy('rt')
            ->get()
            ->getResultArray();
    }

    public function getInvalid()
    {
        $jbt = (session()->get('kode_desa'));
        // $whr = >=1 && <=2;
        $builder = $this->db->table('dtks_pbi_jkn');
        $builder->select('tb_villages.name as nama_desa, rw');
        $builder->where('kode_desa', $jbt);
        $builder->where('ket_verivali !=', 3);
        $builder->join('tb_villages', 'tb_villages.id = dtks_pbi_jkn.kode_desa');
        $builder->selectCount('ket_verivali');
        // $builder->join('tbl_rw', 'tbl_rw.no_rw = dtks_pbi_jkn.rw');
        // $builder->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_pbi_jkn.ket_verivali');
        // $builder->select('(SELECT COUNT(dtks_pbi_jkn.idv) FROM dtks_pbi_jkn WHERE dtks_pbi_jkn.rw=tbl_rw.no_rw && dtks_pbi_jkn.ket_verivali <= 2 && dtks_pbi_jkn.status <= 1) AS Inv', false);
        // $builder->where(['rw' => $jbt]);
        // $builder->where(['ket_verivali <=' =>  2]);
        // ->where(['ket_verivali' =>  2])
        $builder->groupBy(
            'nama_desa, rw'
        );
        $query = $builder->get();

        return $query;
        // ->getResultArray();
    }

    public function getDataNoAddress()
    {
        $jbt = (session()->get('level'));
        return $this->db->table('dtks_vv06')
            ->where(['rw' => ""])
            ->where(['cek_update' => '0'])
            ->get()
            ->getResultArray();
    }
    public function getIdDtks($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }
        return $this->where(['ids' => $id])->first();
    }

    public function getListVv()
    {
        $jbt = (session()->get('jabatan'));

        $db = db_connect();
        $builder = $db->table('dtks_vv06');
        $builder->where('rw', $jbt);

        $query = $builder->countAllResults();
        return $query;
    }
    public function getListSisaPerb()
    {
        $jbt = (session()->get('jabatan'));

        $db = db_connect();
        $builder = $db->table('dtks_vv06');
        $builder->where('rw', $jbt);
        $builder->where('cek_update', '>0');

        $query = $builder->countAllResults();
        return $query;
    }

    public function getDataRekRw()
    {
        $jbt = (session()->get('kode_desa'));

        $builder = $this->db->table('dtks_pbi_jkn');
        $builder->select('tb_villages.name as nama_desa, rw');
        $builder->where('kode_desa', $jbt);
        $builder->join('tb_villages', 'tb_villages.id = dtks_pbi_jkn.kode_desa');
        // $builder->selectCount('kode_desa');
        // $builder->selectCount('rw');
        $builder->selectCount('ket_verivali');
        $builder->selectSum('cek_update');
        // $builder->select('(SELECT COUNT(dtks_pbi_jkn.idv) FROM dtks_pbi_jkn WHERE dtks_pbi_jkn.rw=tbl_rw.no_rw) AS Vv', false);
        // $builder->select('(SELECT COUNT(dtks_pbi_jkn.idv) FROM dtks_pbi_jkn WHERE dtks_pbi_jkn.rw=tbl_rw.no_rw && dtks_pbi_jkn.cek_update != 0) AS Hsl', false);
        $builder->groupBy('nama_desa, rw');
        // $builder->orderBy('rw');
        $query = $builder->get();
        return $query;
    }

    public function getDataRW()
    {
        $builder = $this->db->table('dtks_pbi_jkn');
        $builder->select('rw');
        $builder->distinct();
        $builder->orderBy('rw', 'asc');

        $query = $builder->get();

        return $query;
    }

    public function getDataRT()
    {
        $builder = $this->db->table('dtks_pbi_jkn');
        $builder->select('rt');
        $builder->distinct();
        $builder->orderBy('rt', 'asc');

        $query = $builder->get();

        return $query;
    }

    public function getDataPisat()
    {
        $builder = $this->db->table('tb_pisat');
        // $builder->join('tb_pisat', 'tb_pisat.id=dtks_pbi_jkn.pisat');
        // $builder->distinct();

        $query = $builder->get()->getResultArray();

        // foreach ($query as $row) {
        // }

        return $query;
    }

    public function jml_grup()
    {
        $builder = $this->db->table('dtks_pbi_jkn');
        $builder->join('tb_villages', 'tb_villages.id=dtks_pbi_jkn.desa_kode');
        $builder->join('dtks_status', 'dtks_status.id_status=dtks_pbi_jkn.status');
        $builder->select('tb_villages.name, status, jenis_status');
        $builder->selectCount('desa_kode', 'jumlah');
        // $builder->distinct();
        $builder->groupBy('tb_villages.name, status, jenis_status');

        $query = $builder->get()->getResultArray();

        return $query;
    }

    public function jml_perdesa()
    {
        $builder = $this->db->table('dtks_pbi_jkn');
        $builder->select('tb_villages.name, status');
        $builder->join('tb_villages', 'tb_villages.id=dtks_pbi_jkn.desa_kode');

        $builder->selectCount('desa_kode', 'jumlah');
        $builder->where(['status' => 0]);
        $builder->selectCount('status');
        $builder->groupBy('tb_villages.name, status');
        $builder->orderBy('jumlah', 'desc');

        $query = $builder->get()->getResultArray();

        return $query;
    }

    public function jml_persentase()
    {
        $sql = 'SELECT tb_villages.name, desa_kode,
                    SUM(IF(`status` >= 0,1,0)) dataTarget,
                    SUM(IF(`status` > 0,1,0)) dataCapaian,
                    SUM(IF(`status` = 1,1,0)) aktif,
                    SUM(IF(`status` = 2,1,0)) meninggalDunia,
                    SUM(IF(`status` = 3,1,0)) ganda,
                    SUM(IF(`status` = 4,1,0)) pindah,
                    SUM(IF(`status` = 5,1,0)) tidakDitemukan,
                    SUM(IF(`status` = 7,1,0)) menolak,
                    ROUND(( SUM(IF(`status` > 0,1,0))/SUM(IF(`status` >= 0,1,0)) * 100 ),2) AS percentage
                FROM dtks_pbi_jkn
                JOIN tb_villages ON tb_villages.id=dtks_pbi_jkn.desa_kode
                GROUP BY tb_villages.name, desa_kode
                ORDER BY percentage DESC';

        // $query = $sql;
        $builder = $this->db->query($sql);
        $builder->getResult();
        $query = $builder->getResultArray();

        return $query;
    }

    public function perbaikanAll()
    {
        $builder = $this->db->table('dtks_pbi_jkn');
        $builder->select('(SUM(IF(`status` > 0,1,0))) dataCapaianAll');

        $query = $builder->get()->getResultArray();

        return $query;
    }
}
