<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\Cron\WaReminder;

class WaReminderCommand extends BaseCommand
{
    protected $group       = 'WA';
    protected $name        = 'cron:wa-reminder';
    protected $description = 'Menjalankan cron WA Reminder (alatwa/fonnte).';

    public function run(array $params)
    {
        CLI::write("Menjalankan WA Reminder...", 'yellow');

        $controller = new WaReminder();

        // Controller WaReminder::index() sekarang CLI-safe dan TIDAK mengembalikan Response
        $controller->index();

        CLI::write("WA Reminder executed.", 'green');
    }
}
