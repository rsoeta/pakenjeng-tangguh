<?php

namespace App\Models\Dtks\Famantama;

use CodeIgniter\Model;

class FamantamaModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'famantama_data';
    protected $primaryKey = 'fd_id';

    protected $allowedFields = [
        "fd_id",
        "fd_nama_lengkap",
        "fd_nik",
        "fd_nkk",
        "fd_alamat",
        "fd_rt",
        "fd_rw",
        "fd_desa",
        "fd_kec",
        "fd_kab",
        "fd_prov",
        "fd_shdk",
        "fd_jenkel",
        "fd_sta_bangteti",
        "fd_sta_lahteti",
        "fd_jenlai",
        "fd_jendin",
        "fd_kondin",
        "fd_jentap",
        "fd_kontap",
        "fd_penghasilan",
        "fd_pengeluaran",
        "fd_jml_tanggungan",
        "fd_roda_dua",
        "fd_sumber_minum",
        "fd_cara_minum",
        "fd_penerangan_utama",
        "fd_daya_listrik",
        "fd_bahan_masak",
        "fd_tempat_bab",
        "fd_jenis_kloset",
        "fd_tempat_tinja",
        "fd_pekerjaan_kk",
        "fd_created_at_year",
        "fd_created_at_month",
        "fd_created_by",
        "fd_created_at",
        "fd_updated_by",
        "fd_updated_at",
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'fd_created_at';
    protected $updatedField  = 'fd_updated_at';
    // protected $deletedField  = 'deleted_at';

    protected $skipValidation     = false;

    var $column_order = array('', 'fd_nama_lengkap', 'fd_nik', 'fd_nkk', 'fd_alamat', 'fd_rt',  'fd_rw', 'fd_shdk', 'fd_pekerjaan_kk', 'fd_created_by', 'fd_updated_at');

    var $order = array('famantama_data.fd_updated_at' => 'desc');


    function get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6)
    {
        // fil$filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND fd_desa = '$filter1'";
        }
        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND fd_rw = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND fd_rt = '$filter3'";
        }
        // shdk
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND fd_shdk = '$filter4'";
        }
        // filter5
        if ($filter5 == "") {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND fd_pekerjaan_kk = '$filter5'";
        }
        // filter5
        if ($filter6 == "") {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND fd_jenkel = '$filter6'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(fd_nama_lengkap LIKE '%$search%' OR fd_nkk LIKE '%$search%' OR fd_nik LIKE '%$search%' OR fd_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        } else {
            $kondisi_search = "famantama_data.fd_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
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
        $builder = $db->table('famantama_data');
        $query = $builder->select('famantama_data.fd_id, tb_villages.name as namaDesa, tb_districts.name as namaKec, fd_nama_lengkap, fd_nkk, famantama_data.fd_nik, fd_alamat, fd_rt, fd_rw, fd_desa, fd_kec, famantama_data.fd_created_at, fd_created_at_year, fd_created_at_month, fd_created_by, dtks_users.email, famantama_data.fd_updated_at, dtks_users.nope, dtks_users.fullname as namaCreator, dtks_users.fullname as namaEditor, jenis_shdk, fd_pekerjaan_kk, tb_penduduk_pekerjaan.pk_nama, fpp_id')
            ->join('tb_villages', 'tb_villages.id=famantama_data.fd_desa')
            ->join('tb_districts', 'tb_districts.id=famantama_data.fd_kec')
            ->join('dtks_users', 'dtks_users.nik=famantama_data.fd_created_by')
            ->join('tb_shdk', 'tb_shdk.id=famantama_data.fd_shdk')
            ->join('tb_penduduk_pekerjaan', 'tb_penduduk_pekerjaan.pk_id=famantama_data.fd_pekerjaan_kk')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(fd_id) as jml FROM famantama_data";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6)
    {
        // fil$filter1
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND fd_desa = '$filter1'";
        }
        // status
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND fd_rw = '$filter2'";
        }
        // rw
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND fd_rt = '$filter3'";
        }
        // shdk
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND fd_shdk = '$filter4'";
        }
        // filter5
        if (
            $filter5 == ""
        ) {
            $kondisi_filter5 = "";
        } else {
            $kondisi_filter5 = " AND fd_pekerjaan_kk = '$filter5'";
        }
        // filter6
        if (
            $filter6 == ""
        ) {
            $kondisi_filter6 = "";
        } else {
            $kondisi_filter6 = " AND fd_jenkel = '$filter6'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (fd_nama_lengkap LIKE '%$search%' OR fd_nkk LIKE '%$search%' OR fd_nik LIKE '%$search%' OR fd_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4 $kondisi_filter5 $kondisi_filter6";
        }

        $sQuery = "SELECT COUNT(fd_nik) as jml FROM famantama_data WHERE fd_nik != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    public function dataExport($filter1, $filter5, $filter6)
    {
        $builder = $this->db->table('famantama_data');
        $builder->select('fd_nama_lengkap, fd_nik, fd_nkk, fd_alamat, fd_rt, fd_rw, fd_desa, fd_kec, fd_kab, fd_prov, fd_shdk, fd_sta_bangteti, fd_sta_lahteti, fd_jenlai, fd_jendin, fd_kondin, fd_jentap, fd_kontap, fd_penghasilan, fd_pengeluaran, fd_jml_tanggungan, fd_roda_dua, fd_sumber_minum, fd_cara_minum, fd_penerangan_utama, fd_daya_listrik, fd_bahan_masak, fd_tempat_bab, fd_jenis_kloset, fd_tempat_tinja, fd_pekerjaan_kk, fd_created_at_year, fd_created_at_month, fd_created_by, fd_created_at, fd_updated_by, fd_updated_at, tb_villages.name as namaDesa, fpp_id, tsf_id');
        $builder->join('tb_villages', 'tb_villages.id=famantama_data.fd_desa', 'LEFT');
        $builder->join('tb_penduduk_pekerjaan', 'tb_penduduk_pekerjaan.pk_id=famantama_data.fd_pekerjaan_kk', 'LEFT');
        $builder->join('tb_shdk', 'tb_shdk.id=famantama_data.fd_shdk', 'LEFT');
        if ($filter1 !== "") {
            $builder->where('fd_desa', $filter1);
        }
        if ($filter5 !== "") {
            $builder->where('fd_created_at_year', $filter5);
        }
        if ($filter6 !== "") {
            $builder->where('fd_created_at_month', $filter6);
        }
        // $builder->orderBy('dtks_usulan22.du_id', 'asc');
        // $query = $builder->orderBy('fd_updated_at', 'ASC')->limit(100)->get();
        $query = $builder->orderBy('fd_id', 'ASC')->get();

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

    public function getByNomorKK($nomorKK)
    {
        return $this->where('fd_nkk', $nomorKK)->first();
    }
}
