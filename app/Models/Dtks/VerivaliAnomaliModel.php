<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class VerivaliAnomaliModel extends Model
{
    protected $table      = "dtks_verivali_anomali";
    protected $primaryKey = "va_id";

    protected $allowedFields = [
        'va_id',
        'va_id_dtks',
        'va_nik',
        'va_nama',
        'va_nkk',
        'va_tmp_lhr',
        'va_tgl_lhr',
        'va_alamat',
        'va_prov',
        'va_kab',
        'va_kec',
        'va_desa',
        'va_jk',
        'va_ibu',
        'va_pekerjaan',
        'va_rw',
        'va_nama_anomali',
        'va_status',
        'va_ds_id',
        'va_creator',
        'va_updated_at',
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'va_updated_at';


    var $column_order = array('',  'db_nik', 'db_nama', 'db_nkk', 'db_alamat',  'db_tmp_lahir', 'db_tgl_lahir', 'db_ibu_kandung', 'va_nama_anomali');
    var $column_order2 = array('',  'va_nik', 'va_nama', 'va_nkk', 'va_alamat',  'va_tmp_lhr', 'va_tgl_lhr', 'va_ibu', 'va_nama_anomali', 'va_ds_id');

    var $order = array('va_updated_at' => 'desc');

    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter5)
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
            $kondisi_filter4 = " AND va_nama_anomali = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND va_status = '$filter5'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "va_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
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
        $builder = $db->table('vw_verivali_anomali');
        $query = $builder->select('*')
            ->join('dtks_status2', 'dtks_status2.id_status = vw_verivali_anomali.va_ds_id')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(va_id) as jml FROM vw_verivali_anomali";
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
            $kondisi_filter4 = " AND va_nama_anomali = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND va_status = '$filter5'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5";
        }

        $sQuery = "SELECT COUNT(va_id) as jml FROM vw_verivali_anomali WHERE va_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function get_datatables2($filter1, $filter2, $filter4, $filter5, $filter6)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND va_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND va_rw = '$filter2'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND va_nama_anomali = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND va_status = '$filter5'";
        }
        // status
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND va_ds_id = '$filter6'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(va_nama LIKE '%$search%' OR va_nik LIKE '%$search%' OR va_nkk LIKE '%$search%' OR va_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        } else {
            $kondisi_search = "va_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        }

        // order
        if (isset($_POST['order'])) {
            $result_order = $this->column_order2[$_POST['order']['0']['column']];
            $result_dir = $_POST['order']['0']['dir'];
        } else if ($this->order) {
            $order = $this->order;
            $result_order = key($order);
            $result_dir = $order[key($order)];
        }

        if ($_POST['length'] != -1);
        $db = db_connect();
        $builder = $db->table('dtks_verivali_anomali');
        $query = $builder->select('*')
            ->join('tbl_jenkel', 'tbl_jenkel.IdJenKel = dtks_verivali_anomali.va_jk')
            ->join('tb_status', 'tb_status.sta_id = dtks_verivali_anomali.va_status')
            ->join('tb_penduduk_pekerjaan', 'tb_penduduk_pekerjaan.pk_id = dtks_verivali_anomali.va_pekerjaan')
            ->join('dtks_status2', 'dtks_status2.id_status = dtks_verivali_anomali.va_ds_id')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua2()
    {
        $sQuery = "SELECT COUNT(va_id) as jml FROM dtks_verivali_anomali";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter2($filter1, $filter2, $filter4, $filter5, $filter6)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND va_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND va_rw = '$filter2'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND va_nama_anomali = '$filter4'";
        }
        // status
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND va_status = '$filter5'";
        }
        // status
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND va_ds_id = '$filter6'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (va_nama LIKE '%$search%' OR va_nik LIKE '%$search%' OR va_nkk LIKE '%$search%' OR va_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        }

        $sQuery = "SELECT COUNT(va_id) as jml FROM dtks_verivali_anomali WHERE va_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }
}
