<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tbstatus extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'sta_id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'sta_nama'       => [
				'type'          => 'VARCHAR',
				'constraint'    => '255',
				'null'     		=> true,
			]
		]);
		// Membuat primary key
		$this->forge->addKey('sta_id', TRUE);

		// Membuat tabel news
		$this->forge->createTable('tb_status', TRUE);
	}

	public function down()
	{
		$this->forde->dropTable('tb_status');
	}
}
