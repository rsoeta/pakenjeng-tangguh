<?php

//CountryModel.php

namespace App\Models\Dtks\Ppks;

use CodeIgniter\Model;

class PpksKatModel extends Model
{

	protected $table = 'ppks_kategori';
	protected $primaryKey = 'pk_id ';
	protected $allowedFields = ['pk_nama_kategori'];

	public function getKat($id = false)
	{
		if ($id == false) {
			return $this->findAll();
		}

		return $this->asArray()
			->where(['pk_id ' => $id])
			->first();
	}
}
