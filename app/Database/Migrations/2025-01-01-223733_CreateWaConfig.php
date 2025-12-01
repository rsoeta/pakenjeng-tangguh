<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWaConfig extends Migration
{
    public function up()
    {
        // tabel konfigurasi WhatsApp per Admin Desa
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'auto_increment' => true],
            'user_id'           => ['type' => 'INT'],
            'api_key'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sender'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'template_groundcheck' => ['type' => 'TEXT', 'null' => true],
            'reminder_default_months' => ['type' => 'INT', 'default' => 3],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('dtsen_wa_config');

        // tabel reminder per KK
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'kk_id'     => ['type' => 'INT'],
            'admin_id'  => ['type' => 'INT'],
            'due_date'  => ['type' => 'DATETIME'],
            'status'    => ['type' => 'ENUM("pending","sent")', 'default' => 'pending'],
            'sent_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('dtsen_kk_reminder_log');
    }

    public function down()
    {
        $this->forge->dropTable('dtsen_wa_config');
        $this->forge->dropTable('dtsen_kk_reminder_log');
    }
}
