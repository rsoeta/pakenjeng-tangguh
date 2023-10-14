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

    protected $allowedFields = ["dk_nkk", "dk_nisn", "dk_kip", "dk_nama_siswa", "dk_jenkel", "dk_nik", "dk_tmp_lahir", "dk_tgl_lahir", "dk_alamat", "dk_rt", "dk_rw", "dk_desa", "dk_kecamatan", "dk_nama_ibu", "dk_nama_ayah", "dk_nama_sekolah", "dk_jenjang", "dk_kelas", "dk_partisipasi", "dk_foto_identitas", "dk_created_at", "dk_created_by", "dk_updated_at", "dk_updated_by"];

    protected $useTimestamps = false;
    protected $createdField  = 'dk_created_at';
    protected $updatedField  = 'dk_updated_at';
    protected $deletedField  = 'dk_deleted_at';

    protected $skipValidation     = false;

    var $column_order = array("", "dk_nama_siswa", "dk_nisn", "dk_nkk", "dk_alamat", "dk_rt", "dk_rw", "dk_kelas");

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
            $kondisi_search = "(dk_nama_siswa LIKE '%$search%' OR dk_nik LIKE '%$search%' OR dk_nkk LIKE '%$search%' OR dk_kip LIKE '%$search%' OR dk_nama_ibu LIKE '%$search%' OR dk_nama_ayah LIKE '%$search%' OR dk_nama_sekolah LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
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
        $query = $builder->select('dk_id, dk_nkk, dk_nisn, dk_kip, dk_nik, dk_nama_siswa, dk_tmp_lahir, dk_tgl_lahir, dk_alamat, dk_rt, dk_rw, dk_nama_sekolah, dk_desa, name, dk_jenjang, dk_kelas, dk_partisipasi, dk_foto_identitas')
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
            $kondisi_search = "AND (dk_nama_siswa LIKE '%$search%' OR dk_nik LIKE '%$search%' OR dk_nkk LIKE '%$search%' OR dk_kip LIKE '%$search%' OR dk_nama_ibu LIKE '%$search%' OR dk_nama_ayah LIKE '%$search%' OR dk_nama_sekolah LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
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

    public function dataExport($filter1, $filter2, $filter3)
    {
        $builder = $this->db->table('dtks_kip');
        $builder->select('dk_nkk, dk_nisn, dk_kip, dk_nama_siswa, dk_jenkel, dk_nik, dk_tmp_lahir, dk_tgl_lahir, dk_alamat, dk_rt, dk_rw, dk_desa, dk_kecamatan, dk_nama_ibu, dk_nama_ayah, dk_nama_sekolah, dk_jenjang, dk_kelas, dk_partisipasi, dk_foto_identitas, NamaJenKel, tb_villages.name as namaDesa, tb_districts.name as namaKec');
        $builder->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=dtks_kip.dk_jenkel', 'LEFT');
        $builder->join('tb_districts', 'tb_districts.id=dtks_kip.dk_kecamatan', 'LEFT');
        $builder->join('tb_villages', 'tb_villages.id=dtks_kip.dk_desa', 'LEFT');
        if ($filter1 !== "") {
            $builder->where('dk_desa', $filter1);
        }
        if ($filter2 !== "") {
            $builder->where('dk_rw', $filter2);
        }
        if ($filter3 !== "") {
            $builder->where('dk_kelas', $filter3);
        }
        $builder->orderBy('dtks_kip.dk_nkk', 'asc');
        $query = $builder->get();

        return $query;
    }
}
