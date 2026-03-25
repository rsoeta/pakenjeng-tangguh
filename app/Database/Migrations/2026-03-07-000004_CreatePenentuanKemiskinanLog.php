<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenentuanKemiskinanLog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'penentuan_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'aksi' => [
                'type'       => 'ENUM',
                'constraint' => ['create', 'update', 'validate', 'reject'],
            ],
            'status_kemiskinan' => [
                'type'       => 'ENUM',
                'constraint' => ['miskin', 'tidak_miskin'],
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('penentuan_id');

        $this->forge->createTable('dtsen_penentuan_kemiskinan_log');
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_penentuan_kemiskinan_log');
    }
}
