<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class VeriVali09Model extends Model
{
    protected $table      = 'dtks_verivali09';
    protected $primaryKey = 'idv';

    protected $allowedFields = ['ids', 'prov', 'kab', 'kec', 'desa', 'nik', 'nkk', 'nama', 'tgl_lahir', 'tmp_lahir', 'alamat', 'kode_desa', 'rw', 'rt', 'indikasi_masalah', 'nik_perbaikan', 'pekerjaan', 'status', 'cek_update', 'ket_verivali', 'created_at', 'updated_at', 'created_by'];

    protected $useTimestamps = true;

    var $column_order = array('', 'nama', 'alamat',  'nkk', 'nik', 'tmp_lahir', 'tgl_lahir', 'indikasi_masalah', 'jenis_keterangan');

    var $order = array('updated_at' => 'desc');

    function get_datatables($filter1, $filter2, $filter3, $filter4)
    {
        // desa
        if ($filter1 == "") {
            $kondisi_filter1 = "";
        } else {
            $kondisi_filter1 = " AND kode_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }
        // status
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND ket_verivali = '$filter4'";
        }

        // search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "(nama LIKE '%$search%' OR nik LIKE '%$search%' OR nkk LIKE '%$search%' OR alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "idv != '' $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
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
        $builder = $db->table('dtks_verivali09');
        $query = $builder->select('*')
            // ->join('tb_ket_bayar', 'tb_ket_bayar.KodeBayar=pbb_dhkp21.ket')
            // ->join('tbl_rt', 'tbl_rt.IdJenKel=pbb_dhkp21.JKAnak')
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
        $sQuery = "SELECT COUNT(idv) as jml FROM dtks_verivali09";
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
            $kondisi_filter1 = " AND kode_desa = '$filter1'";
        }

        // rw
        if ($filter2 == "") {
            $kondisi_filter2 = "";
        } else {
            $kondisi_filter2 = " AND rw = '$filter2'";
        }

        // operator
        if ($filter3 == "") {
            $kondisi_filter3 = "";
        } else {
            $kondisi_filter3 = " AND rt = '$filter3'";
        }
        // status
        if ($filter4 == "") {
            $kondisi_filter4 = "";
        } else {
            $kondisi_filter4 = " AND ket_verivali = '$filter4'";
        }
        // kondisi search
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
            $kondisi_search = "AND (nama LIKE '%$search%' OR nik LIKE '%$search%' OR nkk LIKE '%$search%' OR alamat LIKE '%$search%') $kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        } else {
            $kondisi_search = "$kondisi_filter1 $kondisi_filter2 $kondisi_filter3 $kondisi_filter4";
        }

        $sQuery = "SELECT COUNT(idv) as jml FROM dtks_verivali09 WHERE idv != '' $kondisi_search";
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
        $builder->join('dtks_status', 'dtks_status.id_status = dtks_vv06.status');
        $builder->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_vv06.ket_verivali');
        $builder->orderBy('updated_at');
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
            ->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_vv06.ket_verivali')
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
            ->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_verivali09.ket_verivali')
            ->where(['rw' => $jbt])
            ->where(['status <=' => 1])
            ->where(['ket_verivali <=' => 2])
            ->orderBy('rt')
            ->get()
            ->getResultArray();
    }

    public function getInvalid()
    {
        $jbt = (session()->get('level'));
        // $whr = >=1 && <=2;
        $builder = $this->db->table('dtks_verivali09');
        $builder->select('*');
        $builder->join('tbl_rw', 'tbl_rw.no_rw = dtks_verivali09.rw');
        // $builder->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_verivali09.ket_verivali');
        $builder->select('(SELECT COUNT(dtks_verivali09.idv) FROM dtks_verivali09 WHERE dtks_verivali09.rw=tbl_rw.no_rw && dtks_verivali09.ket_verivali <= 2 && dtks_verivali09.status <= 1) AS Inv', false);
        // $builder->where(['rw' => $jbt]);
        // $builder->where(['ket_verivali <=' =>  2]);
        // ->where(['ket_verivali' =>  2])
        $builder->orderBy('rw');
        $builder->distinct();
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
        $builder = $this->db->table('dtks_vv06');
        $builder->select('rw, nama_rw');
        $builder->join('tbl_rw', 'tbl_rw.no_rw = dtks_vv06.rw');
        $builder->select('(SELECT COUNT(dtks_vv06.idv) FROM dtks_vv06 WHERE dtks_vv06.rw=tbl_rw.no_rw) AS Vv', false);
        $builder->select('(SELECT COUNT(dtks_vv06.idv) FROM dtks_vv06 WHERE dtks_vv06.rw=tbl_rw.no_rw && dtks_vv06.cek_update != 0) AS Hsl', false);

        $builder->distinct();
        $builder->orderBy('rw');
        $query = $builder->get();
        return $query;
    }

    public function getDataRW()
    {
        $builder = $this->db->table('dtks_verivali09');
        $builder->select('rw');
        $builder->distinct();
        $builder->orderBy('rw', 'asc');

        $query = $builder->get();

        return $query;
    }

    public function getDataRT()
    {
        $builder = $this->db->table('dtks_verivali09');
        $builder->select('rt');
        $builder->distinct();
        $builder->orderBy('rt', 'asc');

        $query = $builder->get();

        return $query;
    }

    public function getDataGroupBy()
    {
        $builder = $this->db->table('dtks_verivali09');
        $builder->join('ket_verivali', 'ket_verivali.id_ketvv=dtks_verivali09.ket_verivali');
        $builder->select('jenis_keterangan');
        $builder->selectCount('idv', 'total_data');
        $builder->groupBy('jenis_keterangan');
        $query = $builder->get()->getResultArray();

        // foreach ($query as $row) {
        // }

        return $query;
    }
}
