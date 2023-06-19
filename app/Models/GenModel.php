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

	function getDeadlinePpks()
	{
		$role = session()->get('role');
		$akses1 = 3;
		$akses2 = 4;

		$builder = $this->db->table('ppks_deadline');
		// get last row
		$builder = $builder->select('*');
		if ($role <= $akses1) {
			$builder = $builder->join('tb_roles', 'ppks_deadline.dd_role = tb_roles.id_role');
			$query = $builder->get();
			return $query->getResultArray();
		} elseif ($role >= $akses2) {
			$builder = $builder->join('tb_roles', 'ppks_deadline.dd_role = tb_roles.id_role');
			$query = $builder->where('dd_role', $akses2)->get();
			return $query->getResultArray();
		} else {
			$query = $builder->where('dd_role', null)->get();
			return $query->getResultArray();
		}
	}

	function get_staortu()
	{
		$builder = $this->db->table('tb_status_ortu');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_sta_bangteti()
	{
		$builder = $this->db->table('tb_sta_bangteti');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_sta_lahteti()
	{
		$builder = $this->db->table('tb_sta_lahteti');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_jenlai()
	{
		$builder = $this->db->table('tb_jenlai');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_jendin()
	{
		$builder = $this->db->table('tb_jendin');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_jentap()
	{
		$builder = $this->db->table('tb_jentap');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_kondisi()
	{
		$builder = $this->db->table('tb_kondisi');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_penghasilan()
	{
		$builder = $this->db->table('tb_penghasilan');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_pengeluaran()
	{
		$builder = $this->db->table('tb_pengeluaran');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_jml_tanggungan()
	{
		$builder = $this->db->table('tb_jml_tanggungan');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_roda_dua()
	{
		$builder = $this->db->table('tb_roda_dua');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_sumber_minum()
	{
		$builder = $this->db->table('tb_sumber_minum');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_cara_minum()
	{
		$builder = $this->db->table('tb_cara_minum');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_penerangan_utama()
	{
		$builder = $this->db->table('tb_penerangan_utama');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_daya_listrik()
	{
		$builder = $this->db->table('tb_daya_listrik');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_bahan_masak()
	{
		$builder = $this->db->table('tb_bahan_masak');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_tempat_bab()
	{
		$builder = $this->db->table('tb_tempat_bab');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_jenis_kloset()
	{
		$builder = $this->db->table('tb_jenis_kloset');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_tempat_tinja()
	{
		$builder = $this->db->table('tb_tempat_tinja');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}

	function get_jenis_pekerjaan()
	{
		$builder = $this->db->table('tb_jenis_pekerjaan');
		$query = $builder->select('*')->get();

		return $query->getResultArray();
	}
}
