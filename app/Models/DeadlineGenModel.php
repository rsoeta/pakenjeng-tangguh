<?php

//CountryModel.php

namespace App\Models;

use CodeIgniter\Model;

class DeadlineGenModel extends Model
{

	protected $table = 'ppks_deadline';
	protected $primaryKey = 'dd_id';
	protected $allowedFields = ['dd_waktu_start', 'dd_waktu_end', 'dd_role', 'dd_deskripsi'];


	public function submit_general($data)
	{
		return $this->db
			->table('ppks_deadline')
			->set($data)
			->insert();
	}

	public function updateData($data, $primaryKey = null, int $batchSize = 100, bool $returnSQL = false)
	{
		$builder = $this->db->table($this->table);
		$builder->updateBatch($data, $primaryKey, $batchSize, $returnSQL);

		if ($returnSQL) {
			return $builder->getCompiledUpdate();
		}
	}
}
