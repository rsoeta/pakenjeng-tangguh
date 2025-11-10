<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDtsenArt extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_art' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_kk' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nik' => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'hubungan_keluarga' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P'], 'null' => true],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true],
            'status_kawin' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'pendidikan_terakhir' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'pekerjaan' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'disabilitas' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'status_hamil' => ['type' => 'ENUM', 'constraint' => ['Tidak', 'Ya'], 'default' => 'Tidak'],
            'tgl_hamil' => ['type' => 'DATE', 'null' => true],
            'foto_identitas' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'created_by' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
        ]);
        $this->forge->addKey('id_art', true);
        $this->forge->addForeignKey('id_kk', 'dtsen_kk', 'id_kk', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dtsen_art');
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_art');
    }
}
