<?php

namespace App\Models\Dtks;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class CsvReportModel extends Model
{
    protected $table = 'dtks_csv_report';
    protected $allowedFields = [
        'cr_nama_kec',
        'cr_nama_desa',
        'cr_nik_usulan',
        'cr_program_bansos',
        'cr_hasil',
        'cr_padan',
        'cr_nama_lgkp',
        'cr_ket_vali',
        'cr_ck_id',
        'cr_created_by',
        'cr_created_at',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    var $column_order = array('cr_id', 'du_nik', 'nama', 'cr_nama_lgkp', 'nokk', 'alamat', 'rt', 'rw', 'cr_nama_desa', 'cr_program_bansos', 'cr_hasil', 'cr_padan', 'cr_ket_vali', 'cr_ck_id');


    var $order = array('vw_csv_report.cr_id' => 'asc');

    function getDataTabel($filter1, $filter2, $filter3, $filter4, $filter5, $filter6)
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
            $kondisi_filter4 = " AND cr_ck_id = '$filter4'";
        }
        // updated_at
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND tahun_upload = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND bulan_upload = '$filter6'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(nama LIKE '%$search%' OR nokk LIKE '%$search%' OR du_nik LIKE '%$search%' OR cr_nama_lgkp LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        } else {
            $kondisi_search = "vw_csv_report.cr_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
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
        $builder = $db->table('vw_csv_report');
        $query = $builder->select('cr_id, kecamatan, kelurahan, cr_nama_kec, cr_nama_desa, du_nik, nama, cr_nama_lgkp, nokk, alamat, rt, rw, program_bansos, cr_program_bansos, cr_hasil, cr_padan, cr_ket_vali, cr_ck_id, cr_created_by, cr_created_at, tahun_upload, bulan_upload')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function semua()
    {
        $sQuery = "SELECT COUNT(cr_id) as jml FROM vw_csv_report";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6)
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
            $kondisi_filter4 = " AND cr_ck_id = '$filter4'";
        }
        // rt
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND tahun_upload = '$filter5'";
        }
        // updated_at
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND bulan_upload = '$filter6'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (nama LIKE '%$search%' OR nokk LIKE '%$search%' OR du_nik LIKE '%$search%' OR cr_nama_lgkp LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        }

        $sQuery = "SELECT COUNT(cr_id) as jml FROM vw_csv_report WHERE cr_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    public function getCsvKet()
    {
        $builder = $this->db->table('tb_csv_ket')
            ->select('*')
            ->orderBy('ck_nama', 'ASC')
            ->get();
        $query = $builder->getResultArray();

        return $query;
    }
}
