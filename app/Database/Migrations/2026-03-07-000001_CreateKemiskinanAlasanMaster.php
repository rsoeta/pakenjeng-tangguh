<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKemiskinanAlasanMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'label' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'status_kemiskinan' => [
                'type'       => 'ENUM',
                'constraint' => ['miskin', 'tidak_miskin'],
            ],
            'urutan' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'default'    => 1,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('dtsen_kemiskinan_alasan_master');
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_kemiskinan_alasan_master');
    }
}
