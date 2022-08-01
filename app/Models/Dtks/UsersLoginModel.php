<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class UsersLoginModel extends Model
{

	protected $table = 'dtks_users_login';
	protected $primaryKey = 'dul_id';
	protected $allowedFields = ['dul_du_id', 'dul_last_activity', 'dul_ip_address', 'dul_user_agent', 'dul_token'];

	public function save_data($data)
	{
		$this->insert($data);
	}

	public function update_data($id, $data)
	{
		$this->update($id, $data);
	}

	public function getUserLogged()
	{
		$builder = $this->table('dtks_users_login');
		$builder->select('*');
		$builder->join('dtks_users', 'dtks_users_login.dul_du_id = dtks_users.id');
		$builder->where('dul_last_activity >', date('Y-m-d H:i:s', strtotime('-5 minutes')));
		$builder->orderBy('dul_last_activity', 'desc');
		$query = $builder->get();

		return $query->getResultArray();
	}
}
