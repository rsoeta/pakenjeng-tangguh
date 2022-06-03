<?php

//CountryModel.php

namespace App\Models;

use CodeIgniter\Model;

class RwModel extends Model
{

	protected $table = 'tb_rw';

	protected $primaryKey = 'id';

	protected $allowedFields = ['kode_desa', 'no_dusun', 'no_rw', 'nama_ketua_rw'];

	public function noRw()
	{
		$role = session()->get('role_id');
		$desa = session()->get('kode_desa');
		$level = session()->get('level');

		if ($level == null && $role == 1) {
			$builder = $this->db->table('tb_rw');
			$builder->select('no_rw');
			$builder->distinct();
			$query = $builder->get();
		} elseif ($level == null && $role == 2) {
			$builder = $this->db->table('tb_rw');
			$builder->select('no_rw');
			$builder->distinct();
			$query = $builder->get();
		} elseif ($role == 3 && $level == null) {
			$builder = $this->db->table('tb_rw');
			$builder->where('kode_desa', $desa);
			$builder->select('no_rw')->distinct();
			$query = $builder->get();
		} elseif ($role == 4 && $level !== null) {
			$builder = $this->db->table('tb_rw');
			$builder->where('kode_desa', $desa);
			$builder->where('no_rw', $level);
			$builder->select('no_rw')->distinct();
			$query = $builder->get();
		} else {
			$builder = $this->db->table('tb_rw');
			$builder->where('kode_desa', $desa);
			$builder->where('no_rw', '000');
			$builder->select('no_rw')->distinct();
			$query = $builder->get();
		}

		return $query->getResultArray();
	}
}
