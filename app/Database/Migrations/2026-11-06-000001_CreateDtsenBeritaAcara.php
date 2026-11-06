<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDtsenBeritaAcara extends Migration
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
            'nomor_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'bulan_data' => [
                'type'       => 'CHAR',
                'constraint' => 2,
            ],
            'tahun_data' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'kode_desa' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'default'    => '',
            ],
            'tanggal_musyawarah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tanggal_ttd' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(
            ['kode_desa', 'bulan_data', 'tahun_data', 'nomor_surat'],
            'uq_dtsen_ba_identity'
        );
        $this->forge->createTable('dtsen_berita_acara', true);

        $this->forge->addColumn('dtsen_kk_reminder_log', [
            'ba_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('ba_id', false, false, 'idx_reminder_ba_id');
        $this->forge->processIndexes('dtsen_kk_reminder_log');
    }

    public function down()
    {
        $this->forge->dropKey('dtsen_kk_reminder_log', 'idx_reminder_ba_id', false);
        $this->forge->dropColumn('dtsen_kk_reminder_log', 'ba_id');
        $this->forge->dropTable('dtsen_berita_acara', true);
    }
}
