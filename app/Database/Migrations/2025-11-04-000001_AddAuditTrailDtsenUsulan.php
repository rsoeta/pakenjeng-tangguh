<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuditTrailDtsenUsulan extends Migration
{
    public function up()
    {
        // Pastikan tabel dtsen_usulan ada
        if (!$this->db->tableExists('dtsen_usulan')) {
            echo "⚠️ Tabel dtsen_usulan tidak ditemukan.\n";
            return;
        }

        // =============================
        // 🔧 1️⃣ Tambah kolom audit trail
        // =============================
        $fields = [];

        // Tambahkan kolom jika belum ada
        if (!$this->db->fieldExists('updated_by', 'dtsen_usulan')) {
            $fields['updated_by'] = [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'updated_at'
            ];
        }

        if (!$this->db->fieldExists('verified_by', 'dtsen_usulan')) {
            $fields['verified_by'] = [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'verified_at'
            ];
        }

        if (!$this->db->fieldExists('applied_by', 'dtsen_usulan')) {
            $fields['applied_by'] = [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'applied_at'
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('dtsen_usulan', $fields);
        }

        // =============================
        // 🔗 2️⃣ Tambah Index dan Foreign Key
        // =============================
        $table = $this->db->table('dtsen_usulan');

        // Tambahkan index jika belum ada
        $indexes = ['idx_updated_by' => 'updated_by', 'idx_verified_by' => 'verified_by', 'idx_applied_by' => 'applied_by'];
        foreach ($indexes as $indexName => $column) {
            $exists = $this->db->query("
                SELECT COUNT(1) AS cnt
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'dtsen_usulan'
                  AND INDEX_NAME = '{$indexName}'
            ")->getRow()->cnt;

            if ($exists == 0) {
                $this->db->query("ALTER TABLE dtsen_usulan ADD INDEX {$indexName} ({$column})");
            }
        }

        // Tambahkan foreign key (dengan pengecekan)
        $foreignKeys = [
            'fk_usulan_updated_by'  => 'updated_by',
            'fk_usulan_verified_by' => 'verified_by',
            'fk_usulan_applied_by'  => 'applied_by'
        ];

        foreach ($foreignKeys as $fkName => $column) {
            $exists = $this->db->query("
                SELECT COUNT(1) AS cnt
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'dtsen_usulan'
                  AND CONSTRAINT_NAME = '{$fkName}'
            ")->getRow()->cnt;

            if ($exists == 0) {
                $this->db->query("
                    ALTER TABLE dtsen_usulan
                    ADD CONSTRAINT {$fkName}
                    FOREIGN KEY ({$column})
                    REFERENCES dtks_users(id)
                    ON UPDATE CASCADE
                    ON DELETE SET NULL
                ");
            }
        }

        echo "✅ Migrasi AddAuditTrailDtsenUsulan berhasil dijalankan.\n";
    }

    public function down()
    {
        // Hapus foreign key dan kolom bila rollback
        $foreignKeys = [
            'fk_usulan_updated_by',
            'fk_usulan_verified_by',
            'fk_usulan_applied_by'
        ];

        foreach ($foreignKeys as $fk) {
            $this->db->query("ALTER TABLE dtsen_usulan DROP FOREIGN KEY IF EXISTS {$fk}");
        }

        $this->forge->dropColumn('dtsen_usulan', ['updated_by', 'verified_by', 'applied_by']);

        echo "🧹 Migrasi AddAuditTrailDtsenUsulan berhasil di-rollback.\n";
    }
}
