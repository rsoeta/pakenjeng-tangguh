<?php

namespace App\Controllers\Cron;

use App\Controllers\BaseController;
use App\Libraries\WaService;
use CodeIgniter\CLI\CLI;

class WaReminder extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        CLI::write("Starting WA Reminder (debug mode)...", 'yellow');

        $dueList = $db->table('dtsen_kk_reminder_log r')
            ->select('r.*, kk.no_kk, kk.kepala_keluarga, u.fullname, u.nope,
                      cfg.api_key, cfg.device, cfg.sender, cfg.template_groundcheck, cfg.reminder_default_months')
            ->join('dtsen_kk kk', 'kk.id_kk = r.kk_id')
            ->join('dtks_users u', 'u.id = r.admin_id')
            ->join('dtsen_wa_config cfg', 'cfg.user_id = r.admin_id')
            ->where('r.status', 'pending')
            ->where('r.due_date <=', date('Y-m-d H:i:s'))
            ->orderBy('r.due_date', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($dueList)) {
            CLI::write("No due reminders found.", 'yellow');
            return CLI::write("WA-REMINDER: NO-DUE", 'green');
        }

        CLI::write("Found " . count($dueList) . " reminder(s) to process.", 'green');

        foreach ($dueList as $row) {
            CLI::write("--------------------------------------------------", 'cyan');
            CLI::write("Processing reminder id={$row['id']} kk_id={$row['kk_id']} admin_id={$row['admin_id']}", 'blue');
            CLI::write("Admin phone (nope): {$row['nope']}", 'blue');
            CLI::write("Config - api_key: " . ($row['api_key'] ? 'present' : 'missing') . " | device: " . ($row['device'] ?? 'null') . " | sender: " . ($row['sender'] ?? 'null'), 'blue');
            CLI::write("Template raw:\n" . ($row['template_groundcheck'] ?? '[empty]'), 'blue');

            // render template
            $msg = $this->renderTemplate($row['template_groundcheck'] ?? '', [
                'no_kk'   => $row['no_kk'],
                'nama_kk' => $row['kepala_keluarga'] ?? 'TIDAK DIKENAL',
                // capitalize admin per-kata
                'admin'   => isset($row['fullname']) ? $this->capitalizeName($row['fullname']) : '',
                // helper replacement for nameApp()
                'nameApp()' => function_exists('nameApp') ? nameApp() : 'APP'
            ]);

            CLI::write("Message after render:\n" . $msg, 'light_cyan');

            // instantiate WaService
            $wa = new WaService([
                'alatwa_api_key' => $row['api_key'] ?? null,
                'alatwa_device'  => $row['device'] ?? null,
                'alatwa_sender'  => $row['sender'] ?? null,
                'fonnte_token'   => getenv('fonnte.token') ?: ($_ENV['fonnte.token'] ?? null)
            ]);

            // show chosen provider preference (WaService internal may auto-fallback)
            CLI::write("Calling WaService->sendText()", 'yellow');

            $send = $wa->sendText($row['nope'], $msg);

            // dump full response
            CLI::write("Provider response: " . json_encode($send, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), 'white');

            // log to file as well
            log_message('info', "[WA-REMINDER][debug] reminder_id={$row['id']} payload_sent=" . json_encode([
                'to' => $row['nope'],
                'message' => $msg,
                'api_key' => $row['api_key'] ? 'present' : 'missing',
                'device' => $row['device'] ?? null,
                'sender' => $row['sender'] ?? null,
            ]));

            if (isset($send['status']) && $send['status'] === true) {
                $db->table('dtsen_kk_reminder_log')
                    ->where('id', $row['id'])
                    ->update([
                        'status'  => 'sent',
                        'sent_at' => date('Y-m-d H:i:s')
                    ]);
                CLI::write("[OK] reminder id={$row['id']} marked SENT (provider: {$send['provider']})", 'green');
                log_message('info', "[WA-REMINDER] SENT via {$send['provider']} → KK {$row['no_kk']}");
            } else {
                CLI::write("[FAILED] reminder id={$row['id']} NOT sent", 'red');
                log_message('error', "[WA-REMINDER] FAILED → KK {$row['no_kk']} | " . json_encode($send));
            }
        }

        CLI::write("WA-REMINDER: DONE", 'green');
    }

    private function renderTemplate($template, $vars = [])
    {
        // replace simple variables
        foreach ($vars as $k => $v) {
            if (is_callable($v)) {
                $val = $v();
            } else {
                $val = $v;
            }
            $template = str_replace('{{' . $k . '}}', $val, $template);
            $template = str_replace('{{ ' . $k . ' }}', $val, $template);
        }

        // replace helper() calls like {{nameApp()}}
        if (preg_match_all('/\{\{\s*([a-zA-Z_]\w*)\s*\(\s*\)\s*\}\}/', $template, $matches)) {
            foreach ($matches[1] as $func) {
                if (function_exists($func)) {
                    $template = preg_replace('/\{\{\s*' . $func . '\s*\(\s*\)\s*\}\}/', $func(), $template);
                }
            }
        }

        return $template;
    }

    private function capitalizeName($raw)
    {
        $parts = preg_split('/\s+/', trim($raw));
        $parts = array_map(function ($p) {
            return mb_convert_case(mb_strtolower($p), MB_CASE_TITLE, "UTF-8");
        }, $parts);
        return implode(' ', $parts);
    }
}
