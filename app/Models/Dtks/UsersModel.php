<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class UsersModel extends Model
{

	protected $table = 'dtks_users';

	protected $primaryKey = 'id';

	protected $allowedFields = ['nik', 'username', 'fullname', 'email', 'password', 'status', 'level', 'role_id', 'kode_desa', 'nope', 'opr_sch', 'user_image', 'created_at', 'updated_at'];

	protected function beforeInsert(array $data)
	{
		$data = $this->passwordHash($data);

		return $data;
	}

	protected function passwordHash(array $data)
	{
		if (isset($data['data']['password']))
			$data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

		return $data;
	}

	public function getUser()
	{
		$id = session()->get('id');
		$builder = $this->db->table('dtks_users');
		$builder->select('*');
		$builder->join('tb_roles', 'tb_roles.id_role = dtks_users.role', 'left');
		$builder->join('tb_des_kel', 'tb_des_kel.KodeDesa = dtks_users.desa_id', 'left');
		$builder->where('dtks_users.id', $id);
		$query = $builder->get();

		return $query;
	}

	public function getFindAll()
	{
		$builder = $this->db->table('dtks_users');
		$builder->select('dtks_users.id, nik, username, fullname, email, password, status, level, role_id, kode_desa, tb_villages.id as desa_id, tb_villages.name as nama_desa, nope, user_image, created_at, updated_at');
		$builder->join('tb_roles', 'tb_roles.id_role = dtks_users.role_id', 'left');
		$builder->join('tb_villages', 'tb_villages.id = dtks_users.kode_desa', 'left');
		$builder->orderBy('created_at', 'asc');
		$query = $builder->get();

		return $query;
	}

	public function update_status($uid, $ustatus)
	{

		if ($ustatus == 1) {
			$status = 0;
		} else {
			$status = 1;
		}

		$data = [
			'status' => $status,
		];

		$builder = $this->db->table('dtks_users');
		$builder->where('id', $uid);
		$query = $builder->update($data);

		return $query;
	}

	public function getSchool()
	{
		$builder = $this->db->table('dtks_users');
		$builder->select('opr_sch');
		$builder->distinct();
		$query = $builder->get();

		return $query;
	}
}
