<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class BnbaModel extends Model
{
    protected $table      = 'dtks_bnba';
    protected $primaryKey = 'db_id';

    protected $allowedFields = [
        "db_id_dtks", "db_province", "db_regency", "db_district", "db_village", "db_alamat", "db_dusun", "db_rw", "db_rt", "db_nkk", "db_nik", "db_nama", "db_tgl_lahir", "db_tmp_lahir", "db_jenkel_id", "db_ibu_kandung", "db_shdk_id", "db_status", "db_tgl_kejadian", "db_noreg_kejadian", "db_tb_status", "db_pkh", "db_bpnt", "db_bst", "db_bpnt_ppkm", "db_pbi", "db_creator", "db_created", "db_modifier", "db_modified"
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'db_created';
    protected $updatedField  = 'db_modified';


    var $column_order = array('', '', 'db_nama', 'db_nkk', 'db_nik', 'db_jenkel_id', 'db_tmp_lahir', 'db_tgl_lahir', 'db_shdk_id');
    var $column_order1 = array('', '', 'db_nama', 'db_nkk', 'db_nik', 'db_jenkel_id', 'db_tmp_lahir', 'db_tgl_lahir', 'db_modified');

    var $order = array('db_nkk' => 'asc', 'db_shdk_id' => 'asc');
    var $order1 = array('db_modified' => 'asc');

    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter0)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND db_village = '$filter1'";
        }
        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND db_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND db_rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND db_shdk_id = '$filter4'";
        }
        // status
        if ($filter0 == "") {
            $kondisi_filter0 = "";
        } else {
            $kondisi_filter0 = " AND db_status = '$filter0'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0";
        } else {
            $kondisi_search = "db_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0";
        }

        // order
        if (isset($_POST['order'])) {
            $result_order = $this->column_order1[$_POST['order']['0']['column']];
            $result_dir = $_POST['order']['0']['dir'];
        } else if ($this->order1) {
            $order = $this->order1;
            $result_order = key($order);
            $result_dir = $order[key($order)];
        }

        // // order
        // if (isset($_POST['order'])) {
        //     $result_order = $this->column_order[$_POST['order']['0']['column']];
        //     $result_dir = $_POST['order']['0']['dir'];
        // } else if ($this->order) {
        //     $result_order = key($this->order);
        //     $result_dir = $this->order[key($this->order)];
        // }


        if ($_POST['length'] != -1);
        $db = db_connect();
        $builder = $db->table('dtks_bnba');
        $query = $builder->select('*')
            ->join('tb_shdk', 'tb_shdk.id=dtks_bnba.db_shdk_id')
            ->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=dtks_bnba.db_jenkel_id')
            ->join('dtks_status', 'dtks_status.id_status=dtks_bnba.db_status')
            // ->join('ket_verivali', 'ket_verivali.idb_ketvv=individu_data.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();
        if (!$query) {
            die($db->getError()); // Tampilkan pesan kesalahan jika terjadi
        }
        // var_dump($result_order, $result_dir);
        // die;
        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(db_id) as jml FROM dtks_bnba";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter0)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND db_village = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND db_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND db_rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND db_shdk_id = '$filter4'";
        }
        // status
        if ($filter0 == "") {
            $kondisi_filter0 = "";
        } else {
            $kondisi_filter0 = " AND db_status = '$filter0'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0";
        }

        $sQuery = "SELECT COUNT(db_id) as jml FROM dtks_bnba WHERE db_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function get_datatables1($filter1, $filter2, $filter3, $filter4, $filter0, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND db_village = '$filter1'";
        }
        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND db_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND db_rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND db_shdk_id = '$filter4'";
        }
        // status
        if ($filter0 == "") {
            $kondisi_filter0 = "";
        } else {
            $kondisi_filter0 = " AND db_status = '$filter0'";
        }

        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND db_tb_status = '$filter5'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0 $kondisi_filter5";
        } else {
            $kondisi_search = "db_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0 $kondisi_filter5";
        }

        // order
        if (isset($_POST['order'])) {
            $result_order = $this->column_order1[$_POST['order']['0']['column']];
            $result_dir = $_POST['order']['0']['dir'];
        } else if ($this->order1) {
            $order1 = $this->order1;
            $result_order = key($order1);
            $result_dir = $order1[key($order1)];
        }

        if ($_POST['length'] != -1);
        $db = db_connect();
        $builder = $db->table('dtks_bnba');
        $query = $builder->select('*')
            ->join('tb_shdk', 'tb_shdk.id=dtks_bnba.db_shdk_id')
            ->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=dtks_bnba.db_jenkel_id')
            ->join('dtks_status', 'dtks_status.id_status=dtks_bnba.db_status')
            ->join('tb_villages', 'tb_villages.id=dtks_bnba.db_village')
            // ->join('ket_verivali', 'ket_verivali.idb_ketvv=individu_data.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua1()
    {
        $sQuery = "SELECT COUNT(db_id) as jml FROM dtks_bnba";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter1($filter1, $filter2, $filter3, $filter4, $filter0, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND db_village = '$filter1'";
        }
        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND db_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND db_rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND db_shdk_id = '$filter4'";
        }
        // status
        if ($filter0 == "") {
            $kondisi_filter0 = "";
        } else {
            $kondisi_filter0 = " AND db_status = '$filter0'";
        }

        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND db_tb_status = '$filter5'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0 $kondisi_filter5";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0 $kondisi_filter5";
        }

        $sQuery = "SELECT COUNT(db_id) as jml FROM dtks_bnba WHERE db_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function get_datatables2($filter1, $filter2, $filter3, $filter4, $filter0, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND db_village = '$filter1'";
        }
        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND db_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND db_rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND db_shdk_id = '$filter4'";
        }
        // status
        if ($filter0 == "") {
            $kondisi_filter0 = "";
        } else {
            $kondisi_filter0 = " AND db_status = '$filter0'";
        }

        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND db_tb_status = '$filter5'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0 $kondisi_filter5";
        } else {
            $kondisi_search = "db_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0 $kondisi_filter5";
        }

        // order
        if (isset($_POST['order'])) {
            $result_order = $this->column_order1[$_POST['order']['0']['column']];
            $result_dir = $_POST['order']['0']['dir'];
        } else if ($this->order1) {
            $order1 = $this->order1;
            $result_order = key($order1);
            $result_dir = $order1[key($order1)];
        }

        if ($_POST['length'] != -1);
        $db = db_connect();
        $builder = $db->table('dtks_bnba');
        $query = $builder->select('*')
            ->join('tb_shdk', 'tb_shdk.id=dtks_bnba.db_shdk_id')
            ->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=dtks_bnba.db_jenkel_id')
            ->join('dtks_status', 'dtks_status.id_status=dtks_bnba.db_status')
            ->join('tb_villages', 'tb_villages.id=dtks_bnba.db_village')
            // ->join('ket_verivali', 'ket_verivali.idb_ketvv=individu_data.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua2()
    {
        $sQuery = "SELECT COUNT(db_id) as jml FROM dtks_bnba";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter2($filter1, $filter2, $filter3, $filter4, $filter0, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND db_village = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND db_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND db_rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND db_shdk_id = '$filter4'";
        }
        // status
        if ($filter0 == "") {
            $kondisi_filter0 = "";
        } else {
            $kondisi_filter0 = " AND db_status = '$filter0'";
        }

        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND db_tb_status = '$filter5'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0 $kondisi_filter5";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter0 $kondisi_filter5";
        }

        $sQuery = "SELECT COUNT(db_id) as jml FROM dtks_bnba WHERE db_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function getDataRow($ids)
    {
        $builder = $this->db->table($this->table);
        $query = $builder->getWhere(['ids' => $ids]);

        return $query->getRow();
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
        $builder->join('dtks_status', 'dtks_status.idb_status = dtks_vv06.status');
        $builder->join('ket_verivali', 'ket_verivali.idb_ketvv = dtks_vv06.ket_verivali');
        $builder->orderBy('updatedb_at');
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
            ->join('ket_verivali', 'ket_verivali.idb_ketvv = dtks_vv06.ket_verivali')
            ->where(['rw' => $jbt])
            ->where(['cek_update' => '0'])
            ->orderBy('rt')
            ->get()
            ->getResultArray();
    }

    public function getDataInvalid()
    {
        $jbt = (session()->get('level'));
        return $this->db->table('dtks_verivali09')
            ->join('ket_verivali', 'ket_verivali.idb_ketvv = dtks_verivali09.ket_verivali')
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
        $builder = $this->db->table('dtks_verivali09');
        $builder->select('tb_villages.name as nama_desa, rw');
        $builder->where('kode_desa', $jbt);
        $builder->where('ket_verivali !=', 3);
        $builder->join('tb_villages', 'tb_villages.id = dtks_verivali09.kode_desa');
        $builder->selectCount('ket_verivali');
        // $builder->join('tbl_rw', 'tbl_rw.db_rw = dtks_verivali09.rw');
        // $builder->join('ket_verivali', 'ket_verivali.idb_ketvv = dtks_verivali09.ket_verivali');
        // $builder->select('(SELECT COUNT(dtks_verivali09.idv) FROM dtks_verivali09 WHERE dtks_verivali09.rw=tbl_rw.db_rw && dtks_verivali09.ket_verivali <= 2 && dtks_verivali09.status <= 1) AS Inv', false);
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

        $builder = $this->db->table('dtks_verivali09');
        $builder->select('tb_villages.name as nama_desa, rw');
        $builder->where('kode_desa', $jbt);
        $builder->join('tb_villages', 'tb_villages.id = dtks_verivali09.kode_desa');
        // $builder->selectCount('kode_desa');
        // $builder->selectCount('rw');
        $builder->selectCount('ket_verivali');
        $builder->selectSum('cek_update');
        // $builder->select('(SELECT COUNT(dtks_verivali09.idv) FROM dtks_verivali09 WHERE dtks_verivali09.rw=tbl_rw.db_rw) AS Vv', false);
        // $builder->select('(SELECT COUNT(dtks_verivali09.idv) FROM dtks_verivali09 WHERE dtks_verivali09.rw=tbl_rw.db_rw && dtks_verivali09.cek_update != 0) AS Hsl', false);
        $builder->groupBy('nama_desa, rw');
        // $builder->orderBy('rw');
        $query = $builder->get();
        return $query;
    }

    public function getDataRW()
    {
        $builder = $this->db->table('dtks_bnba');
        $builder->select('db_rw');
        $builder->distinct();
        $builder->orderBy('db_rw', 'asc');

        $query = $builder->get();

        return $query;
    }

    public function getDataRT()
    {
        $builder = $this->db->table('dtks_bnba');
        $builder->select('db_rt');
        $builder->distinct();
        $builder->orderBy('db_rt', 'asc');

        $query = $builder->get();

        return $query;
    }

    public function getDataJenkel()
    {
        $builder = $this->db->table('tbl_jenkel');
        $query = $builder->get()->getResultArray();

        // foreach ($query as $row) {
        // }

        return $query;
    }

    public function getDataShdk()
    {
        $builder = $this->db->table('tb_shdk');
        $query = $builder->get()->getResultArray();

        // foreach ($query as $row) {
        // }

        return $query;
    }

    function countStatusBnba()
    {
        $sql = 'SELECT tb_villages.name as nama_desa, 
                    SUM(IF(`db_status` = 0,1,0)) "Tidak Aktif",
                    SUM(IF(`db_status` = 1,1,0)) Aktif,
                    SUM(IF(`db_status` = 2,1,0)) "Meninggal Dunia",
                    SUM(IF(`db_status` = 3,1,0)) "Ganda",
                    SUM(IF(`db_status` = 4,1,0)) "Pindah",
                    SUM(IF(`db_status` = 5,1,0)) "Tidak Ditemukan",
                    SUM(IF(`db_status` = 6,1,0)) "Sudah Mampu/Menolak",
                    SUM(IF(`db_status` >= 0,1,0)) DataTarget,
                    SUM(IF(`db_status` >= 1,1,0)) Capaian,
                    ROUND(( SUM(IF(`db_status` > 1,1,0))/SUM(IF(`db_status` > 0,1,0)) * 100 ),2) AS percentage
                FROM dtks_bnba
                JOIN tb_villages ON tb_villages.id = dtks_bnba.db_village
                GROUP BY nama_desa            
                ORDER BY nama_desa ASC';

        // $query = $sql;
        $builder = $this->db->query($sql);
        $query = $builder->getResult();
        // $query = $builder->getResultArray();

        return $query;
    }
}
