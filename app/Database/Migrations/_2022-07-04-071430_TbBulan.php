<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbBulan extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'tb_id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'tb_nama'      => [
				'type'          => 'VARCHAR',
				'constraint'    => '100',
				'null'        	=> true
			],
		]);
		$this->forge->addKey('tb_id', true);

		$this->forge->createTable('tb_bulan');
	}

	public function down()
	{
		$this->forge->dropTable('tb_bulan');
	}
}
