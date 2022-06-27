<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class VerivaliGeoModel extends Model
{
    protected $table      = "dtks_verivali_geo";
    protected $primaryKey = "vg_id";

    protected $allowedFields = [
        'vg_nik',
        'vg_nama_lengkap',
        'vg_nkk',
        'vg_alamat',
        'vg_rw',
        'vg_rt',
        'vg_desa',
        'vg_kec',
        'vg_kab',
        'vg_prov',
        'vg_dbj_id1',
        'vg_dbj_id2',
        'vg_norek',
        'vg_source',
        'vg_fp',
        'vg_fr',
        'vg_lat',
        'vg_lang',
        'vg_ds_id',
        'vg_sta_id',
        'vg_created_by',
        'vg_created_at',
        'vg_updated_by',
        'vg_updated_at',
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'vg_updated_at';


    var $column_order = array('',  'vg_nik', 'vg_nama_lengkap', 'vg_nkk', 'vg_alamat',  'vg_dbj_id1', 'vg_norek');
    var $column_order2 = array('',  'vg_nik', 'vg_nama_lengkap', 'vg_nkk', 'vg_alamat',  'vg_dbj_id1', 'vg_norek');

    var $order = array('vg_nama_lengkap' => 'asc');

    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND vg_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND vg_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND vg_rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND vg_dbj_id1 = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND vg_sta_id = '$filter5'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(vg_nama_lengkap LIKE '%$search%' OR vg_nik LIKE '%$search%' OR vg_nkk LIKE '%$search%' OR vg_alamat LIKE '%$search%' OR vg_norek LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "vg_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
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
        $builder = $db->table('dtks_verivali_geo');
        $query = $builder->select(
            'vg_id, 
                    vg_nik,
                    vg_nama_lengkap,
                    vg_nkk,
                    vg_alamat,
                    vg_rw,
                    vg_rt,
                    vg_desa,
                    tb_villages.name as namaDesa,
                    dbj_nama_bansos,
                    sta_nama,
                    vg_kec,
                    vg_kab,
                    vg_prov,
                    vg_dbj_id1,
                    vg_dbj_id2,
                    vg_norek,
                    vg_source,
                    vg_fp,
                    vg_fr,
                    vg_lat,
                    vg_lang,
                    vg_ds_id,
                    vg_sta_id,
                    vg_created_by,
                    vg_created_at,
                    vg_updated_by,
                    vg_updated_at'
        )
            ->join('dtks_status', 'dtks_status.id_status = dtks_verivali_geo.vg_ds_id')
            ->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id = dtks_verivali_geo.vg_dbj_id1')
            ->join('tb_status', 'tb_status.sta_id = dtks_verivali_geo.vg_sta_id')
            ->join('tb_villages', 'tb_villages.id = dtks_verivali_geo.vg_desa')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(vg_id) as jml FROM dtks_verivali_geo";
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
            $kondisi_filter1 = " AND vg_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND vg_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND vg_rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND vg_dbj_id1 = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND vg_sta_id = '$filter5'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (vg_nama_lengkap LIKE '%$search%' OR vg_nik LIKE '%$search%' OR vg_nkk LIKE '%$search%' OR vg_alamat LIKE '%$search%' OR vg_norek LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        }

        $sQuery = "SELECT COUNT(vg_id) as jml FROM dtks_verivali_geo WHERE vg_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function get_datatables2($filter1, $filter2,  $filter3, $filter4, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND vg_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND vg_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND vg_ds_id = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND vg_dbj_id1 = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND vg_sta_id = '$filter5'";
        }
        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(vg_nama_lengkap LIKE '%$search%' OR vg_nik LIKE '%$search%' OR vg_nkk LIKE '%$search%' OR vg_alamat LIKE '%$search%' OR vg_norek LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "vg_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
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
        $builder = $db->table('dtks_verivali_geo');
        $query = $builder->select(
            'vg_id, 
                    vg_nik,
                    vg_nama_lengkap,
                    vg_nkk,
                    vg_alamat,
                    vg_rw,
                    vg_rt,
                    vg_desa,
                    tb_villages.name as namaDesa,
                    dbj_nama_bansos,
                    sta_nama,
                    vg_kec,
                    vg_kab,
                    vg_prov,
                    vg_dbj_id1,
                    vg_dbj_id2,
                    vg_norek,
                    vg_source,
                    vg_fp,
                    vg_fr,
                    vg_lat,
                    vg_lang,
                    vg_ds_id,
                    vg_sta_id,
                    vg_created_by,
                    vg_created_at,
                    vg_updated_by,
                    vg_updated_at'
        )
            ->join('dtks_status', 'dtks_status.id_status = dtks_verivali_geo.vg_ds_id')
            ->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id = dtks_verivali_geo.vg_dbj_id1')
            ->join('tb_status', 'tb_status.sta_id = dtks_verivali_geo.vg_sta_id')
            ->join('tb_villages', 'tb_villages.id = dtks_verivali_geo.vg_desa')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua2()
    {
        $sQuery = "SELECT COUNT(vg_id) as jml FROM dtks_verivali_geo";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter2($filter1, $filter2, $filter3, $filter4, $filter5)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND vg_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND vg_rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND vg_ds_id = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND vg_dbj_id1 = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND vg_sta_id = '$filter5'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (vg_nama LIKE '%$search%' OR vg_nik LIKE '%$search%' OR vg_nkk LIKE '%$search%' OR vg_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        }

        $sQuery = "SELECT COUNT(vg_id) as jml FROM dtks_verivali_geo WHERE vg_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function getVerivaliFix($filter1, $filter5)
    {

        $db = db_connect();
        $builder = $db->table('dtks_verivali_geo');
        $builder->select('vg_id, vg_nik, vg_nama_lengkap, vg_nkk, vg_alamat, vg_rw, vg_rt, vg_desa, tb_villages.name as namaDesa, tb_districts.name as namaKec, dbj_nama_bansos, sta_nama, vg_kec, vg_kab, vg_prov, vg_dbj_id1, vg_dbj_id2, vg_norek, vg_source, vg_fp, vg_fr, vg_lat, vg_lang, vg_ds_id, vg_sta_id, vg_created_by, vg_created_at, vg_updated_by, vg_updated_at');
        $builder->join('dtks_status', 'dtks_status.id_status = dtks_verivali_geo.vg_ds_id');
        $builder->join('dtks_bansos_jenis', 'dtks_bansos_jenis.dbj_id = dtks_verivali_geo.vg_dbj_id1');
        $builder->join('tb_status', 'tb_status.sta_id = dtks_verivali_geo.vg_sta_id');
        $builder->join('tb_villages', 'tb_villages.id = dtks_verivali_geo.vg_desa');
        $builder->join('tb_districts', 'tb_districts.id = dtks_verivali_geo.vg_kec');
        $builder->where('vg_desa', $filter1);
        $builder->where('vg_sta_id', $filter5);
        $builder->orderBy('vg_nama_lengkap', 'ASC');
        $query = $builder->get();

        return $query->getResultArray();
    }
}
