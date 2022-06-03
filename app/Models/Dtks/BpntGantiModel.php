<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class BpntGantiModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'dtks_bpnt_ganti';
    protected $primaryKey = 'dbg_id';

    protected $allowedFields = ["dbg_nkk", "dbg_nik", "dbg_nama", "dbg_tgl_lahir", "dbg_alamat", "dbg_program", "dbg_keterangan", "dbg_rt", "dbg_rw", "dbg_desa", "dbg_kecamatan", "dbg_nama_ibu", "dbg_status", "dbg_tgl_mati", "dbg_noreg_mati", "dbg_nkk_ganti", "dbg_nik_ganti", "dbg_created_at", "dbg_created_by", "dbg_updated_at", "dbg_updated_by"];

    protected $useTimestamps = false;
    protected $createdField  = 'dbg_created_at';
    protected $updatedField  = 'dbg_updated_at';
    protected $deletedField  = 'dbg_deleted_at';

    protected $skipValidation     = false;

    var $column_order = array("", "dbg_nkk", "dbg_nik", "dbg_nama", "dbg_alamat", "dbg_rt", "dbg_rw", "dbg_jenjang", "dbg_kelas", "dbg_partisipasi");

    var $order = array('dtks_bpnt_ganti.dbg_id' => 'asc');

    function get_datatables($filter1, $filter2, $filter3, $filter4)
    {
        // fil$filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND dbg_desa = '$filter1'";
        }

        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND dbg_nama_sekolah = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND dbg_jenjang = '$filter3'";
        }
        // rt
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND dbg_kelas = '$filter4'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(dbg_nama LIKE '%$search%' OR dbg_nik LIKE '%$search%' OR dbg_nkk LIKE '%$search%' OR dbg_nik LIKE '%$search%' OR dbg_nama_ibu LIKE '%$search%' OR dbg_nama_ayah LIKE '%$search%' OR dbg_nama_sekolah LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "dtks_bpnt_ganti.dbg_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
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
        $builder = $db->table('dtks_bpnt_ganti');
        $query = $builder->select('dbg_id, dbg_nkk, dbg_nik, dbg_nik, dbg_nama, dbg_tmp_lahir, dbg_tgl_lahir, dbg_alamat, dbg_rt, dbg_rw, dbg_nama_sekolah, dbg_desa, name, dbg_jenjang, sj_nama, dbg_kelas, dbg_partisipasi, ps_nama')
            ->join('tb_villages', 'tb_villages.id=dtks_bpnt_ganti.dbg_desa')
            ->join('tb_sekolah_jenjang', 'tb_sekolah_jenjang.sj_id=dtks_bpnt_ganti.dbg_jenjang')
            ->join('tb_sekolah_partisipasi', 'tb_sekolah_partisipasi.ps_id=dtks_bpnt_ganti.dbg_partisipasi')
            // ->join('tb_shdk', 'tb_shdk.id=dtks_bpnt_ganti.shdk')
            // ->join('tb_districts', 'tb_districts.id=dtks_bpnt_ganti.kecamatan')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(dbg_id) as jml FROM dtks_bpnt_ganti";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4)
    {
        // fil$filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND dbg_desa = '$filter1'";
        }

        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND dbg_nama_sekolah = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND dbg_jenjang = '$filter3'";
        }
        // rt
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND dbg_kelas = '$filter4'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (dbg_nama LIKE '%$search%' OR dbg_nik LIKE '%$search%' OR dbg_nkk LIKE '%$search%' OR dbg_nik LIKE '%$search%' OR dbg_nama_ibu LIKE '%$search%' OR dbg_nama_ayah LIKE '%$search%' OR dbg_nama_sekolah LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        }

        $sQuery = "SELECT COUNT(dbg_id) as jml FROM dtks_bpnt_ganti WHERE dbg_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    public function getKelas()
    {
        $builder = $this->db->table('dtks_bpnt_ganti');
        $builder->select('dbg_kelas');
        $builder->distinct();
        $builder->orderBy('dbg_kelas', 'asc');

        $sQuery = $builder->get();

        return $sQuery;
    }
    function index()
    {
        $this->db->setDatabase('db_bend');
        $builder = $this->db->table('dtks_bpnt_ganti');

        $post = $builder->get()->getResult();
        return $post;
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
        return $this->db->table('dtks_usulan21')->get()->getResultArray();
    }
    public function getData()
    {
        $jbt = (session()->get('jabatan'));
        return $this->db->table('dtks_usulan21')
            ->where(['rw' => $jbt])
            ->where(['status' => 1])
            ->get()
            ->getResultArray();
    }
    public function getIdDtks($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }
        return $this->where(['id' => $id])->first();
    }

    public function rekapUsulan()
    {
        $builder = $this->db->table('dtks_usulan21');
        // $builder->groupStart()
        //     ->where(['program_bansos' => '1', 'program_bansos' => '2'])
        //     ->groupEnd();
        // ->orWhere('kecamatan', '32.05.33');
        // $builder->select('tb_villages.name as NamaDesa, COUNT(*) AS Total');
        // $builder->select('dtks_usulan21.id as idUsul, tb_villages.name as NamaDesa, NamaBansos');
        // $builder->select('COUNT( program_bansos = 1 ) AS PKH, COUNT( program_bansos = 2 ) AS BPNT, COUNT( program_bansos = 3 ) AS BST, COUNT( program_bansos = 4 ) AS NONBANSOS, COUNT( program_bansos = 5 ) AS KIS, COUNT( program_bansos = 3 ) AS KIP, program_bansos');
        // $builder->where('kelurahan')
        // $builder->selectCount('dtks_usulan21.id', 'total_data');
        $builder->select('tb_villages.name as NamaDesa, NamaBansos, COUNT(NamaBansos) as Total');
        // $builder->selectCount('program_bansos');
        // $builder->selectCount('NamaBansos');
        // $builder->selectCount('dtks_usulan21.program_bansos');
        $builder->join('tb_villages', 'tb_villages.id=dtks_usulan21.kelurahan');
        $builder->join('dtks_bansos', 'dtks_bansos.Id=dtks_usulan21.program_bansos');
        // $builder->distinct('NamaDesa');
        $builder->groupBy('NamaDesa, NamaBansos');
        $builder->orderBy('NamaDesa');
        // $builder->groupBy('kelurahan');


        $query = $builder->get()->getResultArray();
        // $query = 'SELECT kelurahan, SUM( status = 2 ) AS active, SUM( status = 3 ) AS disabled, SUM( status = 5 ) AS archived
        // FROM geo
        // WHERE userId = 1 
        // GROUP BY typeId';

        return $query;

        // $sql = 'SELECT kelurahan, COUNT(*) AS "Total" FROM `dtks_usulan21` GROUP BY kelurahan';
        // $query = $this->db->query($sql);
        // $arr = $query->getResultArray();
        // $arr = explode(',', $arr['Program']);

        // dd($arr);
        // print_r($arr);
        // return $arr;
    }
}
