<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class LembagaModel extends Model
{

	protected $table = 'lembaga_kategori';

	protected $primaryKey = 'lk_id';

	protected $allowedFields = ['lk_nama'];

	public function getLembaga($id = false)
	{
		if ($id == false) {
			return $this->findAll();
		}

		return $this->asArray()
			->where(['lk_id' => $id])
			->first();
	}
}
