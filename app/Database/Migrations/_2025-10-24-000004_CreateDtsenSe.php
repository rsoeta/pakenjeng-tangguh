<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDtsenSe extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_se' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_rt' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_kk' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'sumber_penghasilan' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'rata_penghasilan_bulanan' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'rata_pengeluaran_bulanan' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'kepemilikan_aset' => ['type' => 'TEXT', 'null' => true],
            'kepemilikan_bantuan' => ['type' => 'TEXT', 'null' => true],
            'status_kks' => ['type' => 'ENUM', 'constraint' => ['Ya', 'Tidak'], 'default' => 'Tidak'],
            'status_bpjs' => ['type' => 'ENUM', 'constraint' => ['Ya', 'Tidak'], 'default' => 'Tidak'],
            'status_kip' => ['type' => 'ENUM', 'constraint' => ['Ya', 'Tidak'], 'default' => 'Tidak'],
            'catatan_tambahan' => ['type' => 'TEXT', 'null' => true],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'created_by' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
        ]);
        $this->forge->addKey('id_se', true);
        $this->forge->addForeignKey('id_rt', 'dtsen_rt', 'id_rt', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kk', 'dtsen_kk', 'id_kk', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dtsen_se');
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_se');
    }
}
