<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMenuLaporanPenerangan extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Hindari duplikasi jika sudah pernah dijalankan
        $exists = $db->table('tb_menu')
            ->where('tm_url', 'dtsen/laporan-penerangan')
            ->countAllResults();

        if ($exists > 0) {
            return;
        }

        // Cari menu induk "Monitoring" di bawah modul DTSEN
        $monitoring = $db->table('tb_menu')
            ->where('tm_nama', 'Monitoring')
            ->where('tm_parent_id', 3)
            ->get()
            ->getRowArray();

        $parentId = $monitoring ? (int) $monitoring['tm_id'] : 36;

        $db->table('tb_menu')->insert([
            'tm_nama'         => 'Laporan Penerangan',
            'tm_class'        => null,
            'tm_url'          => 'dtsen/laporan-penerangan',
            'tm_icon'         => 'fas fa-bolt',
            'tm_parent_id'    => $parentId,
            'tm_status'       => 1,
            'tm_grup_akses'   => 3, // Khusus role_id < 4 (Super Admin, Admin, Operator)
            'tm_urutan'       => 4,
            'tm_is_dashboard' => 0,
        ]);
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('tb_menu')
            ->where('tm_url', 'dtsen/laporan-penerangan')
            ->delete();
    }
}
