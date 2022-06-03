<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table      = 'tb_roles';
    protected $primaryKey = 'id_role';

    protected $allowedFields = ['id_role', 'nm_role'];
    // protected $beforeInsert = ['beforeInsert'];
    // protected $beforeUpdate = ['beforeUpdate'];

    public function getRole()
    {
        $builder = $this->db->table('tb_roles');
        $builder->select('*');
        $builder->orderBy('id_role', 'asc');
        $query = $builder->get();

        return $query;
    }
}
