<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Villages extends ResourceController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function provinces()
    {
        $data = $this->db->table('tb_provinces')
            ->select('id, CAST(name AS CHAR) as name')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }

    public function regencies($provinceId = null)
    {
        if (!$provinceId) return $this->fail('Missing provinceId', 400);

        $data = $this->db->table('tb_regencies')
            ->select('id, name')
            ->where('province_id', $provinceId)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }

    public function districts($regencyId = null)
    {
        if (!$regencyId) return $this->fail('Missing regencyId', 400);

        $data = $this->db->table('tb_districts')
            ->select('id, name')
            ->where('regency_id', $regencyId)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }

    public function villages($districtId = null)
    {
        if (!$districtId) return $this->fail('Missing districtId', 400);

        $data = $this->db->table('tb_villages')
            ->select('id, name')
            ->where('district_id', $districtId)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }

    public function lookup($villageId = null)
    {
        if (!$villageId) return $this->fail('Missing villageId', 400);

        $result = $this->db->table('tb_villages v')
            ->select("
            v.id AS desa_id, v.name AS desa,
            d.id AS kecamatan_id, d.name AS kecamatan,
            r.id AS kabupaten_id, r.name AS kabupaten,
            p.id AS provinsi_id, p.name AS provinsi
        ")
            ->join('tb_districts d', 'd.id = v.district_id', 'left')
            ->join('tb_regencies r', 'r.id = d.regency_id', 'left')
            ->join('tb_provinces p', 'p.id = r.province_id', 'left')
            ->where('v.id', $villageId)
            ->get()
            ->getRowArray();

        if (!$result) {
            return $this->failNotFound('Kode desa tidak ditemukan.');
        }

        return $this->response->setJSON($result);
    }
}
