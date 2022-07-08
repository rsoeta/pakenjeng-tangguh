<?php

namespace App\Controllers;

use App\Models\Dtks\Usulan22Model;
use App\Models\WilayahModel;

class Landing extends BaseController
{
	public function __construct()
	{
		$this->WilayahModel = new WilayahModel();
	}

	public function index()
	{
		$district_id = Profil_Admin()['kode_kec'];
		$getDesa = $this->WilayahModel->getDesa($district_id);

		$data = [
			'title' => 'Landing',
			'getDesa' => $getDesa,
		];
		return view('landing', $data);
	}

	public function maintenance()
	{
		return view('maintenance2');
	}

	public function cek_usulan()
	{
		if ($this->request->getPost()) {

			// dd($this->request->getPost());
			$db = \Config\Database::connect();
			$model = new Usulan22Model();

			$cek_desa = $this->request->getPost('cek_desa');
			$cek_nik = $this->request->getPost('cek_nik');

			// $data = [
			// 	'tampildata' => $model->getDtks()
			// ];
			// var_dump($data['tampildata']);
			// die;
			// get data dari table dtks_usulan22 where nik = $cek_nik and kode_kec = $cek_desa
			$data = [
				'title' => 'Cek Usulan DTKS',
				'cek_desa' => $cek_desa,
				'cek_nik' => $cek_nik,
				'tampilData' => $model->getHasilPencarian($cek_desa, $cek_nik)->getResultArray(),
			];
			// $cek = $db->table('dtks_usulan22')->select('*')->where(['kelurahan' => $cek_desa, 'du_nik' => $cek_nik]);
			// $cek->select('*');
			// $cek->join('pbb_detailtrans22', 'pbb_detailtrans22.nop = pbb_dhkp22.nop');
			// $cek->join('pbb_transaksi22', 'pbb_transaksi22.tr_faktur = pbb_detailtrans22.dettr_faktur');

			// select all data
			// $data = $cek->get()->getResultArray();
			// var_dump($data['tampilData']);
			// die;
			// get data $tampilData


			if ($cek_desa == "" || $cek_nik == "") {
				$msg = [
					'error' => 'Silahkan isi data terlebih dahulu!'
				];
			} else if (empty($data['tampilData'])) {
				$msg = [
					'null' => 'Mohon Maaf, data tidak ditemukan!'
				];
			} else {
				$msg  = [
					'data' => view('hasil_pencarian', $data)
				];
				// var_dump($msg['data']);
				// die;
			}

			echo json_encode($msg);
		}
	}
}
