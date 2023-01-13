<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class Usulan22Model extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'dtks_usulan22';
    protected $primaryKey = 'du_id';

    protected $allowedFields = ["du_nik", "program_bansos", "nokk", "nama", "tempat_lahir", "tanggal_lahir", "ibu_kandung", "jenis_kelamin", "jenis_pekerjaan", "status_kawin", "alamat", "rt", "rw", "provinsi", "kabupaten", "kecamatan", "kelurahan", "shdk", "foto_identitas", "foto_rumah", "disabil_status", "disabil_kode", "hamil_status", "hamil_tgl", "du_latitude", "du_longitude", "du_proses", "created_at", "created_at_year", "created_at_month", "created_by", "updated_at", "updated_by"];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $skipValidation     = false;

    var $column_order = array('', 'nama', 'nokk', 'du_nik', 'jenis_kelamin',  'alamat', 'shdk');

    var $order = array('dtks_usulan22.updated_at' => 'asc');


    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7)
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
            $kondisi_filter5 = " AND created_at_year = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND created_at_month = '$filter6'";
        }
        // du_proses
        if ($filter7 == "") {
            $kondisi_filter7 = "";
        } else {
            $kondisi_filter7 = " AND du_proses = '$filter7'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(nama LIKE '%$search%' OR nokk LIKE '%$search%' OR du_nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        } else {
            $kondisi_search = "dtks_usulan22.du_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
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
        $builder = $db->table('dtks_usulan22');
        $query = $builder->select('dtks_usulan22.du_id as idUsulan, tb_villages.name as namaDesa, tb_districts.name as namaKec, nama, nokk, dtks_usulan22.du_nik, jenis_kelamin, tempat_lahir, tanggal_lahir, ibu_kandung, jenis_pekerjaan, JenisPekerjaan, StatusKawin, dbj_nama_bansos, jenis_shdk, status_kawin, alamat, rt, rw, kelurahan, kecamatan, shdk, foto_rumah, created_at, created_at_year, created_at_month')
            ->join('tbl_pekerjaan', 'tbl_pekerjaan.idPekerjaan=dtks_usulan22.jenis_pekerjaan')
            ->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_usulan22.status_kawin')
            ->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id=dtks_usulan22.program_bansos')
            ->join('tb_shdk', 'tb_shdk.id=dtks_usulan22.shdk')
            ->join('tb_villages', 'tb_villages.id=dtks_usulan22.kelurahan')
            ->join('tb_districts', 'tb_districts.id=dtks_usulan22.kecamatan')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(du_id) as jml FROM dtks_usulan22";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7)
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
            $kondisi_filter5 = " AND created_at_year = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND created_at_month = '$filter6'";
        }
        // du_proses
        if ($filter7 == "") {
            $kondisi_filter7 = "";
        } else {
            $kondisi_filter7 = " AND du_proses = '$filter7'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (nama LIKE '%$search%' OR nokk LIKE '%$search%' OR du_nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        }

        $sQuery = "SELECT COUNT(du_id) as jml FROM dtks_usulan22 WHERE du_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function get_datatables01($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7)
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
            $kondisi_filter5 = " AND created_at_year = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND created_at_month = '$filter6'";
        }
        // du_proses
        if ($filter7 == "") {
            $kondisi_filter7 = "";
        } else {
            $kondisi_filter7 = " AND du_proses = '$filter7'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(nama LIKE '%$search%' OR nokk LIKE '%$search%' OR du_nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        } else {
            $kondisi_search = "dtks_usulan22.du_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
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
        $builder = $db->table('dtks_usulan22');
        $query = $builder->select('dtks_usulan22.du_id as idUsulan, tb_villages.name as namaDesa, tb_districts.name as namaKec, nama, nokk, dtks_usulan22.du_nik, jenis_kelamin, tempat_lahir, tanggal_lahir, ibu_kandung, jenis_pekerjaan, JenisPekerjaan, StatusKawin, dbj_nama_bansos, jenis_shdk, status_kawin, alamat, rt, rw, kelurahan, kecamatan, shdk, foto_rumah, created_at, created_at_year, created_at_month')
            ->join('tbl_pekerjaan', 'tbl_pekerjaan.idPekerjaan=dtks_usulan22.jenis_pekerjaan')
            ->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_usulan22.status_kawin')
            ->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id=dtks_usulan22.program_bansos')
            ->join('tb_shdk', 'tb_shdk.id=dtks_usulan22.shdk')
            ->join('tb_villages', 'tb_villages.id=dtks_usulan22.kelurahan')
            ->join('tb_districts', 'tb_districts.id=dtks_usulan22.kecamatan')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua01()
    {
        $sQuery = "SELECT COUNT(du_id) as jml FROM dtks_usulan22";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter01($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7)
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
            $kondisi_filter5 = " AND created_at_year = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND created_at_month = '$filter6'";
        }
        // du_proses
        if ($filter7 == "") {
            $kondisi_filter7 = "";
        } else {
            $kondisi_filter7 = " AND du_proses = '$filter7'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (nama LIKE '%$search%' OR nokk LIKE '%$search%' OR du_nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        }

        $sQuery = "SELECT COUNT(du_id) as jml FROM dtks_usulan22 WHERE du_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function index()
    {
        $this->db->setDatabase('db_bend');
        $builder = $this->db->table('dtks_usulan22');

        $post = $builder->get()->getResult();
        return $post;
    }

    public function dataExport($filter1, $filter4, $filter5, $filter6)
    {
        $builder = $this->db->table('dtks_usulan22');
        $builder->select('dbj_nama_bansos, tb_villages.name as desa, tb_districts.name as kec, tb_regencies.name as kab, tb_provinces.name as prov, nama, nokk, du_nik, NamaJenKel, tempat_lahir, tanggal_lahir, ibu_kandung, JenisPekerjaan, StatusKawin, jenis_shdk, status_kawin, alamat, rt, rw, kelurahan, kecamatan, dc_status, dj_kode, hamil_status, hamil_tgl, created_at, created_at_year, created_at_month');
        $builder->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id=dtks_usulan22.program_bansos', 'LEFT');
        $builder->join('tbl_pekerjaan', 'tbl_pekerjaan.idPekerjaan=dtks_usulan22.jenis_pekerjaan', 'LEFT');
        $builder->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_usulan22.status_kawin', 'LEFT');
        $builder->join('tb_disabil_jenis', 'tb_disabil_jenis.dj_id=dtks_usulan22.disabil_kode', 'LEFT');
        $builder->join('tb_disabil_cek', 'tb_disabil_cek.dc_id=dtks_usulan22.disabil_status', 'LEFT');
        $builder->join('tb_hamil_cek', 'tb_hamil_cek.hc_id=dtks_usulan22.hamil_status', 'LEFT');
        $builder->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=dtks_usulan22.jenis_kelamin', 'LEFT');
        $builder->join('tb_districts', 'tb_districts.id=dtks_usulan22.kecamatan', 'LEFT');
        $builder->join('tb_regencies', 'tb_regencies.id=dtks_usulan22.kabupaten', 'LEFT');
        $builder->join('tb_provinces', 'tb_provinces.id=dtks_usulan22.provinsi', 'LEFT');
        $builder->join('tb_villages', 'tb_villages.id=dtks_usulan22.kelurahan', 'LEFT');
        $builder->join('tb_shdk', 'tb_shdk.id=dtks_usulan22.shdk', 'LEFT');
        if ($filter1 !== "") {
            $builder->where('kelurahan', $filter1);
        }
        if ($filter4 !== "") {
            $builder->where('program_bansos', $filter4);
        }
        if ($filter5 !== "") {
            $builder->where('created_at_year', $filter5);
        }
        if ($filter6 !== "") {
            $builder->where('created_at_month', $filter6);
        }
        // $builder->orderBy('dtks_usulan22.du_id', 'asc');
        $query = $builder->get();

        return $query;
    }

    public function allExport($filter4, $filter5, $filter6)
    {
        // fil$filter1
        return $this->table('dtks_usulan22')
            ->select('dbj_nama_bansos, tb_villages.name as desa, tb_districts.name as kec, tb_regencies.name as kab, tb_provinces.name as prov, nama, nokk, du_nik, NamaJenKel, tempat_lahir, tanggal_lahir, ibu_kandung, JenisPekerjaan, StatusKawin, jenis_shdk, status_kawin, alamat, rt, rw, kelurahan, kecamatan, dc_status, dj_kode, hamil_status, hamil_tgl, created_at, created_at_year, created_at_month')
            ->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id=dtks_usulan22.program_bansos', 'LEFT')
            ->join('tbl_pekerjaan', 'tbl_pekerjaan.idPekerjaan=dtks_usulan22.jenis_pekerjaan', 'LEFT')
            ->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_usulan22.status_kawin', 'LEFT')
            ->join('tb_disabil_jenis', 'tb_disabil_jenis.dj_id=dtks_usulan22.disabil_kode', 'LEFT')
            ->join('tb_disabil_cek', 'tb_disabil_cek.dc_id=dtks_usulan22.disabil_status', 'LEFT')
            // ->join('tb_hamil_cek', 'tb_hamil_cek.hc_id=dtks_usulan22.hamil_status', 'LEFT')
            ->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=dtks_usulan22.jenis_kelamin', 'LEFT')
            ->join('tb_districts', 'tb_districts.id=dtks_usulan22.kecamatan', 'LEFT')
            ->join('tb_regencies', 'tb_regencies.id=dtks_usulan22.kabupaten', 'LEFT')
            ->join('tb_provinces', 'tb_provinces.id=dtks_usulan22.provinsi', 'LEFT')
            ->join('tb_villages', 'tb_villages.id=dtks_usulan22.kelurahan', 'LEFT')
            ->join('tb_shdk', 'tb_shdk.id=dtks_usulan22.shdk', 'LEFT')
            ->where('created_at_year =', $filter5)
            ->where('created_at_month =', $filter6)
            ->where('program_bansos =', $filter4)
            ->get();
    }

    public function getDtks()
    {
        return $this->db->table('dtks_usulan22')->get()->getResultArray();
    }

    public function getData()
    {
        $jbt = (session()->get('jabatan'));
        return $this->db->table('dtks_usulan22')
            ->where(['rw' => $jbt])
            ->where(['status' => 1])
            ->get()
            ->getResultArray();
    }

    public function getIdDtks($id = false)
    {
        $role_id = session()->get('role_id');
        $kelurahan = session()->get('kode_desa');
        $rw = session()->get('level');

        if ($id == false) {
            // return $this->findAll();
            // } elseif ($id !== false && $role_id <= '2') {
            //     return $this->db->table('dtks_usulan22')->where(['du_id' => $id]);
            // } elseif ($id !== false && $role_id == '3') {
            //     return $this->db->table('dtks_usulan22')->where(['du_id' => $id])->where(['kelurahan' => $kelurahan]);
            // } elseif ($id !== false && $role_id == '4') {
            //     return $this->db->table('dtks_usulan22')->where(['du_id' => $id])->where(['kelurahan' => $kelurahan])->where(['rw' => $rw]);
        } else {
            // denied
            return $this->db->table('dtks_usulan22')->where(['du_id' => $id]);
        }
    }

    public function rekapUsulan()
    {
        $year = date('Y');
        $month = date('n');

        $sql = 'SELECT tb_villages.name as namaDesa, kelurahan, created_at_year, created_at_month,
                    SUM(IF(`program_bansos` >= 0,1,0)) DataTarget,
                    SUM(IF(`program_bansos` > 0,1,0)) Capaian,
                    SUM(IF(`program_bansos` = 1,1,0)) PKH,
                    SUM(IF(`program_bansos` = 2,1,0)) BPNT,
                    SUM(IF(`program_bansos` = 3,1,0)) BST,
                    SUM(IF(`program_bansos` = 4,1,0)) NONBANSOS,
                    SUM(IF(`program_bansos` = 5,1,0)) PBI,
                    ROUND(( SUM(IF(`program_bansos` > 0,1,0))/SUM(IF(`program_bansos` >= 0,1,0)) * 100 ),2) AS percentage
                FROM dtks_usulan22
                JOIN tb_villages ON tb_villages.id = dtks_usulan22.kelurahan
                WHERE (created_at_year = ' . $year . ' AND created_at_month = ' . $month . ')
                GROUP BY namaDesa, kelurahan
                ORDER BY Capaian DESC';

        // $query = $sql;
        $builder = $this->db->query($sql);
        $query = $builder->getResult();
        // $query = $builder->getResultArray();

        return $query;
    }

    public function rekapUsulanBa()
    {
        $year = date('Y');
        $month = date('n');
        $kode_desa = session()->get('kode_desa');

        $sql = "SELECT tb_villages.name as nama_desa, kelurahan,
                    SUM(IF(`program_bansos` = 1,1,0)) pkh,
                    SUM(IF(`program_bansos` = 2,1,0)) bpnt,
                    SUM(IF(`program_bansos` = 3,1,0)) bst,
                    SUM(IF(`program_bansos` = 4,1,0)) nonbansos,
                    SUM(IF(`program_bansos` = 5,1,0)) pbi,
                    SUM(IF(`program_bansos` > 0,1,0)) AS total_usulan
                FROM dtks_usulan22
                JOIN tb_villages ON tb_villages.id = dtks_usulan22.kelurahan
                WHERE (kelurahan =  '" . $kode_desa . "'  AND created_at_year =  " . $year . "  AND created_at_month =  " . $month . " )";

        // $query = $sql;
        $builder = $this->db->query($sql);
        $query = $builder->getResultArray();
        // $query = $builder->getResultArray();

        return $query;
    }

    public function getBulan()
    {
        $builder = $this->db->table('dtks_usulan22');
        $builder->select('created_at');
        $builder->distinct('created_at');
        $query = $builder->get('vw_csv_report');

        return $query;
    }

    public function getHasilPencarian($cek_desa, $cek_nik)
    {
        $builder = $this->db->table('dtks_usulan22');
        $builder->select('*');
        $builder->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id = dtks_usulan22.program_bansos');
        $builder->join('tb_bulan', 'tb_bulan.tb_id = dtks_usulan22.created_at_month');
        $builder->where('kelurahan =', $cek_desa);
        $builder->where('du_nik =', $cek_nik);
        // join function bulan_ini
        // $builder->join('created_at_month =', $this->bulan_ini());

        $query = $builder->get();

        return $query;
    }
}
