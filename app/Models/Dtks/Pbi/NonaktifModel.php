<?php

namespace App\Models\Dtks\Pbi;

use CodeIgniter\Model;

class NonaktifModel extends Model
{
    protected $table      = "dtks_pbi_nonaktif";
    protected $primaryKey = "dpn_id";

    protected $allowedFields = ['dpn_id', 'dpn_noka_kis', 'dpn_ps_noka', 'dpn_nama_kis', 'dpn_alamat_kis', 'dpn_rt_kis', 'dpn_rw_kis', 'dpn_tmp_lhr_kis', 'dpn_tgl_lhr_kis', 'dpn_nik_kis', 'dpn_faskes_kis', 'dpn_nik_pm', 'dpn_nama_pm', 'dpn_nkk_pm', 'dpn_alamat_pm', 'dpn_rt_pm', 'dpn_rw_pm', 'dpn_kode_desa', 'dpn_tmp_lhr_pm', 'dpn_tgl_lhr_pm', 'dpn_created_by', 'dpn_created_at', 'dpn_updated_by', 'dpn_updated_at'];

    protected $useTimestamps = true;

    var $column_order = array('', 'dpn_nama_kis', 'dpn_noka_kis',  'dpn_ps_noka', 'dpn_nik_kis', 'dpn_tgl_lhr_kis', 'dpn_alamat_kis', 'dpn_nik_pm', 'dpn_nama_pm', 'dpn_nkk_pm', 'dpn_tgl_lhr_pm', 'dpn_alamat_pm');

    var $order = array('dpn_id' => 'desc');

    function get_datatables($filter1, $filter2, $filter3)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND dpn_kode_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND dpn_rw_pm = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND dpn_rt_pm = '$filter3'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(dpn_noka_kis LIKE '%$search%' OR dpn_nama_kis LIKE '%$search%' OR dpn_nik_kis LIKE '%$search%' OR dpn_alamat_kis LIKE '%$search%' OR dpn_nik_pm LIKE '%$search%' OR dpn_nama_pm LIKE '%$search%' OR dpn_nkk_pm LIKE '%$search%' OR dpn_alamat_pm LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3";
        } else {
            $kondisi_search = "dpn_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3";
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
        $builder = $db->table($this->table);
        $query = $builder->select('*')
            // ->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_pbi_jkn.kdstawin')
            // ->join('pekerjaan_kondisi_pekerjaan', 'pekerjaan_kondisi_pekerjaan.IDKondisi=individu_data.KondisiPekerjaan')
            // ->join('pendidikan_pend_tinggi', 'pendidikan_pend_tinggi.IDPendidikan=individu_data.PendTertinggi')
            // ->join('ket_verivali', 'ket_verivali.id_ketvv=individu_data.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(dpn_id) as jml FROM " . $this->table;
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND dpn_kode_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND dpn_rw_pm = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND dpn_rt_pm = '$filter3'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (dpn_noka_kis LIKE '%$search%' OR dpn_nama_kis LIKE '%$search%' OR dpn_nik_kis LIKE '%$search%' OR dpn_alamat_kis LIKE '%$search%' OR dpn_nik_pm LIKE '%$search%' OR dpn_nama_pm LIKE '%$search%' OR dpn_nkk_pm LIKE '%$search%' OR dpn_alamat_pm LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3";
        }

        $sQuery = "SELECT COUNT(dpn_id) as jml FROM " . $this->table . " WHERE dpn_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }
}
