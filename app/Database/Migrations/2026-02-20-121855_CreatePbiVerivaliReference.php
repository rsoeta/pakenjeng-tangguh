<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePbiVerivaliReference extends Migration
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

            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],

            'noka_jkn' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],

            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],

            'no_kk' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],

            'desil_nasional' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],

            'kepesertaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],

            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],

            'kode_desa' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],

            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
            ],

            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
            ],

            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // PRIMARY KEY
        $this->forge->addKey('id', true);

        // 🔥 PERFORMANCE INDEX (WAJIB UNTUK DATATABLE SERVER-SIDE)
        $this->forge->addUniqueKey('nik');
        $this->forge->addKey('kode_desa');
        $this->forge->addKey(['rw', 'rt']); // Composite index
        $this->forge->addKey('status');


        $this->forge->createTable('pbi_verivali_reference');
    }

    public function down()
    {
        $this->forge->dropTable('pbi_verivali_reference');
    }
}
