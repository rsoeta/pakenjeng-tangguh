<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgresDesaModel extends Model
{
    protected $table = 'tbl_desa';
    protected $primaryKey = 'id_desa';

    protected $allowedFields = ['kode_desa', 'KodeDesa', 'nama_desa', 'BelumCek', 'TidakValid', 'MeninggalDihapus', 'SudahValid', 'TidakMemilikiEktp', 'PembatalanDiperiksa'];

    public function getDesa()
    {
        $db = db_connect();
        $query = $db->table('tbl_desa')->get();

        return $query;
    }

    public function getStatus()
    {
        $db = db_connect();
        $builder = $db->table('dtks_status');
        $builder->distinct();
        $builder->select('id_status, jenis_status');
        $builder->join('dtks_pkj07', 'dtks_pkj07.status=dtks_status.id_status');
        $builder->orderBy('jenis_status', 'asc');
        $query = $builder->get();

        return $query;
    }

    public function getKet()
    {
        $db = db_connect();
        $builder = $db->table('ket_verivali');
        $builder->distinct();
        $builder->select('id_ketvv, jenis_keterangan');
        $builder->join('dtks_pkj07', 'dtks_pkj07.ket_verivali=ket_verivali.id_ketvv');
        $builder->orderBy('jenis_keterangan', 'desc');
        $query = $builder->get();

        return $query;
    }

    public function update_data($data, $id)
    {
        $data = [
            'title' => $title,
            'name'  => $name,
            'date'  => $date,
        ];

        $builder->where('id', $id);
        $builder->update($data);
    }
}
