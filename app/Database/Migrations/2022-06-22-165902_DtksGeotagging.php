<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DtksGeotagging extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'vg_id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'vg_nik'      => [
				'type'          => 'VARCHAR',
				'constraint'    => '16',
				'null'        	=> true
			],
			'vg_nama_lengkap' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '255',
				'null'           => true,
			],
			'vg_nkk'      => [
				'type'          => 'VARCHAR',
				'constraint'    => '16',
				'null'        	=> true
			],
			'vg_alamat'       => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'     		=> true,
			],
			'vg_rw'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'null'		=> true,
			],
			'vg_rt'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'null'		=> true,
			],
			'vg_desa'      => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'        	=> true
			],
			'vg_kec'       => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'     		=> true,
			],
			'vg_kab'       => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'     		=> true,
			],
			'vg_prov'       => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'     		=> true,
			],
			'vg_dbj_id1' => [
				'type'           => 'INT',
				'constraint'	=> 5,
				'null'           => true,
			],
			'vg_dbj_id2' => [
				'type'           => 'INT',
				'constraint'	=> 5,
				'null'           => true,
			],
			'vg_norek' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '100',
				'null'           => true,
			],
			'vg_source' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '100',
				'null'           => true,
			],
			'vg_fp' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '100',
				'null'           => true,
			],
			'vg_fr' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '100',
				'null'           => true,
			],
			'vg_lat'      => [
				'type'           => 'VARCHAR',
				'constraint'	=> '255',
				'null'           => true,
			],
			'vg_lang'      => [
				'type'           => 'VARCHAR',
				'constraint'	=> '255',
				'null'           => true,
			],
			'vg_ds_id' => [
				'type'           => 'INT',
				'constraint'	=> 5,
				'null'           => true,
			],
			'vg_sta_id' => [
				'type'           => 'INT',
				'constraint'	=> 5,
				'null'           => true,
			],
			'vg_created_by'     => [
				'type'          => 'VARCHAR',
				'constraint'	=> '255',
				'null'          => true,
			],
			'vg_created_at'     => [
				'type'          => 'DATETIME',
				'null'          => true,
			],
			'vg_updated_by'     => [
				'type'          => 'VARCHAR',
				'constraint'	=> '255',
				'null'          => true,
			],
			'vg_updated_at DATETIME DEFAULT CURRENT_TIMESTAMP'
		]);
		// Membuat primary key
		$this->forge->addKey('vg_id', TRUE);

		// Membuat tabel news
		$this->forge->createTable('dtks_verivali_geo', TRUE);
	}

	public function down()
	{
		$this->forge->dropTable('dtks_verivali_geo');
	}
}
