<?php

use CodeIgniter\Files\File;

if (!function_exists('ensureUsulanFolders')) {
    /**
     * Memastikan semua folder penyimpanan usulan tersedia.
     * Akan membuat folder otomatis jika belum ada.
     *
     * @return array Daftar path folder yang telah dipastikan ada
     */
    function ensureUsulanFolders(): array
    {
        // Gunakan path absolut berbasis FCPATH agar bisa diakses via browser
        $basePath = FCPATH . 'data/usulan/';

        // Daftar subfolder wajib
        $folders = [
            $basePath . 'foto_rumah/',
            $basePath . 'foto_rumah_dalam/',
            $basePath . 'foto_identitas/',
        ];

        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                try {
                    mkdir($folder, 0777, true);
                } catch (\Throwable $e) {
                    log_message('error', "Gagal membuat folder: $folder. Pesan: " . $e->getMessage());
                }
            }
        }

        return [
            'base' => $basePath,
            'foto_rumah' => $folders[0],
            'foto_rumah_dalam' => $folders[1],
            'foto_identitas' => $folders[2],
        ];
    }
}
