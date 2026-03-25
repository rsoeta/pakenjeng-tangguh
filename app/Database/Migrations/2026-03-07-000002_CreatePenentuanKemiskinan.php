<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenentuanKemiskinan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'dtsen_kk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status_kemiskinan' => [
                'type'       => 'ENUM',
                'constraint' => ['miskin', 'tidak_miskin'],
            ],
            'status_verifikasi' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'     => 'BIGINT',
                'null'     => true,
            ],
            'verified_by' => [
                'type'     => 'BIGINT',
                'null'     => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('dtsen_kk_id');

        $this->forge->addForeignKey(
            'dtsen_kk_id',
            'dtsen_kk',
            'id_kk',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('dtsen_penentuan_kemiskinan');
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_penentuan_kemiskinan');
    }
}
