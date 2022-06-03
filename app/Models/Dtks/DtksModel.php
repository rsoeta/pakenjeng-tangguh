<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class DtksModel extends Model
{
    var $column_order = array('idv', 'nik', 'nama', 'alamat', 'rt', 'rw', 'kode_desa', 'desa', 'jenis_status', 'jenis_keterangan', 'ket_verivali');
    var $order = array('idv' => 'asc');

    function get_datatables($desa, $rw, $status, $keterangan)
    {
        // desa
        if ($desa == "") {
            $kondisi_desa = "";
        } else {
            $kondisi_desa = " AND desa = '$desa'";
        }

        // rw
        if ($rw == "") {
            $kondisi_rw = "";
        } else {
            $kondisi_rw = " AND rw = '$rw'";
        }
        // status
        if ($status == "") {
            $kondisi_status = "";
        } else {
            $kondisi_status = " AND status = '$status'";
        }
        // keterangan
        if ($keterangan == "") {
            $kondisi_keterangan = "";
        } else {
            $kondisi_keterangan = " AND ket_verivali = '$keterangan'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "nama LIKE '%$search%' OR alamat LIKE '%$search%' $kondisi_desa $kondisi_rw $kondisi_status $kondisi_keterangan";
        } else {
            $kondisi_search = "idv != '' $kondisi_desa $kondisi_rw $kondisi_status $kondisi_keterangan";
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
        $builder = $db->table('dtks_pkj07');
        $query = $builder->select('*')
            ->join('dtks_status', 'dtks_status.id_status=dtks_pkj07.status')
            ->join('ket_verivali', 'ket_verivali.id_ketvv=dtks_pkj07.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    // function get_datatables()
    // {
    //     if ($_POST['length'] != -1);
    //     $db = db_connect();
    //     $builder = $db->table('dtks_pkj07');
    //     $query = $builder->select('*')
    //         ->limit($_POST['length'], $_POST['start'])
    //         ->get();

    //     return $query->getResult();
    // }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(idv) as jml FROM dtks_pkj07";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($desa, $rw, $status, $keterangan)
    {
        // desa
        if ($desa == "") {
            $kondisi_desa = "";
        } else {
            $kondisi_desa = " AND desa = '$desa'";
        }

        // rw
        if ($rw == "") {
            $kondisi_rw = "";
        } else {
            $kondisi_rw = " AND rw = '$rw'";
        }

        // status
        if ($status == "") {
            $kondisi_status = "";
        } else {
            $kondisi_status = " AND status = '$status'";
        }

        // rw
        if ($keterangan == "") {
            $kondisi_keterangan = "";
        } else {
            $kondisi_keterangan = " AND ket_verivali = '$keterangan'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (nama LIKE '%$search%' OR alamat LIKE '%$search%') $kondisi_desa $kondisi_rw $kondisi_status $kondisi_keterangan";
        } else {
            $kondisi_search = "$kondisi_desa $kondisi_rw $kondisi_status $kondisi_keterangan";
        }

        $sQuery = "SELECT COUNT(idv) as jml FROM dtks_pkj07 WHERE idv != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function distinctRw()
    {
        $db = db_connect();
        $builder = $db->table('dtks_pkj07');
        $builder->distinct();
        $builder->select('rw');
        $builder->orderBy('rw', 'asc');
        $query = $builder->get();

        return $query;
    }
}
