<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class DtksStatusModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }
    protected $table      = 'dtks_status';
    protected $primaryKey = 'id_status';

    protected $allowedFields = ['id_status', 'jenis_status'];



    public function pilihStatus()
    {
        $nomorStatus = $this->select('jenis_status')->distinct(); // This should be equivalent to "SELECT DISTINCT(city) FROM outlets_data";

        return $nomorStatus;
    }

    public function joinStatus()
    {
        $builder = $this->db->table('dtks_vv06');
        $builder->select('status', 'jenis_status');
        $builder->join('dtks_status', 'dtks_status.id_status = dtks_vv06.status');
        // $builder->distinct();
        $builder->orderBy('id_status');
        $query = $builder->get();
        return $query;
    }

    public function cariData($cari)
    {
        return $this->table('dtks_status')->like('jenis_status', $cari);
    }
}
