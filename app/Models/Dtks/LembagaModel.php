<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class LembagaModel extends Model
{

	protected $table = 'lembaga_kategori';

	protected $primaryKey = 'lk_id';

	protected $allowedFields = ['lk_nama'];

	public function getLembaga($role)
	{
		if ($role == false) {
			return $this->orderby('lk_id', 'desc')->findAll();
		} else {
			// return getrow
			return $this->db->table($this->table)->where('lk_id', $role)->get();
		}
	}

	public function submitLembagaData($lembagaData)
	{
		return $this->db
			->table('lembaga_profil')
			->set($lembagaData)
			->insert();
	}

	public function updateLembagaData($lp_id, $lembagaData)
	{
		return $this->db
			->table('lembaga_profil')
			->where("lp_id", $lp_id)
			->set($lembagaData)
			->update();
	}
}
