<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'dtks_users';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDelete        = false;
    protected $protectFields        = true;

    protected $allowedFields        = [
        'nik', 'username', 'fullname', 'email', 'password', 'status', 'level', 'role_id', 'kode_desa', 'kode_kec', 'kode_kab', 'nope', 'opr_sch', 'jabatan', 'user_image', 'created_at', 'updated_at'
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

    protected function passwordHash(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    public function getUserId()
    {
        $logDtks = session()->get('logDtks');

        // if ($logDtks !== null) {
        $user_id = session()->get('id');
        $builder = $this->db->table('dtks_users');
        $builder->select('dtks_users.id as id_user, dtks_users.password, dtks_users.nik, dtks_users.fullname, dtks_users.email, dtks_users.nope, dtks_users.opr_sch, dtks_users.kode_desa, dtks_users.kode_kec, dtks_users.kode_kab, dtks_users.user_image, dtks_users.user_lembaga_id, dtks_users.created_at, dtks_users.updated_at, tb_roles.id_role as role_id, tb_roles.nm_role, lembaga_profil.lp_id, lembaga_profil.lp_kode, lembaga_profil.lp_kepala, lembaga_profil.lp_nip, lembaga_profil.lp_sekretariat, lembaga_profil.lp_email, lembaga_profil.lp_kode_pos, lembaga_profil.lp_logo, lembaga_kategori.lk_nama, tb_villages.name as nama_desa');

        $builder->join('tb_roles', 'dtks_users.role_id=tb_roles.id_role');
        $builder->join('lembaga_profil', 'dtks_users.id=lembaga_profil.lp_user');
        $builder->join('lembaga_kategori', 'dtks_users.role_id=lembaga_kategori.lk_id');
        $builder->join('tb_villages', 'dtks_users.kode_desa=tb_villages.id');
        $query = $builder->getWhere(['dtks_users.id' => $user_id])->getRowArray();


        $buildor = $this->db->table('dtks_users');
        $buildor->select('dtks_users.id as id_user, dtks_users.password, dtks_users.nik, dtks_users.fullname, dtks_users.email, dtks_users.nope, dtks_users.kode_desa, dtks_users.kode_kec, dtks_users.kode_kab, dtks_users.opr_sch, dtks_users.user_image, dtks_users.user_lembaga_id, dtks_users.created_at, dtks_users.updated_at, tb_roles.id_role as role_id, tb_roles.nm_role');

        $buildor->join('tb_roles', 'dtks_users.role_id=tb_roles.id_role');
        // $buildor->join('lembaga_profil', 'dtks_users.id=lembaga_profil.lp_user');
        // $buildor->join('lembaga_kategori', 'lembaga_profil.lp_kategori=lembaga_kategori.lk_id');
        // $buildor->join('tb_villages', 'lembaga_profil.lp_kode=tb_villages.id');
        $quero = $buildor->where('dtks_users.id', $user_id);
        $quero = $quero->get()->getRowArray();

        if ($query !== null) {
            return $query;
        }
        return $quero;
        // } else {
        //     return redirect()->to(base_url('/'));
        // }
    }

    public function updatePersonalData($id_user, $personalData)
    {
        return $this->db
            ->table('dtks_users')
            ->where(["id" => $id_user])
            ->set($personalData)
            ->update();
    }
}
