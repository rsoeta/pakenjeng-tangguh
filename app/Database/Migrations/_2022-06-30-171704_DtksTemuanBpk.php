<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DtksTemuanBpk extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tkt_id'          => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'tkt_num'      => [
                'type'          => 'INT',
                'constraint'    => '5',
                'null'            => true
            ],
            'tkt_ket' => [
                'type'          => 'VARCHAR',
                'constraint'    => '255',
                'null'          => true,
            ]
        ]);
        $this->forge->addKey('tkt_id', true);

        $this->forge->createTable('tb_ket_temuan');
    }

    public function down()
    {
        //
        $this->forge->dropTable('tb_ket_temuan');
    }
}
