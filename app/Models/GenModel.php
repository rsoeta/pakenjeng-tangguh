<?php

//CountryModel.php

namespace App\Models;

use CodeIgniter\Model;

class GenModel extends Model
{

	protected $table = 'tb_shdk';

	protected $primaryKey = 'id';

	protected $allowedFields = ['jenis_shdk'];

	public function getDataJenkel()
	{
		$builder = $this->db->table('tbl_jenkel');
		$query = $builder->get()->getResultArray();

		// foreach ($query as $row) {
		// }

		return $query;
	}

	public function getDataStatusKawin()
	{
		$builder = $this->db->table('tb_status_kawin');
		$query = $builder->get()->getResultArray();

		return $query;
	}

	public function getDataVerivaliPbi()
	{
		$builder = $this->db->table('dtks_verivali_pbi');
		$builder->orderBy('vp_keterangan', 'asc');
		$query = $builder->get()->getResultArray();

		return $query;
	}

	public function getStatusRole()
	{
		$builder = $this->db->table('tb_roles');
		$query = $builder->get()->getResultArray();

		return $query;
	}

	public function getStatusPs()
	{
		$builder = $this->db->table('tb_sekolah_partisipasi');
		$query = $builder->get();

		return $query;
	}

	public function getSekolahJenjang()
	{
		$builder = $this->db->table('tb_sekolah_jenjang');
		$query = $builder->get();

		return $query;
	}

	// function id data tb_penduduk_pekerjaan
	public function getPendudukPekerjaan()
	{
		$builder = $this->db->table('tb_penduduk_pekerjaan');
		$query = $builder->get();

		return $query;
	}

	// function getdata from tb_ket_anomali
	public function getDataKetAnomali()
	{
		$builder = $this->db->table('tb_ket_anomali');
		$query = $builder->get();

		return $query;
	}

	// function getdata from tb_status2
	public function getStatusDtks()
	{
		$builder = $this->db->table('dtks_status2');
		$query = $builder->get();

		return $query;
	}
}
