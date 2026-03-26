<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterStatusVerifikasiRollback extends Migration
{
    public function up()
    {
        $this->db->query("
            ALTER TABLE dtsen_penentuan_kemiskinan
            MODIFY status_verifikasi 
            ENUM('pending','approved','rejected','rollback') 
            DEFAULT 'pending'
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE dtsen_penentuan_kemiskinan
            MODIFY status_verifikasi 
            ENUM('pending','approved','rejected') 
            DEFAULT 'pending'
        ");
    }
}
