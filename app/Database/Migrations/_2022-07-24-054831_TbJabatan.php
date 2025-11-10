<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbJabatan extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'tj_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			],
			'tj_nama' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'tj_role' => [
				'type' => 'INT',
				'constraint' => 11,
			],
			'tj_deskripsi' => [
				'type' => 'VARCHAR',
				'constraint' => '120',
				'null' => TRUE,
			],
			'tj_created_at' => [
				'type' => 'DATETIME',
				'null' => TRUE,
			],
			'tj_updated_at' => [
				'type' => 'DATETIME',
				'null' => TRUE,
			],
		]);
		$this->forge->addKey('tj_id', TRUE);
		$this->forge->createTable('tb_jabatan');
	}

	public function down()
	{
		$this->forge->dropTable('tb_jabatan');
	}
}
