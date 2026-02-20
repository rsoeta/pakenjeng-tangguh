<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PbiReaktivasi extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'nik' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
			],
			'nama_snapshot' => [
				'type' => 'VARCHAR',
				'constraint' => 150,
			],
			'status_pbi_awal' => [
				'type' => 'ENUM',
				'constraint' => ['aktif', 'nonaktif'],
			],
			'desil_snapshot' => [
				'type' => 'INT',
				'constraint' => 11,
			],
			'alasan' => [
				'type' => 'TEXT',
			],
			'kondisi_mendesak' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'surat_faskes' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'status_pengajuan' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'catatan_pentri' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'catatan_desa' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'tanggal_draft' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'tanggal_diajukan' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'tanggal_verifikasi' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'tanggal_keputusan' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'tanggal_kirim_siks' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'tanggal_respon_kab' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'created_by' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
			],
			'verified_by' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'null' => true,
			],
			'keputusan_by' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'null' => true,
			],
			'desa_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addKey('nik');
		$this->forge->addKey('status_pengajuan');
		$this->forge->addKey('desa_id');
		$this->forge->createTable('pbi_reaktivasi');
	}

	public function down()
	{
		$this->forge->dropTable('pbi_reaktivasi');
	}
}
