<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class KipModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'dtks_kip';
    protected $primaryKey = 'dk_id';

    protected $allowedFields = ["dk_kks", "dk_nisn", "dk_kip", "dk_nama_siswa", "dk_jenkel", "dk_nik", "dk_tmp_lahir", "dk_tgl_lahir", "dk_alamat", "dk_rt", "dk_rw", "dk_desa", "dk_kecamatan", "dk_nama_ibu", "dk_nama_ayah", "dk_nama_sekolah", "dk_jenjang", "dk_kelas", "dk_partisipasi", "dk_foto_identitas", "dk_created_at", "dk_created_by", "dk_updated_at", "dk_updated_by"];

    protected $useTimestamps = false;
    protected $createdField  = 'dk_created_at';
    protected $updatedField  = 'dk_updated_at';
    protected $deletedField  = 'dk_deleted_at';

    protected $skipValidation     = false;

    var $column_order = array("", "dk_nama_siswa", "dk_nisn", "dk_kks", "dk_alamat", "dk_rt", "dk_rw", "dk_kelas");

    var $order = array('dtks_kip.dk_updated_at' => 'asc');

    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6)
    {
        // filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND dk_desa = '$filter1'";
        }

        // filter2
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND dk_rw = '$filter2'";
        }

        // filter3
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND dk_rt = '$filter3'";
        }

        // filter4
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND dk_nama_sekolah = '$filter4'";
        }

        // filter5
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND dk_jenjang = '$filter5'";
        }

        // filter6
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND dk_kelas = '$filter6'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(dk_nama_siswa LIKE '%$search%' OR dk_nik LIKE '%$search%' OR dk_kks LIKE '%$search%' OR dk_kip LIKE '%$search%' OR dk_nama_ibu LIKE '%$search%' OR dk_nama_ayah LIKE '%$search%' OR dk_nama_sekolah LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        } else {
            $kondisi_search = "dtks_kip.dk_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
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
        $builder = $db->table('dtks_kip');
        $query = $builder->select('dk_id, dk_kks, dk_nisn, dk_kip, dk_nik, dk_nama_siswa, dk_tmp_lahir, dk_tgl_lahir, dk_alamat, dk_rt, dk_rw, dk_nama_sekolah, dk_desa, name, dk_jenjang, dk_kelas, dk_partisipasi, dk_foto_identitas')
            ->join('tb_villages', 'tb_villages.id=dtks_kip.dk_desa')
            // ->join('tb_sekolah_jenjang', 'tb_sekolah_jenjang.sj_id=dtks_kip.dk_jenjang')
            // ->join('tb_sekolah_partisipasi', 'tb_sekolah_partisipasi.ps_id=dtks_kip.dk_partisipasi')
            // ->join('tb_shdk', 'tb_shdk.id=dtks_kip.shdk')
            // ->join('tb_districts', 'tb_districts.id=dtks_kip.kecamatan')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(dk_id) as jml FROM dtks_kip";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6)
    {
        // filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND dk_desa = '$filter1'";
        }
        // filter2
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND dk_rw = '$filter2'";
        }
        // filter3
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND dk_rt = '$filter3'";
        }

        // filter4
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND dk_nama_sekolah = '$filter4'";
        }
        // filter5
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND dk_jenjang = '$filter5'";
        }
        // filter6
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND dk_kelas = '$filter6'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (dk_nama_siswa LIKE '%$search%' OR dk_nik LIKE '%$search%' OR dk_kks LIKE '%$search%' OR dk_kip LIKE '%$search%' OR dk_nama_ibu LIKE '%$search%' OR dk_nama_ayah LIKE '%$search%' OR dk_nama_sekolah LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        }

        $sQuery = "SELECT COUNT(dk_id) as jml FROM dtks_kip WHERE dk_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    public function getKelas()
    {
        $builder = $this->db->table('dtks_kip');
        $builder->select('dk_kelas');
        $builder->distinct();
        $builder->orderBy('dk_kelas', 'asc');

        $sQuery = $builder->get();

        return $sQuery;
    }
    function index()
    {
        $this->db->setDatabase('db_bend');
        $builder = $this->db->table('dtks_kip');

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
