<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDtsenUsulanTables extends Migration
{
    public function up()
    {
        // Tabel utama: dtsen_usulan
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'auto_increment' => true],
            'usulan_no'         => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'jenis'             => [
                'type'       => 'ENUM',
                'constraint' => ['pembaruan', 'keluarga_baru', 'individu_baru', 'lainnya'],
                'default'    => 'pembaruan'
            ],
            'status'            => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'submitted', 'verified', 'applied', 'rejected'],
                'default'    => 'draft'
            ],
            'dtsen_kk_id'       => ['type' => 'INT', 'null' => true],
            'no_kk_target'      => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'created_by'        => ['type' => 'INT', 'null' => false],
            'assigned_to'       => ['type' => 'INT', 'null' => true],
            'payload'           => ['type' => 'JSON', 'null' => false],
            'summary'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'verified_at'       => ['type' => 'DATETIME', 'null' => true],
            'applied_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        // HAPUS baris duplikat: $this->forge->addKey('usulan_no');
        $this->forge->addKey('no_kk_target');
        $this->forge->addKey('status');
        // $this->forge->addForeignKey('dtsen_kk_id', 'dtsen_kk', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('dtsen_usulan', true);

        // Tabel anggota dalam usulan
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'auto_increment' => true],
            'dtsen_usulan_id'   => ['type' => 'INT', 'null' => false],
            'nik'               => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'nama'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'hubungan'          => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'payload_member'    => ['type' => 'JSON', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('nik');
        $this->forge->addForeignKey('dtsen_usulan_id', 'dtsen_usulan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dtsen_usulan_art', true);
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_usulan_art', true);
        $this->forge->dropTable('dtsen_usulan', true);
    }
}
