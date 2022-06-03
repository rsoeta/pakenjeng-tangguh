<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class DtksModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'dtks_verivali';
    protected $column_order = array('no', 'nik', 'nama', 'nkk', 'tmp_lahir', 'tgl_lahir', 'alamat', 'indikasi_masalah');
    protected $column_search = array('nama', 'nik', 'nkk', 'tmp_lahir', 'tgl_lahir', 'alamat', 'indikasi_masalah');
    protected $order = array('no' => 'desc');
    protected $request;
    protected $db;
    protected $dt;
    protected $primaryKey = 'no';

    protected $allowedFields = ['nik_perbaikan', 'pekerjaan', 'rt', 'rw'];

    protected $useTimestamps = true;
    protected $skipValidation     = false;

    private function _get_datatables_query()
    {
        $i = 0;
        foreach ($this->column_search as $item) {
            if ($this->request->getPost('search')['value']) {
                if ($i === 0) {
                    $this->dt->groupStart();
                    $this->dt->like($item, $this->request->getPost('search')['value']);
                } else {
                    $this->dt->orLike($item, $this->request->getPost('search')['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->dt->groupEnd();
            }
            $i++;
        }

        if ($this->request->getPost('order')) {
            $this->dt->orderBy($this->column_order[$this->request->getPost('order')['0']['column']], $this->request->getPost('order')['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->dt->orderBy(key($order), $order[key($order)]);
        }
    }
    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        $tbl_storage = $this->db->table($this->table);
        return $tbl_storage->countAllResults();
    }


    function index()
    {
        $this->db->setDatabase('db_bend');
        $builder = $this->db->table('dtks_verivali');

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
        return $this->db->table('dtks_verivali')->get()->getResultArray();
    }
}
