<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeviceToWaConfig extends Migration
{
    public function up()
    {
        $this->forge->addColumn('dtsen_wa_config', [
            'device' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'api_key'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('dtsen_wa_config', 'device');
    }
}
