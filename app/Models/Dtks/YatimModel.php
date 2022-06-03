<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class YatimModel extends Model
{
    protected $table      = 'dtks_yatim';
    protected $primaryKey = "ID";

    protected $allowedFields = [
        "Prov",
        "Kab",
        "Kec",
        "Desa",
        "Rw",
        "Rt",
        "AlamatAnak",
        "NamaAnak",
        "NIKAnak",
        "NoKKAnak",
        "TmpLahirAnak",
        "TglLahirAnak",
        "JKAnak",
        "PendidikanAnak",
        "AnakTinggalBersama",
        "LamFotoAnak",
        "LamFotoKK",
        "Usia",
        "NamaIbuKandung",
        "NIKIbuKandung",
        "NamaAyahKandung",
        "NIKAyahKandung",
        "StatusOrtu",
        "NamaKakek",
        "NIKKakek",
        "NamaWali",
        "NIKWali",
        "JKWali",
        "TmpLahirWali",
        "TglLahirWali",
        "NoTlpWali",
        "AlamatWali",
        "StatusPengasuh",
        "Bansos",
        "Covid19",
        "Operator",
        "WaktuAssesmen",
    ];

    var $column_order = array(
        "ID",
        "Prov",
        "Kab",
        "Kec",
        "Desa",
        "Rw",
        "Rt",
        "AlamatAnak",
        "NamaAnak",
        "NIKAnak",
        "NoKKAnak",
        "TmpLahirAnak",
        "TglLahirAnak",
        "JKAnak",
        "PendidikanAnak",
        "AnakTinggalBersama",
        "LamFotoAnak",
        "LamFotoKK",
        "Usia",
        "NamaIbuKandung",
        "NIKIbuKandung",
        "NamaAyahKandung",
        "NIKAyahKandung",
        "StatusOrtu",
        "NamaKakek",
        "NIKKakek",
        "NamaWali",
        "NIKWali",
        "JKWali",
        "TmpLahirWali",
        "TglLahirWali",
        "NoTlpWali",
        "AlamatWali",
        "StatusPengasuh",
        "Bansos",
        "Covid19",
        "Operator",
        "WaktuAssesmen",
    );

    var $order = array('NamaAnak' => 'asc');

    function get_datatables($desa, $rw, $operator, $keterangan)
    {
        // desa
        if ($desa == "") {
            $kondisi_desa = "";
        } else {
            $kondisi_desa = " AND Desa = '$desa'";
        }

        // rw
        if ($rw == "") {
            $kondisi_rw = "";
        } else {
            $kondisi_rw = " AND Rw = '$rw'";
        }
        // status
        if ($operator == "") {
            $kondisi_operator = "";
        } else {
            $kondisi_operator = " AND Operator = '$operator'";
        }
        // keterangan
        if ($keterangan == "") {
            $kondisi_keterangan = "";
        } else {
            $kondisi_keterangan = " AND Bansos = '$keterangan'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(NamaAnak LIKE '%$search%' OR NoKKAnak LIKE '%$search%' OR NIKAnak LIKE '%$search%' OR NamaIbuKandung LIKE '%$search%' OR NamaAyahKandung LIKE '%$search%' OR NamaWali LIKE '%$search%') $kondisi_desa $kondisi_rw $kondisi_operator $kondisi_keterangan";
        } else {
            $kondisi_search = "ID != '' $kondisi_desa $kondisi_rw $kondisi_operator $kondisi_keterangan";
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
        $builder = $db->table('dtks_yatim');
        $query = $builder->select('*')
            ->join('tbl_desa', 'tbl_desa.KodeDesa=dtks_yatim.Desa')
            ->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=dtks_yatim.JKAnak')
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
        $sQuery = "SELECT COUNT(ID) as jml FROM dtks_yatim";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($desa, $rw, $operator, $keterangan)
    {
        // desa
        if ($desa == "") {
            $kondisi_desa = "";
        } else {
            $kondisi_desa = " AND Desa = '$desa'";
        }

        // rw
        if ($rw == "") {
            $kondisi_rw = "";
        } else {
            $kondisi_rw = " AND Rw = '$rw'";
        }

        // operator
        if ($operator == "") {
            $kondisi_operator = "";
        } else {
            $kondisi_operator = " AND Operator = '$operator'";
        }

        // rw
        if ($keterangan == "") {
            $kondisi_keterangan = "";
        } else {
            $kondisi_keterangan = " AND Bansos = '$keterangan'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (NamaAnak LIKE '%$search%' OR NoKKAnak LIKE '%$search%' OR NIKAnak LIKE '%$search%' OR NamaIbuKandung LIKE '%$search%' OR NamaAyahKandung LIKE '%$search%' OR NamaWali LIKE '%$search%') $kondisi_desa $kondisi_rw $kondisi_operator $kondisi_keterangan";
        } else {
            $kondisi_search = "$kondisi_desa $kondisi_rw $kondisi_operator $kondisi_keterangan";
        }

        $sQuery = "SELECT COUNT(ID) as jml FROM dtks_yatim WHERE ID != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    public function tambah($data)
    {
        $this->db->table('tbl_masuk')->insert($data);
    }

    public function jmlYatim()
    {
        $builder = $this->db->table('dtks_yatim');
        $query = $builder->select("COUNT(ID) as jml, Rw");
        $query = $builder->where("Rw GROUP BY Rw, Rw")->get();
        $record = $query->getResult();

        return $record;
    }
}
