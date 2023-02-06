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
		$builder = $this->db->table('dtks_status');
		$builder->select('*');
		$builder->orderBy('jenis_status', 'asc');
		$query = $builder->get();

		return $query->getResult();
	}

	// function getdata from tb_status with limit
	public function getStatusLimit()
	{
		$user = session()->get('role_id');

		if ($user < 3) {
			$builder = $this->db->table('tb_status');
			$query = $builder->get();
		} elseif ($user >= 3) {
			$builder = $this->db->table('tb_status');
			$builder->limit(2);
			$query = $builder->get();
		} else {
			$builder = $this->db->table('tb_status');
			$builder->limit(1);
			$query = $builder->get();
		}

		return $query;
	}

	function getVersion()
	{
		$builder = $this->db->table('tb_version');
		// get last row

		$query = $builder->get();
		return $query->getLastRow();
	}

	function getDeadline()
	{
		$role = session()->get('role');
		$akses1 = 3;
		$akses2 = 4;

		$builder = $this->db->table('dtks_deadline');
		// get last row
		$builder = $builder->select('*');
		if ($role <= $akses1) {
			$builder = $builder->join('tb_roles', 'dtks_deadline.dd_role = tb_roles.id_role');
			$query = $builder->get();
			return $query->getResultArray();
		} elseif ($role >= $akses2) {
			$builder = $builder->join('tb_roles', 'dtks_deadline.dd_role = tb_roles.id_role');
			$query = $builder->where('dd_role', $akses2)->get();
			return $query->getResultArray();
		} else {
			$query = $builder->where('dd_role', null)->get();
			return $query->getResultArray();
		}
	}

	function submit_general($data)
	{
		return $this->db
			->table('dtks_deadline')
			->set($data)
			->insert();
	}

	function update_general($id, $data)
	{
		return $this->db
			->table('dtks_deadline')
			->where('dd_id', $id)
			->set($data)
			->update();
	}
}
