<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CsvKet extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'ck_id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'ck_nama'       => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'     		=> true,
			]
		]);
		// Membuat primary key
		$this->forge->addKey('ck_id', TRUE);

		// Membuat tabel news
		$this->forge->createTable('tb_csv_ket', TRUE);
	}

	public function down()
	{
		$this->forde->dropTable('tb_csv_ket');
	}
}
