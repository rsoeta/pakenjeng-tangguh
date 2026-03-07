<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenentuanKemiskinanAlasan extends Migration
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
            'alasan_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('penentuan_id');
        $this->forge->addKey('alasan_id');

        $this->forge->addForeignKey(
            'penentuan_id',
            'dtks_penentuan_kemiskinan',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'alasan_id',
            'dtks_kemiskinan_alasan_master',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('dtks_penentuan_kemiskinan_alasan');
    }

    public function down()
    {
        $this->forge->dropTable('dtks_penentuan_kemiskinan_alasan');
    }
}
