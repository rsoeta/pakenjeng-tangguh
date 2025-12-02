<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToDtsenTables extends Migration
{
    public function up()
    {
        /*
        |------------------------------------------
        | 🔵 TABEL dtsen_kk
        |------------------------------------------
        */
        if (!$this->db->fieldExists('deleted_at', 'dtsen_kk')) {
            $this->forge->addColumn('dtsen_kk', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'updated_at'
                ],
            ]);
        }

        if (!$this->db->fieldExists('delete_reason', 'dtsen_kk')) {
            $this->forge->addColumn('dtsen_kk', [
                'delete_reason' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'deleted_at'
                ],
            ]);
        }

        /*
        |------------------------------------------
        | 🟡 TABEL dtsen_art
        |------------------------------------------
        */
        if (!$this->db->fieldExists('deleted_at', 'dtsen_art')) {
            $this->forge->addColumn('dtsen_art', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
            ]);
        }

        if (!$this->db->fieldExists('delete_reason', 'dtsen_art')) {
            $this->forge->addColumn('dtsen_art', [
                'delete_reason' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
            ]);
        }

        /*
        |------------------------------------------
        | 🟠 TABEL dtsen_usulan_art
        |------------------------------------------
        */
        if (!$this->db->fieldExists('deleted_at', 'dtsen_usulan_art')) {
            $this->forge->addColumn('dtsen_usulan_art', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
            ]);
        }

        if (!$this->db->fieldExists('delete_reason', 'dtsen_usulan_art')) {
            $this->forge->addColumn('dtsen_usulan_art', [
                'delete_reason' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
            ]);
        }

        /*
        |------------------------------------------
        | 🔒 Foreign Key dtsen_se → dtsen_kk
        | (Opsional, Aman Ditambahkan)
        |------------------------------------------
        */
        // Pastikan constraint belum ada sebelum menambah
        $fkName = 'fk_se_kk';

        $constraints = $this->db->query("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'dtsen_se'
            AND CONSTRAINT_NAME = '{$fkName}'
        ")->getResult();

        if (empty($constraints)) {
            $this->forge->addForeignKey(
                'id_kk',
                'dtsen_kk',
                'id_kk',
                '',
                'CASCADE',
                $fkName
            );
        }
    }

    public function down()
    {
        // Remove columns safely
        if ($this->db->fieldExists('deleted_at', 'dtsen_kk')) {
            $this->forge->dropColumn('dtsen_kk', 'deleted_at');
        }

        if ($this->db->fieldExists('delete_reason', 'dtsen_kk')) {
            $this->forge->dropColumn('dtsen_kk', 'delete_reason');
        }

        if ($this->db->fieldExists('deleted_at', 'dtsen_art')) {
            $this->forge->dropColumn('dtsen_art', 'deleted_at');
        }

        if ($this->db->fieldExists('delete_reason', 'dtsen_art')) {
            $this->forge->dropColumn('dtsen_art', 'delete_reason');
        }

        if ($this->db->fieldExists('deleted_at', 'dtsen_usulan_art')) {
            $this->forge->dropColumn('dtsen_usulan_art', 'deleted_at');
        }

        if ($this->db->fieldExists('delete_reason', 'dtsen_usulan_art')) {
            $this->forge->dropColumn('dtsen_usulan_art', 'delete_reason');
        }

        // Drop FK jika ada
        $this->forge->dropForeignKey('dtsen_se', 'fk_se_kk');
    }
}
