<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class BnbaModel extends Model
{
    protected $table      = 'dtks_bnba';
    protected $primaryKey = 'db_id';

    protected $allowedFields = [
        "db_id_dtks", "db_province", "db_regency", "db_district", "db_village", "db_alamat", "db_dusun", "db_rw", "db_rt", "db_nkk", "db_nik", "db_nama", "db_tgl_lahir", "db_tmp_lahir", "db_jenkel_id", "db_ibu_kandung", "db_shdk_id", "db_status", "db_pkh", "db_bpnt", "db_bst", "db_bpnt_ppkm", "db_pbi", "db_creator", "db_created", "db_modifier", "db_modified"
    ];

    protected $useTimestamps = true;

    var $column_order = array('', 'db_nama', 'db_id_dtks', 'db_nkk', 'db_nik', 'db_tmp_lahir', 'db_tgl_lahir');

    var $order = array('db_id' => 'asc');

    function get_datatables($filter1, $filter2, $filter3, $filter4)
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
            $kondisi_filter4 = " AND db_shdk_id = '$filter4'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "db_id != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
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
        $builder = $db->table('dtks_bnba');
        $query = $builder->select('*')
            ->join('tb_shdk', 'tb_shdk.id=dtks_bnba.db_shdk_id')
            // ->join('pekerjaan_kondisi_pekerjaan', 'pekerjaan_kondisi_pekerjaan.IDKondisi=individu_data.KondisiPekerjaan')
            // ->join('pendidikan_pendb_tinggi', 'pendidikan_pendb_tinggi.IDPendidikan=individu_data.PendTertinggi')
            // ->join('ket_verivali', 'ket_verivali.idb_ketvv=individu_data.ket_verivali')
            ->where($kondisi_search)
            ->orderBy($result_order, $result_dir)
            ->limit($_POST['length'], $_POST['start'])
            ->get();

        return $query->getResult();
    }

    function jumlah_semua()
    {
        $sQuery = "SELECT COUNT(db_id) as jml FROM dtks_bnba";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function jumlah_filter($filter1, $filter2, $filter3, $filter4)
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
            $kondisi_filter4 = " AND db_shdk_id = '$filter4'";
        }

        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (db_nama LIKE '%$search%' OR db_nik LIKE '%$search%' OR db_nkk LIKE '%$search%' OR db_alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        }

        $sQuery = "SELECT COUNT(db_id) as jml FROM dtks_bnba WHERE db_id != '' $kondisi_search";
        $db = db_connect();
        $query = $db->query($sQuery)->getRow();

        return $query;
    }

    function getDataRow($ids)
    {
        $builder = $this->db->table($this->table);
        $query = $builder->getWhere(['ids' => $ids]);

        return $query->getRow();
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
        $builder = $this->db->table('dtks_vv06');
        $builder->select('*');
        $builder->join('dtks_status', 'dtks_status.idb_status = dtks_vv06.status');
        $builder->join('ket_verivali', 'ket_verivali.idb_ketvv = dtks_vv06.ket_verivali');
        $builder->orderBy('updatedb_at');
        // $builder->where(['ket_verivali' => 1]);
        $query = $builder->get();

        return $query;
    }

    function getTablesDtks()
    {
        // if(isset($_POST["length"]) && $_POST["length"] != -1);
        $request = service('request');
        $post_search = $request->getPost('search');
        $post_order = $request->getPost('order');
        $post_length = $request->getPost('length');
        $post_start = $request->getPost('start');
        $db = db_connect();
        $builder = $db->table('dtks_vv06');
        $query = $builder->select('*')
            ->limit($post_length, $post_start)
            ->get();
        return $query->getResult();
    }

    public function getData()
    {
        $jbt = (session()->get('jabatan'));
        return $this->db->table('dtks_vv06')
            ->join('ket_verivali', 'ket_verivali.idb_ketvv = dtks_vv06.ket_verivali')
            ->where(['rw' => $jbt])
            ->where(['cek_update' => '0'])
            ->orderBy('rt')
            ->get()
            ->getResultArray();
    }

    public function getDataInvalid()
    {
        $jbt = (session()->get('level'));
        return $this->db->table('dtks_verivali09')
            ->join('ket_verivali', 'ket_verivali.idb_ketvv = dtks_verivali09.ket_verivali')
            ->where(['rw' => $jbt])
            ->where(['status <=' => 1])
            ->where(['ket_verivali <=' => 2])
            ->orderBy('rt')
            ->get()
            ->getResultArray();
    }

    public function getInvalid()
    {
        $jbt = (session()->get('kode_desa'));
        // $whr = >=1 && <=2;
        $builder = $this->db->table('dtks_verivali09');
        $builder->select('tb_villages.name as nama_desa, rw');
        $builder->where('kode_desa', $jbt);
        $builder->where('ket_verivali !=', 3);
        $builder->join('tb_villages', 'tb_villages.id = dtks_verivali09.kode_desa');
        $builder->selectCount('ket_verivali');
        // $builder->join('tbl_rw', 'tbl_rw.db_rw = dtks_verivali09.rw');
        // $builder->join('ket_verivali', 'ket_verivali.idb_ketvv = dtks_verivali09.ket_verivali');
        // $builder->select('(SELECT COUNT(dtks_verivali09.idv) FROM dtks_verivali09 WHERE dtks_verivali09.rw=tbl_rw.db_rw && dtks_verivali09.ket_verivali <= 2 && dtks_verivali09.status <= 1) AS Inv', false);
        // $builder->where(['rw' => $jbt]);
        // $builder->where(['ket_verivali <=' =>  2]);
        // ->where(['ket_verivali' =>  2])
        $builder->groupBy(
            'nama_desa, rw'
        );
        $query = $builder->get();

        return $query;
        // ->getResultArray();
    }

    public function getDataNoAddress()
    {
        $jbt = (session()->get('level'));
        return $this->db->table('dtks_vv06')
            ->where(['rw' => ""])
            ->where(['cek_update' => '0'])
            ->get()
            ->getResultArray();
    }
    public function getIdDtks($id = false)
    {
        if ($id == false) {
            return $this->findAll();
        }
        return $this->where(['ids' => $id])->first();
    }

    public function getListVv()
    {
        $jbt = (session()->get('jabatan'));

        $db = db_connect();
        $builder = $db->table('dtks_vv06');
        $builder->where('rw', $jbt);

        $query = $builder->countAllResults();
        return $query;
    }
    public function getListSisaPerb()
    {
        $jbt = (session()->get('jabatan'));

        $db = db_connect();
        $builder = $db->table('dtks_vv06');
        $builder->where('rw', $jbt);
        $builder->where('cek_update', '>0');

        $query = $builder->countAllResults();
        return $query;
    }

    public function getDataRekRw()
    {
        $jbt = (session()->get('kode_desa'));

        $builder = $this->db->table('dtks_verivali09');
        $builder->select('tb_villages.name as nama_desa, rw');
        $builder->where('kode_desa', $jbt);
        $builder->join('tb_villages', 'tb_villages.id = dtks_verivali09.kode_desa');
        // $builder->selectCount('kode_desa');
        // $builder->selectCount('rw');
        $builder->selectCount('ket_verivali');
        $builder->selectSum('cek_update');
        // $builder->select('(SELECT COUNT(dtks_verivali09.idv) FROM dtks_verivali09 WHERE dtks_verivali09.rw=tbl_rw.db_rw) AS Vv', false);
        // $builder->select('(SELECT COUNT(dtks_verivali09.idv) FROM dtks_verivali09 WHERE dtks_verivali09.rw=tbl_rw.db_rw && dtks_verivali09.cek_update != 0) AS Hsl', false);
        $builder->groupBy('nama_desa, rw');
        // $builder->orderBy('rw');
        $query = $builder->get();
        return $query;
    }

    public function getDataRW()
    {
        $builder = $this->db->table('dtks_bnba');
        $builder->select('db_rw');
        $builder->distinct();
        $builder->orderBy('db_rw', 'asc');

        $query = $builder->get();

        return $query;
    }

    public function getDataRT()
    {
        $builder = $this->db->table('dtks_bnba');
        $builder->select('db_rt');
        $builder->distinct();
        $builder->orderBy('db_rt', 'asc');

        $query = $builder->get();

        return $query;
    }

    public function getDataJenkel()
    {
        $builder = $this->db->table('tbl_jenkel');
        $query = $builder->get()->getResultArray();

        // foreach ($query as $row) {
        // }

        return $query;
    }

    public function getDataShdk()
    {
        $builder = $this->db->table('tb_shdk');
        $query = $builder->get()->getResultArray();

        // foreach ($query as $row) {
        // }

        return $query;
    }
}
