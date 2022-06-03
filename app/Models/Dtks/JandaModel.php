<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class JandaModel extends Model
{

	protected $table = 'dtks_janda';

	protected $primaryKey = 'ID';

	protected $allowedFields = ['NO', 'NIK', 'NO_KK', 'NAMA', 'TEMPAT_LAHIR', 'TANGGAL_LAHIR', 'ALAMAT', 'RT', 'RW', 'DESA', 'KEC', 'KAB', 'FOTO_KK', 'FOTO_DIRI', 'Created_by', 'Created_at', 'Updated_by', 'Updated_at'];

	var $column_order = array(
		'ID', 'NO', 'NIK', 'NO_KK', 'NAMA', 'TEMPAT_LAHIR', 'TANGGAL_LAHIR', 'ALAMAT', 'RT', 'RW', 'DESA', 'KEC', 'KAB', 'FOTO_KK', 'FOTO_DIRI', 'Created_by', 'Created_at', 'Updated_by', 'Updated_at'
	);

	var $order = array('NAMA' => 'asc');

	function get_datatables($desa, $rw, $operator, $keterangan)
	{
		// desa
		if ($desa == "") {
			$kondisi_desa = "";
		} else {
			$kondisi_desa = " AND DESA = '$desa'";
		}

		// rw
		if ($rw == "") {
			$kondisi_rw = "";
		} else {
			$kondisi_rw = " AND RW = '$rw'";
		}
		// status
		if ($operator == "") {
			$kondisi_operator = "";
		} else {
			$kondisi_operator = " AND Created_by = '$operator'";
		}
		// keterangan
		if ($keterangan == "") {
			$kondisi_keterangan = "";
		} else {
			$kondisi_keterangan = " AND Updated_by = '$keterangan'";
		}

		// search
		if ($_POST['search']['value']) {
			$search = $_POST['search']['value'];
			$kondisi_search = "(NAMA LIKE '%$search%' OR NO_KK LIKE '%$search%' OR NIK LIKE '%$search%' OR ALAMAT LIKE '%$search%') $kondisi_desa $kondisi_rw $kondisi_operator $kondisi_keterangan";
		} else {
			$kondisi_search = "ID != '' $kondisi_desa $kondisi_rw $kondisi_operator $kondisi_keterangan";
		}

		// order
		if (isset($_POST['order'])) {
			$result_order = $this->column_order[$_POST['order']['0']['column']];
			$result_dir = $_POST['order']['0']['dir'];
		} else if ($this->order) {
			$order = $this->order;
			$result_order = key($order);
			$result_dir = $order[key($order)];
		}

		if ($_POST['length'] != -1);
		$db = db_connect();
		$builder = $db->table('dtks_janda');
		$query = $builder->select('*')
			->join('tbl_desa', 'tbl_desa.KodeDesa=dtks_janda.DESA')
			// ->join('tbl_jenkel', 'tbl_jenkel.IdJenKel=dtks_janda.JKAnak')
			// ->join('pekerjaan_kondisi_pekerjaan', 'pekerjaan_kondisi_pekerjaan.IDKondisi=individu_data.KondisiPekerjaan')
			// ->join('pendidikan_pend_tinggi', 'pendidikan_pend_tinggi.IDPendidikan=individu_data.PendTertinggi')
			// ->join('ket_verivali', 'ket_verivali.id_ketvv=individu_data.ket_verivali')
			->where($kondisi_search)
			->orderBy($result_order, $result_dir)
			->limit($_POST['length'], $_POST['start'])
			->get();

		return $query->getResult();
	}

	function jumlah_semua()
	{
		$sQuery = "SELECT COUNT(ID) as jml FROM dtks_janda";
		$db = db_connect();
		$query = $db->query($sQuery)->getRow();

		return $query;
	}

	function jumlah_filter($desa, $rw, $operator, $keterangan)
	{
		// desa
		if ($desa == "") {
			$kondisi_desa = "";
		} else {
			$kondisi_desa = " AND DESA = '$desa'";
		}

		// rw
		if ($rw == "") {
			$kondisi_rw = "";
		} else {
			$kondisi_rw = " AND RW = '$rw'";
		}

		// operator
		if ($operator == "") {
			$kondisi_operator = "";
		} else {
			$kondisi_operator = " AND Created_by = '$operator'";
		}

		// rw
		if ($keterangan == "") {
			$kondisi_keterangan = "";
		} else {
			$kondisi_keterangan = " AND Updated_by = '$keterangan'";
		}

		// kondisi search
		if ($_POST['search']['value']) {
			$search = $_POST['search']['value'];
			$kondisi_search = "AND (NAMA LIKE '%$search%' OR NO_KK LIKE '%$search%' OR NIK LIKE '%$search%' OR ALAMAT LIKE '%$search%') $kondisi_desa $kondisi_rw $kondisi_operator $kondisi_keterangan";
		} else {
			$kondisi_search = "$kondisi_desa $kondisi_rw $kondisi_operator $kondisi_keterangan";
		}

		$sQuery = "SELECT COUNT(ID) as jml FROM dtks_janda WHERE ID != '' $kondisi_search";
		$db = db_connect();
		$query = $db->query($sQuery)->getRow();

		return $query;
	}

	public function jmlJanda()
	{
		$builder = $this->db->table('dtks_janda');
		$query = $builder->select("COUNT(ID) as jml, RW");
		$query = $builder->where("RW GROUP BY RW, RW")->get();
		$record = $query->getResult();

		return $record;
	}
}
