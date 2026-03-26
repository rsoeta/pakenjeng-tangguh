<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedByToPenentuanKemiskinan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('dtsen_penentuan_kemiskinan', [
            'updated_by' => [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'created_by'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('dtsen_penentuan_kemiskinan', 'updated_by');
    }
}
