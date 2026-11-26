<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use Config\Services;

class MigrationTool extends Controller
{
    /**
     * 🔐 Proteksi ketat: Migration & Backup DB khusus Super Admin (1) & Admin (2) -> role_id < 3
     */
    private function checkAdminAccess(bool $isAjax = false)
    {
        $session = session();
        if (!$session->get('logDtks')) {
            return $isAjax
                ? $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Sesi login telah berakhir.'])
                : redirect()->to(base_url('logout'));
        }

        $roleId = (int) ($session->get('role_id') ?? 99);
        if ($roleId >= 3) {
            return $isAjax
                ? $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak. Fitur ini khusus Super Admin / Admin (role_id < 3).'])
                : redirect()->to(base_url('lockscreen'));
        }

        return null;
    }

    /**
     * Kumpulkan ringkasan status migrasi database (terpasang vs tertunda)
     */
    private function collectMigrationSummary($migrate): array
    {
        $allFound = $migrate->findMigrations();
        $history  = $migrate->getHistory('default');

        $appliedKeys = [];
        $latestVersion = '-';
        $latestBatch   = 0;

        foreach ($history as $h) {
            $key = ($h->version ?? '') . '|' . ($h->class ?? '');
            $appliedKeys[$key] = true;
            $latestVersion = (string) ($h->version ?? $latestVersion);
            $latestBatch   = max($latestBatch, (int) ($h->batch ?? 0));
        }

        $pending = [];
        foreach ($allFound as $m) {
            $key = ($m->version ?? '') . '|' . ($m->class ?? '');
            if (!isset($appliedKeys[$key])) {
                $shortClass = str_replace('App\\Database\\Migrations\\', '', (string) ($m->class ?? ''));
                $pending[]  = ($m->version ?? '') . '_' . $shortClass;
            }
        }

        return [
            'total_defined'  => count($allFound),
            'total_applied'  => count($history),
            'pending_count'  => count($pending),
            'pending'        => $pending,
            'is_up_to_date'  => count($pending) === 0,
            'latest_version' => $latestVersion,
            'latest_batch'   => $latestBatch,
        ];
    }

    /**
     * 🔍 Cek status migrasi saat ini (untuk panel info & validasi sebelum dijalankan)
     */
    public function status()
    {
        if ($resp = $this->checkAdminAccess(true)) {
            return $resp;
        }

        try {
            $migrate = Services::migrations();
            $summary = $this->collectMigrationSummary($migrate);

            return $this->response->setJSON(array_merge([
                'status' => 'success',
            ], $summary));
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memeriksa status migrasi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * 🚀 Jalankan migrasi database (`$migrate->latest()`) via web (cPanel-friendly)
     */
    public function index()
    {
        $wantsJson = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('Accept'), 'application/json');
        if ($resp = $this->checkAdminAccess($wantsJson)) {
            return $resp;
        }

        $migrate = Services::migrations();

        try {
            $beforeHistory = $migrate->getHistory('default');
            $beforeKeys    = [];
            foreach ($beforeHistory as $h) {
                $beforeKeys[($h->version ?? '') . '|' . ($h->class ?? '')] = true;
            }

            $migrate->latest();

            $afterHistory = $migrate->getHistory('default');
            $executed     = [];
            foreach ($afterHistory as $h) {
                $key = ($h->version ?? '') . '|' . ($h->class ?? '');
                if (!isset($beforeKeys[$key])) {
                    $shortClass = str_replace('App\\Database\\Migrations\\', '', (string) ($h->class ?? ''));
                    $executed[] = ($h->version ?? '') . '_' . $shortClass;
                }
            }

            $summary = $this->collectMigrationSummary($migrate);
            $upToDate = empty($executed);
            $msg = $upToDate
                ? 'Database sudah up-to-date. Tidak ada migrasi baru yang perlu dijalankan.'
                : 'Berhasil menjalankan ' . count($executed) . ' migrasi database baru.';

            if ($wantsJson) {
                return $this->response->setJSON(array_merge([
                    'status'     => 'success',
                    'up_to_date' => $upToDate,
                    'executed'   => $executed,
                    'message'    => $msg,
                    'csrfHash'   => csrf_hash(),
                ], $summary));
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            log_message('error', '❌ MigrationTool error: ' . $e->getMessage());

            if ($wantsJson) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status'   => 'error',
                    'message'  => 'Migrasi gagal: ' . $e->getMessage(),
                    'csrfHash' => csrf_hash(),
                ]);
            }

            return redirect()->back()
                ->with('error', 'Migrasi gagal: ' . $e->getMessage());
        }
    }

    public function downloadDb()
    {
        if ($resp = $this->checkAdminAccess(false)) {
            return $resp;
        }

        try {
            if (function_exists('set_time_limit')) {
                @set_time_limit(300);
            }

            $db     = \Config\Database::connect();
            $tables = $db->listTables();

            $fileName = 'backup_sinden_' . date('Ymd_His') . '.sql';
            $filePath = WRITEPATH . 'backups/' . $fileName;

            if (!is_dir(WRITEPATH . 'backups')) {
                mkdir(WRITEPATH . 'backups', 0777, true);
            }

            // Buka file untuk ditulis
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
                $limit   = 5000;

                while (true) {
                    $rows = $builder->get($limit, $offset)->getResultArray();
                    if (empty($rows)) {
                        break;
                    }

                    foreach ($rows as $item) {
                        $columns = array_map(fn($col) => "`$col`", array_keys($item));
                        $values  = array_map(fn($val) => $db->escape($val), array_values($item));

                        fwrite(
                            $fh,
                            "INSERT INTO `$table` (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ");\n"
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
                ->with('error', 'Backup gagal dijalankan. Error: ' . $e->getMessage());
        }
    }
}
