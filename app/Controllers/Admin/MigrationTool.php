<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use Config\Services;

class MigrationTool extends Controller
{
    public function index()
    {
        $migrate = Services::migrations();

        try {
            $migrate->latest();

            return redirect()->back()
                ->with('success', 'Migrasi berhasil dijalankan!');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Migrasi gagal: ' . $e->getMessage());
        }
    }

    public function downloadDb()
    {
        try {
            $db = \Config\Database::connect();
            $tables = $db->listTables();

            $fileName = "backup_sinden_" . date('Ymd_His') . ".sql";
            $filePath = WRITEPATH . "backups/" . $fileName;

            if (!is_dir(WRITEPATH . "backups")) {
                mkdir(WRITEPATH . "backups", 0777, true);
            }

            // buka file untuk ditulis
            $fh = fopen($filePath, 'w');

            fwrite($fh, "-- Backup SINDEN\n");
            fwrite($fh, "-- Created at: " . date('Y-m-d H:i:s') . "\n\n");

            foreach ($tables as $table) {

                // Struktur tabel
                $query = $db->query("SHOW CREATE TABLE `$table`");
                $row   = $query->getRowArray();

                // Ambil kolom kedua (CREATE TABLE ...)
                $createSql = array_values($row)[1] ?? null;
                if (!$createSql) {
                    throw new \Exception("Gagal membaca struktur tabel: $table");
                }

                fwrite($fh, "-- Struktur untuk tabel `$table`\n");
                fwrite($fh, "DROP TABLE IF EXISTS `$table`;\n");
                fwrite($fh, $createSql . ";\n\n");


                // Data tabel, dibaca per-batch (ANTI MEMORY LIMIT)
                fwrite($fh, "-- Data untuk tabel `$table`\n");

                $builder = $db->table($table);
                $offset  = 0;
                $limit   = 5000; // baca 5000 baris per batch

                while (true) {
                    $rows = $builder->get($limit, $offset)->getResultArray();
                    if (empty($rows)) break;

                    foreach ($rows as $item) {
                        $columns = array_map(fn($col) => "`$col`", array_keys($item));
                        $values  = array_map(fn($val) => $db->escape($val), array_values($item));

                        fwrite(
                            $fh,
                            "INSERT INTO `$table` (" . implode(",", $columns) . ") VALUES (" . implode(",", $values) . ");\n"
                        );
                    }

                    $offset += $limit;
                }

                fwrite($fh, "\n");
            }

            fclose($fh);

            // Download file
            return $this->response->download($filePath, null)
                ->setFileName($fileName);
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', "Backup gagal dijalankan. Error: " . $e->getMessage());
        }
    }
}
