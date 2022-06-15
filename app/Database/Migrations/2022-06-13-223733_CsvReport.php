<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CsvReport extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'cr_id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'cr_nama_kec'       => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'     		=> true,
			],
			'cr_nama_desa'      => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'        	=> true
			],
			'cr_nik_usulan'      => [
				'type'          => 'VARCHAR',
				'constraint'    => '16',
				'null'        	=> true
			],
			'cr_program_bansos' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '100',
				'null'           => true,
			],
			'cr_hasil' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '100',
				'null'           => true,
			],
			'cr_padan' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '100',
				'null'           => true,
			],
			'cr_nama_lgkp' => [
				'type'           => 'VARCHAR',
				'constraint'	=> '255',
				'null'           => true,
			],
			'cr_ket_vali'      => [
				'type'           => 'VARCHAR',
				'constraint'	=> '255',
				'null'           => true,
			],
			'cr_created_by'     => [
				'type'          => 'VARCHAR',
				'constraint'	=> '255',
				'null'          => true,
			],
			'cr_created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
		]);
		// Membuat primary key
		$this->forge->addKey('cr_id', TRUE);

		// Membuat tabel news
		$this->forge->createTable('dtks_csv_report', TRUE);
	}

	public function down()
	{
		//
		$this->forge->dropTable('dtks_csv_report');
	}
}
