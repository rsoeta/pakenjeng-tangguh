<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbVersion extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'tv_id'          => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'tv_version'     => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
			],
			'tv_description' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'	 	 => true,
			],
			'tv_created_at'  => [
				'type'       => 'DATETIME',
				'null'       => true,
			],
			'tv_updated_at'  => [
				'type'       => 'DATETIME',
				'null'       => true,
			],
			'tv_deleted_at'  => [
				'type'       => 'DATETIME',
				'null'       => true,
			],
		]);
		$this->forge->addKey('tv_id', true);
		$this->forge->createTable('tb_version');
	}

	public function down()
	{
		//
	}
}
