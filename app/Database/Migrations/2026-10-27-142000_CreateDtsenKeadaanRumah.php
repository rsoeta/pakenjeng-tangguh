<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDtsenKeadaanRumah extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kk' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'status_kepemilikan' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],
            'jenis_lantai' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],
            'jenis_dinding' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],
            'jenis_atap' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],
            'kategori_rumah' => [
                'type'           => 'ENUM',
                'constraint'     => ['Numpang', 'Panggung', 'Permanen', 'Semi Permanen'],
            ],
            'sumber_penilaian' => [
                'type'           => 'ENUM',
                'constraint'     => ['otomatis', 'manual'],
                'default'        => 'otomatis',
            ],
            'catatan' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'created_by' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],
            'updated_by' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('id_kk');
        $this->forge->addKey('kategori_rumah');

        $this->forge->createTable('dtsen_keadaan_rumah', true);
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_keadaan_rumah', true);
    }
}
