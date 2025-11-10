<?php

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * Generate nomor usulan otomatis
 * Format: USL-YYYYMM-XXXX (4 digit urutan per bulan)
 */
if (!function_exists('generateUsulanNo')) {
    function generateUsulanNo(): string
    {
        $db = \Config\Database::connect();
        $prefix = 'USL-' . date('Ym') . '-';
        $bulanSekarang = date('Y-m');

        try {
            $builder = $db->table('dtsen_usulan');
            $builder->like('usulan_no', $prefix, 'after');
            $builder->selectMax('usulan_no');
            $row = $builder->get()->getRow();

            $lastNumber = 0;
            if ($row && $row->usulan_no) {
                $lastNumber = (int)substr($row->usulan_no, -4);
            }

            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            return $prefix . $newNumber;
        } catch (DatabaseException $e) {
            return $prefix . '0001';
        }
    }
}

/**
 * Pastikan folder usulan tersedia
 * Akan membuat otomatis jika belum ada
 */
if (!function_exists('ensureUsulanFolders')) {
    function ensureUsulanFolders(): array
    {
        $basePath = FCPATH . 'data/usulan/';
        $folders = [
            'foto_rumah',
            'foto_rumah_dalam',
            'foto_kk',
            'foto_lain'
        ];

        foreach ($folders as $folder) {
            $path = $basePath . $folder . '/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }

        return [
            'status'  => true,
            'message' => 'Folder usulan siap digunakan',
            'path'    => $basePath
        ];
    }
}
