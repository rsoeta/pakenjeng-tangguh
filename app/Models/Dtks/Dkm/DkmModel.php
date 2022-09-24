<?php

namespace App\Models\Dtks\Dkm;

use CodeIgniter\Model;

class DkmModel extends Model
{
    protected $table      = 'dtks_dkm';
    protected $primaryKey = 'dd_id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        "dd_nik", "dd_nkk", "dd_nama", "dd_alamat", "dd_rt", "dd_rw", "dd_desa", "dd_kec", "dd_kab", "dd_adminduk", "dd_adminduk_foto", "dd_bpjs", "dd_bpjs_foto", "dd_blt", "dd_blt_deskripsi", "dd_blt_dd", "dd_blt_dd_deskripsi", "dd_bpnt", "dd_bpnt_deskripsi", "dd_pkh", "dd_pkh_deskripsi", "dd_foto_cpm", "dd_foto_rumah_depan", "dd_foto_rumah_belakang", "dd_foto_kk", "dd_latitude", "dd_longitude", "dd_status", "dd_created_by", "dd_created_at", "dd_updated_by", "dd_updated_at", "dd_deleted_by", "dd_deleted_at"
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'dd_created_at';
    protected $updatedField  = 'dd_updated_at';
    protected $deletedField  = 'dd_deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    var $column_order = array('', '', 'dd_nama', 'dd_nik', 'dd_alamat', 'dd_rt', 'dd_rw');

    var $order = array('dd_created_at' => 'asc');

    function get_datatables($filter0, $filter1, $filter2, $filter3)
    {
        // status
        if ($filter0 == "") {
            $kondisi_filter0 = "";
        } else {
            $kondisi_filter0 = " AND dd_status = '$filter0'";
        }

        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND dd_desa = '$filter1'";
        }
        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND dd_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND dd_rt = '$filter3'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(dd_nama LIKE '%$search%' OR dd_nik LIKE '%$search%' OR dd_nkk LIKE '%$search%' OR dd_alamat LIKE '%$search%') $kondisi_filter0 $kondisi_filter1 $kondisi_filter2 $kondisi_filter3";
        } else {
            $kondisi_search = "dd_id != '' $kondisi_filter0 $kondisi_filter1 $kondisi_filter2 $kondisi_filter3";
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
        $builder = $db->table('dtks_dkm');

        $query = $builder->select('*')
            // ->join('penduduk_hidup', 'penduduk_hidup.nik=dtks_dkm.dd_nik')
            // ->join('keluarga_aktif', 'keluarga_aktif.id=penduduk_hidup.id_kk')
            // ->join('tweb_wil_clusterdesa', 'tweb_wil_clusterdesa.id=penduduk_hidup.id_cluster')
            // ->join('ket_verivali', 'ket_verivali.idb_ketvv=individu_data.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();
        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(dd_id) as jml FROM dtks_dkm";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter0, $filter1, $filter2, $filter3)
    {
        // status
        if ($filter0 == "") {
            $kondisi_filter0 = "";
        } else {
            $kondisi_filter0 = " AND dd_status = '$filter0'";
        }
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND dd_desa = '$filter1'";
        }
        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND dd_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND dd_rt = '$filter3'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (dd_nama LIKE '%$search%' OR dd_nik LIKE '%$search%' OR dd_nkk LIKE '%$search%' OR dd_alamat LIKE '%$search%') $kondisi_filter0 $kondisi_filter1 $kondisi_filter2 $kondisi_filter3";
        } else {
            $kondisi_search = "$kondisi_filter0 $kondisi_filter1 $kondisi_filter2 $kondisi_filter3";
        }

        $sQuery = "SELECT COUNT(dd_id) as jml FROM dtks_dkm WHERE dd_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function getGambar($filter1 = false, $filter4)
    {
        $buildar = $this->db->table($this->table);
        $buildar->select('dd_nama, dd_nik, dd_alamat, dd_rt, dd_rw, dd_adminduk, dd_bpjs, dd_blt, dd_blt_dd, dd_pkh, dd_bpnt, dd_latitude, dd_longitude, dd_foto_cpm, dd_foto_rumah_depan, dd_foto_rumah_belakang, dd_foto_kk');
        $buildar->where('dd_desa', $filter1);
        $buildar->where('dd_status', $filter4);

        $query = $buildar->get();

        return $query->getResultArray();
    }
}
