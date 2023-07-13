<?php

//CountryModel.php

namespace App\Models;

use CodeIgniter\Model;

class DeadlineModel extends Model
{

	protected $table = 'dtks_deadline';
	protected $primaryKey = 'dd_id';
	protected $allowedFields = ['dd_id', 'dd_waktu_start', 'dd_waktu_end', 'dd_role', 'dd_deskripsi'];


	public function submit_general($data)
	{
		return $this->db
			->table('dtks_deadline')
			->set($data)
			->insert();
	}

	public function update_batch($data, $primaryKey)
	{
		$builder = $this->db->table($this->table);

		$ids = array_column($data, $primaryKey); // Mendapatkan array dari kunci utama

		foreach ($data as $row) {
			$builder->where($primaryKey, $row[$primaryKey]);
			$builder->set($row);
			$builder->update();
		}

		return count($ids); // Mengembalikan jumlah data yang diupdate
	}
}
