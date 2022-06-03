<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class UsulanModel extends Model
{
    protected $table      = 'dtks_usulan21';
    protected $primaryKey = 'id';

    protected $allowedFields = ["nik", "program_bansos", "nokk", "nama", "tempat_lahir", "tanggal_lahir", "ibu_kandung", "jenis_kelamin", "jenis_pekerjaan", "status_kawin", "alamat", "rt", "rw", "provinsi", "kabupaten", "kecamatan", "kelurahan", "shdk", "foto_rumah", "created_at", "created_by", "updated_at", "updated_by"];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $skipValidation     = false;

    var $column_order = array('', 'nokk', 'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'ibu_kandung', 'jenis_kelamin', 'jenis_pekerjaan', 'status_kawin', 'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi', 'shdk', 'created_at', 'updated_at');

    var $order = array('nokk' => 'asc');

    function get_datatables($filter1, $filter2, $filter3, $filter4)
    {
        // filter 1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND keluarahan = '$filter1'";
        }

        // filter 2
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }

        // filter 3
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // filter 4
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND program_bansos = '$filter4'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(nama LIKE '%$search%' OR nokk LIKE '%$search%' OR nik LIKE '%$search%' OR jenis_pekerjaan LIKE '%$search%' OR status_kawin LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "ID != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
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
        $builder->select('*');
        // ->join('tb_ket_bayar', 'tb_ket_bayar.KodeBayar=pbb_dhkp21.ket')
        // ->join('tbl_rt', 'tbl_rt.IdJenKel=pbb_dhkp21.JKAnak')
        // ->join('pekerjaan_kondisi_pekerjaan', 'pekerjaan_kondisi_pekerjaan.IDKondisi=individu_data.KondisiPekerjaan')
        // ->join('pendidikan_pend_tinggi', 'pendidikan_pend_tinggi.IDPendidikan=individu_data.PendTertinggi')
        // ->join('ket_verivali', 'ket_verivali.id_ketvv=individu_data.ket_verivali')
        $builder->where($kondisi_search);
        $builder->orderBy($result_order, $result_dir);
        $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(id) as jml FROM dtks_usulan21";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4)
    {
        // filter 1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND keluarahan = '$filter1'";
        }

        // filter 2
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }

        // filter 3
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // filter 4
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND program_bansos = '$filter4'";
        }


        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (nama LIKE '%$search%' OR nokk LIKE '%$search%' OR nik LIKE '%$search%' OR jenis_pekerjaan LIKE '%$search%' OR status_kawin LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        }

        $sQuery = "SELECT COUNT(id) as jml FROM dtks_usulan21 WHERE id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }


    function index()
    {
        $this->db->setDatabase('db_bend');
        $builder = $this->db->table('dtks_usulan');

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
        return $this->db->table('dtks_usulan')->get()->getResultArray();
    }
    public function getData()
    {
        $jbt = (session()->get('jabatan'));
        return $this->db->table('dtks_usulan')
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
}
