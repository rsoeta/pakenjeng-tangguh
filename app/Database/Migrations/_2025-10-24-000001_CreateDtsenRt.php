<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDtsenRt extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_rt' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'kode_desa' => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
            'alamat' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'rt' => ['type' => 'VARCHAR', 'constraint' => '5', 'null' => true],
            'rw' => ['type' => 'VARCHAR', 'constraint' => '5', 'null' => true],
            'kelurahan' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'latitude' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'longitude' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'accuracy' => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
            'kepemilikan_rumah' => ['type' => 'ENUM', 'constraint' => ['Milik Sendiri', 'Kontrak', 'Menumpang', 'Lainnya'], 'default' => 'Milik Sendiri'],
            'kondisi_atap' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'kondisi_dinding' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'kondisi_lantai' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'sumber_air' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'sanitasi' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'sumber_listrik' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'foto_rumah' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'foto_rumah_dalam' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'created_by' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
        ]);
        $this->forge->addKey('id_rt', true);
        $this->forge->createTable('dtsen_rt');
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_rt');
    }
}
