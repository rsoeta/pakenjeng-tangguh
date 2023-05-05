<?php

namespace App\Models\Dtks\Ppks;

use CodeIgniter\Model;

class PpksModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'ppks_data';
    protected $primaryKey = 'ppks_id';

    protected $allowedFields = [
        "ppks_id",
        "ppks_kategori_id",
        "ppks_nama",
        "ppks_kecamatan",
        "ppks_kelurahan",
        "ppks_rw",
        "ppks_rt",
        "ppks_alamat",
        "ppks_nik",
        "ppks_nokk",
        "ppks_jenis_kelamin",
        "ppks_tempat_lahir",
        "ppks_tgl_lahir",
        "ppks_no_telp",
        "ppks_status_keberadaan",
        "ppks_status_bantuan",
        "ppks_status_panti",
        "ppks_tgl_out",
        "ppks_foto",
        "ppks_proses",
        "ppks_lat",
        "ppks_long",
        "ppks_created_at",
        "ppks_created_at_year",
        "ppks_created_at_month",
        "ppks_created_by",
        "ppks_updated_at",
        "ppks_updated_by",
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'ppks_created_at';
    protected $updatedField  = 'ppks_updated_at';
    // protected $deletedField  = 'deleted_at';

    protected $skipValidation     = false;

    var $column_order = array('', 'ppks_nama', 'ppks_nokk', 'ppks_nik', 'ppks_jenis_kelamin',  'ppks_alamat', 'ppks_status_bantuan');

    var $order = array('ppks_data.ppks_updated_at' => 'asc');


    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7)
    {
        // fil$filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND ppks_kelurahan = '$filter1'";
        }
        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND ppks_rw = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND ppks_rt = '$filter3'";
        }
        // rt
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND ppks_status_bantuan = '$filter4'";
        }
        // updated_at
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND ppks_created_at_year = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND ppks_created_at_month = '$filter6'";
        }
        // ppks_proses
        if ($filter7 == "") {
            $kondisi_filter7 = "";
        } else {
            $kondisi_filter7 = " AND ppks_proses = '$filter7'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(ppks_nama LIKE '%$search%' OR ppks_nokk LIKE '%$search%' OR ppks_nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        } else {
            $kondisi_search = "ppks_data.ppks_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
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
        $builder = $db->table('ppks_data');
        $query = $builder->select('ppks_data.ppks_id as id_ppks, tb_villages.name as namaDesa, tb_districts.name as namaKec, ppks_nama, ppks_nokk, ppks_data.ppks_nik, ppks_jenis_kelamin, ppks_tempat_lahir, ppks_tgl_lahir, dbj_nama_bansos, ppks_alamat, ppks_rt, ppks_rw, ppks_kelurahan, ppks_kecamatan, ppks_status_keberadaan, ppks_status_panti, ppks_foto,  ppks_data.ppks_created_at, ppks_created_at_year, ppks_created_at_month, ppks_created_by, dtks_users.email, ppks_data.ppks_updated_at, dtks_users.nope, dtks_users.fullname, ppks_proses')
            ->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id=ppks_data.ppks_status_bantuan')
            ->join('tb_villages', 'tb_villages.id=ppks_data.ppks_kelurahan')
            ->join('tb_districts', 'tb_districts.id=ppks_data.ppks_kecamatan')
            ->join('dtks_users', 'dtks_users.nik=ppks_data.ppks_created_by')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(ppks_id) as jml FROM ppks_data";
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
            $kondisi_filter1 = " AND ppks_kelurahan = '$filter1'";
        }
        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND ppks_rw = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND ppks_rt = '$filter3'";
        }
        // rt
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND ppks_status_bantuan = '$filter4'";
        }
        // rt
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND ppks_created_at_year = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND ppks_created_at_month = '$filter6'";
        }
        // ppks_proses
        if ($filter7 == "") {
            $kondisi_filter7 = "";
        } else {
            $kondisi_filter7 = " AND ppks_proses = '$filter7'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (ppks_nama LIKE '%$search%' OR ppks_nokk LIKE '%$search%' OR ppks_nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        }

        $sQuery = "SELECT COUNT(ppks_nik) as jml FROM ppks_data WHERE ppks_nik != '' $kondisi_search";
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
            $kondisi_filter1 = " AND ppks_kelurahan = '$filter1'";
        }
        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND ppks_rw = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND ppks_rt = '$filter3'";
        }
        // rt
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND ppks_status_bantuan = '$filter4'";
        }
        // updated_at
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND ppks_created_at_year = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND ppks_created_at_month = '$filter6'";
        }
        // proses
        if ($filter7 == "") {
            $kondisi_filter7 = "";
        } else {
            $kondisi_filter7 = " AND ppks_proses = '$filter7'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(ppks_nama LIKE '%$search%' OR ppks_nokk LIKE '%$search%' OR ppks_nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        } else {
            $kondisi_search = "ppks_data.ppks_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
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
        $builder = $db->table('ppks_data');
        $query = $builder->select('ppks_data.ppks_id as id_ppks, tb_villages.name as namaDesa, tb_districts.name as namaKec, ppks_nama, ppks_nokk, ppks_data.ppks_nik, ppks_jenis_kelamin, ppks_tempat_lahir, ppks_tgl_lahir, dbj_nama_bansos, ppks_alamat, ppks_rt, ppks_rw, ppks_kelurahan, ppks_kecamatan, ppks_status_keberadaan, ppks_status_panti, ppks_foto,  ppks_data.ppks_created_at, ppks_created_at_year, ppks_created_at_month, ppks_created_by, dtks_users.email, ppks_data.ppks_updated_at, dtks_users.nope, dtks_users.fullname, ppks_proses')
            ->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id=ppks_data.ppks_status_bantuan')
            ->join('tb_villages', 'tb_villages.id=ppks_data.ppks_kelurahan')
            ->join('tb_districts', 'tb_districts.id=ppks_data.ppks_kecamatan')
            ->join('dtks_users', 'dtks_users.nik=ppks_data.ppks_created_by')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua01()
    {
        $sQuery = "SELECT COUNT(ppks_id) as jml FROM ppks_data";
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
            $kondisi_filter1 = " AND ppks_kelurahan = '$filter1'";
        }
        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND ppks_rw = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND ppks_rt = '$filter3'";
        }
        // rt
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND ppks_status_bantuan = '$filter4'";
        }
        // rt
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND ppks_created_at_year = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND ppks_created_at_month = '$filter6'";
        }
        // ppks_proses
        if ($filter7 == "") {
            $kondisi_filter7 = "";
        } else {
            $kondisi_filter7 = " AND ppks_proses = '$filter7'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (ppks_nama LIKE '%$search%' OR ppks_nokk LIKE '%$search%' OR ppks_nik LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6 $kondisi_filter7";
        }

        $sQuery = "SELECT COUNT(ppks_nik) as jml FROM ppks_data WHERE ppks_nik != '' $kondisi_search";
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

    public function dataExport($filter1, $filter4, $filter5, $filter6, $filter7)
    {
        $builder = $this->db->table('ppks_data');
        $builder->select('ppks_rt, ppks_rw, ppks_kategori_id, ppks_nama, ppks_alamat, ppks_nik, ppks_nokk, NamaJenKel, ppks_tempat_lahir, ppks_tgl_lahir, ppks_no_telp, ppks_status_keberadaan, psk_nama_status, ppks_status_bantuan, dbj_nama_bansos, ppks_status_panti, pp_status_panti, ppks_foto, tb_villages.name as desa, ppks_created_at, ppks_updated_at, ppks_proses');
        $builder->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id=ppks_data.ppks_status_bantuan', 'LEFT');
        $builder->join('ppks_keberadaan', 'ppks_keberadaan.psk_id=ppks_data.ppks_status_keberadaan', 'LEFT');
        $builder->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=ppks_data.ppks_jenis_kelamin', 'LEFT');
        $builder->join('ppks_panti', 'ppks_panti.pp_id=ppks_data.ppks_status_panti', 'LEFT');
        $builder->join('tb_villages', 'tb_villages.id=ppks_data.ppks_kelurahan', 'LEFT');
        if ($filter1 !== "") {
            $builder->where('ppks_kelurahan', $filter1);
        }
        if ($filter4 !== "") {
            $builder->where('ppks_status_bantuan', $filter4);
        }
        if ($filter5 !== "") {
            $builder->where('ppks_created_at_year', $filter5);
        }
        if ($filter6 !== "") {
            $builder->where('ppks_created_at_month', $filter6);
        }
        if ($filter7 !== "") {
            $builder->where('ppks_proses', $filter7);
        }
        // $builder->orderBy('dtks_usulan22.du_id', 'asc');
        $query = $builder->orderBy('ppks_updated_at', 'ASC')->get();

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
        return $this->db->table('ppks_data')->get()->getResultArray();
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
