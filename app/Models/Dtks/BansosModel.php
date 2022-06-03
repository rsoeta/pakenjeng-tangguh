<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class BansosModel extends Model
{

	protected $table = 'dtks_bansos_jenis';

	protected $primaryKey = 'dbj_id';

	protected $allowedFields = ['dbj_nama_bansos', 'dbj_ket_bansos'];

	public function getBansos($id = false)
	{
		if ($id == false) {
			return $this->findAll();
		}

		return $this->asArray()
			->where(['dbj_id' => $id])
			->first();
	}
}
