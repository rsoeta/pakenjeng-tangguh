<?php

namespace App\Models\Dtks;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class Vv06Model extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'dtks_vv06';
    protected $primaryKey = 'idv';

    protected $allowedFields = ['nik', 'nkk', 'nama', 'tgl_lahir', 'tmp_lahir', 'alamat', 'rt', 'rw', 'indikasi_masalah', 'nik_perbaikan', 'pekerjaan', 'cek_update', 'status', 'ket_verivali', 'created_at', 'updated_at'];

    protected $useTimestamps = true;


    function index()
    {
        $this->db->setDatabase('db_bend');
        $builder = $this->db->table('dtks_data');

        $post = $builder->get()->getResult();
        return $post;
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
        $jbt = (session()->get('jabatan'));
        return $this->db->table('dtks_vv06')
            ->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_vv06.ket_verivali')
            ->where(['rw' => $jbt])
            ->where(['status <=' => 1])
            ->where(['ket_verivali <=' => 2])
            ->orderBy('rt')
            ->get()
            ->getResultArray();
    }

    public function getInvalid()
    {
        $jbt = (session()->get('jabatan'));
        // $whr = >=1 && <=2;
        $builder = $this->db->table('dtks_vv06');
        $builder->select('rw, nama_rw');
        $builder->join('tbl_rw', 'tbl_rw.no_rw = dtks_vv06.rw');
        // $builder->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_vv06.ket_verivali');
        $builder->select('(SELECT COUNT(dtks_vv06.idv) FROM dtks_vv06 WHERE dtks_vv06.rw=tbl_rw.no_rw && dtks_vv06.ket_verivali <= 2 && dtks_vv06.status <= 1) AS Inv', false);
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
        $jbt = (session()->get('jabatan'));
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

    public function getGambar()
    {
    }
}
