<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class Usulan21Model extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'dtks_usulan21';
    protected $primaryKey = 'id';

    protected $allowedFields = ["nik", "program_bansos", "nokk", "nama", "tempat_lahir", "tanggal_lahir", "ibu_kandung", "jenis_kelamin", "jenis_pekerjaan", "status_kawin", "alamat", "rt", "rw", "provinsi", "kabupaten", "kecamatan", "kelurahan", "shdk", "foto_rumah", "created_at", "created_by", "updated_at", "updated_by"];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $skipValidation     = false;

    var $column_order = array('', 'nokk', 'nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'ibu_kandung',  'alamat', 'rt', 'rw',  'shdk', 'created_at', 'updated_at');

    var $order = array('dtks_usulan21.id' => 'asc');

    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter5)
    {
        // fil$filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND kelurahan = '$filter1'";
        }

        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // rt
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND program_bansos = '$filter4'";
        }
        // updated_at
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND updated_at = '$filter5'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(nama LIKE '%$search%' OR nokk LIKE '%$search%' OR nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "dtks_usulan21.id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
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
        $builder = $db->table('dtks_usulan21');
        $query = $builder->select('dtks_usulan21.id as idUsulan, tb_villages.name as namaDesa, tb_districts.name as namaKec, nama, nokk, dtks_usulan21.nik, jenis_kelamin, tempat_lahir, tanggal_lahir, ibu_kandung, jenis_pekerjaan, JenisPekerjaan, StatusKawin, jenis_shdk, status_kawin, alamat, rt, rw, kelurahan, kecamatan, shdk, foto_rumah, updated_at')
            ->join('tbl_pekerjaan', 'tbl_pekerjaan.idPekerjaan=dtks_usulan21.jenis_pekerjaan')
            ->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_usulan21.status_kawin')
            ->join('tb_shdk', 'tb_shdk.id=dtks_usulan21.shdk')
            ->join('tb_villages', 'tb_villages.id=dtks_usulan21.kelurahan')
            ->join('tb_districts', 'tb_districts.id=dtks_usulan21.kecamatan')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(id) as jml FROM dtks_usulan21";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5)
    {
        // fil$filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND kelurahan = '$filter1'";
        }

        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // rt
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND program_bansos = '$filter4'";
        }
        // rt
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND updated_at = '$filter5'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (nama LIKE '%$search%' OR nokk LIKE '%$search%' OR nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        }

        $sQuery = "SELECT COUNT(id) as jml FROM dtks_usulan21 WHERE id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function index()
    {
        $this->db->setDatabase('db_bend');
        $builder = $this->db->table('dtks_usulan21');

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

    public function getBulan()
    {
        $builder = $this->db->table('dtks_usulan21');
        $builder->select('updated_at');
        $builder->distinct('updated_at');
        $query = $builder->get();

        return $query;
    }
}
