<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class LembagaModel extends Model
{

	protected $table = 'lembaga_kategori';

	protected $primaryKey = 'lk_id';

	protected $allowedFields = ['lk_nama'];

	public function getLembaga($user_id = false)
	{
		if ($user_id == false) {
			return $this->orderby('lk_id', 'desc')->findAll();
		}

		return $this->asArray()
			->where('lk_id', $user_id)
			->first();
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
