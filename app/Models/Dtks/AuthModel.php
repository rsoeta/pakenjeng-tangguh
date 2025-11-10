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
    protected $useSoftDelete        = true;
    protected $protectFields        = true;

    protected $allowedFields        = [
        'nik',
        'username',
        'fullname',
        'email',
        'password',
        'status',
        'level',
        'role_id',
        'kode_desa',
        'kode_kec',
        'kode_kab',
        'nope',
        'opr_sch',
        'jabatan_id',
        'user_image',
        'wilayah_tugas',
        'created_at',
        'updated_at',
        'reset_token',
        'reset_expiry'
    ];

    protected $useTimestamps        = true;
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
        $user_id = session()->get('id');

        $builder = $this->db->table('dtks_users u');
        $builder->select("
        u.id AS id_user,
        u.password,
        u.nik,
        u.fullname,
        u.email,
        u.level,
        u.nope,
        u.opr_sch,
        u.kode_desa,
        u.kode_kec,
        u.kode_kab,
        u.user_image,
        u.user_lembaga_id,
        u.created_at,
        u.updated_at,
        u.wilayah_tugas,
        r.id_role AS role_id,
        r.nm_role,
        lp.lp_id,
        lp.lp_kode,
        lp.lp_kepala,
        lp.lp_nip,
        lp.lp_sekretariat,
        lp.lp_email,
        lp.lp_kode_pos,
        lp.lp_logo,
        lk.lk_nama,
        v.name AS nama_desa,
        d.name AS nama_kecamatan,
        reg.name AS nama_kabupaten
    ");

        // join opsional — gunakan LEFT agar aman meskipun data lembaga kosong
        $builder->join('tb_roles r', 'u.role_id = r.id_role', 'left');
        $builder->join('lembaga_profil lp', 'u.id = lp.lp_user', 'left');
        $builder->join('lembaga_kategori lk', 'u.role_id = lk.lk_id', 'left');
        $builder->join('tb_villages v', 'u.kode_desa = v.id', 'left');
        $builder->join('tb_districts d', 'u.kode_kec = d.id', 'left');
        $builder->join('tb_regencies reg', 'u.kode_kab = reg.id', 'left');

        $builder->where('u.id', $user_id);

        $data = $builder->get()->getRowArray();

        return $data ?: false;
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
