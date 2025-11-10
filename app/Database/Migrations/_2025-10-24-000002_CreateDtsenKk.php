<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDtsenKk extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kk' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_rt' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'no_kk' => ['type' => 'VARCHAR', 'constraint' => '20'],
            'kepala_keluarga' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'status_kepemilikan_rumah' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'jumlah_anggota' => ['type' => 'INT', 'constraint' => 3, 'null' => true],
            'program_bansos' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'kategori_adat' => ['type' => 'ENUM', 'constraint' => ['Tidak', 'Ya'], 'default' => 'Tidak'],
            'nama_suku' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'foto_kk' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'created_by' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
        ]);
        $this->forge->addKey('id_kk', true);
        $this->forge->addForeignKey('id_rt', 'dtsen_rt', 'id_rt', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dtsen_kk');
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_kk');
    }
}
