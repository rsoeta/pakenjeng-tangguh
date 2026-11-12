<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedAtToDtsenKkReminderLog extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('created_at', 'dtsen_kk_reminder_log')) {
            $this->forge->addColumn('dtsen_kk_reminder_log', [
                'created_at' => [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'ba_id',
                ],
            ]);
        }

        // Isi data yang sudah ada dengan tanggal 3 bulan ke belakang dari due_date
        $this->db->query("
            UPDATE dtsen_kk_reminder_log
            SET created_at = DATE_SUB(due_date, INTERVAL 3 MONTH)
            WHERE created_at IS NULL
        ");

        // Ubah menjadi NOT NULL agar wajib diisi ke depannya
        $this->forge->modifyColumn('dtsen_kk_reminder_log', [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('created_at', 'dtsen_kk_reminder_log')) {
            $this->forge->dropColumn('dtsen_kk_reminder_log', 'created_at');
        }
    }
}
