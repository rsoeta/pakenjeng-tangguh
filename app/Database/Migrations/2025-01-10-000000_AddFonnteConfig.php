<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFonnteConfig extends Migration
{
    public function up()
    {
        $fields = [
            'fonnte_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'sender'
            ],
            'fonnte_sender' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'fonnte_token'
            ],
            'fallback_enabled' => [
                'type' => 'TINYINT',
                'default' => 1,
                'after' => 'fonnte_sender'
            ]
        ];

        $this->forge->addColumn('dtsen_wa_config', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('dtsen_wa_config', [
            'fonnte_token',
            'fonnte_sender',
            'fallback_enabled'
        ]);
    }
}
