<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DtksDeadline extends Migration
{
	public function up()
	{
		$this->forge->addField(
			[
				'dd_id' => [
					'type' => 'INT',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
				],
				'dd_waktu' => [
					'type' => 'DATETIME',
					'null' => true,
				],
				'dd_deskripsi' => [
					'type' => 'TEXT',
					'null' => true,
				],
			]
		);
		$this->forge->addKey('dd_id', true);
		$this->forge->createTable('dtks_deadline');
	}

	public function down()
	{
		$this->forge->dropTable('dtks_deadline');
	}
}
