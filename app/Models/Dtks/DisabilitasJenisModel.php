<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class DisabilitasJenisModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'tb_disabil_jenis';
    protected $primaryKey           = 'dj_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDelete        = false;
    protected $protectFields        = true;

    protected $allowedFields        = [
        'dj_kode',
        'dj_keterangan'
    ];

    protected $useTimestamps        = false;
    // protected $dateFormat           = 'datetime';
    // protected $createdField         = 'created_at';
    // protected $updatedField         = 'updated_at';
    // protected $deletedField         = 'deleted_at';
    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = ["beforeInsert"];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    protected function beforeInsert(array $data)
    {
        $data = $this->passwordHash($data);

        return $data;
    }
}
