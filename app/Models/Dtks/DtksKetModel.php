<?php

namespace App\Models\Dtks;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class DtksKetModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'ket_verivali';
    protected $primaryKey = 'id_ketvv';

    protected $allowedFields = ['id_ketvv', 'jenis_keterangan'];



    public function pilihStatus()
    {
        $nomorStatus = $this->select('jenis_keterangan')->distinct(); // This should be equivalent to "SELECT DISTINCT(city) FROM outlets_data";

        return $nomorStatus;
    }

    public function joinStatus()
    {
        $builder = $this->db->table('dtks_vv06');
        $builder->select('status', 'jenis_keterangan');
        $builder->join('dtks_status', 'dtks_status.id_status = dtks_vv06.status');
        // $builder->distinct();
        $builder->orderBy('id_status');
        $query = $builder->get();
        return $query;
    }

    public function cariData($cari)
    {
        return $this->table('ket_verivali')->like('jenis_keterangan', $cari);
    }
}
