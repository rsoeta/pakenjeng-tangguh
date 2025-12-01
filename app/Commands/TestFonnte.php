<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestFonnte extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:fonnte';
    protected $description = 'Test kirim pesan ke Fonnte dengan metode yang benar';

    public function run(array $params)
    {
        helper('curl');

        $token = getenv('fonnte.token');

        $target  = '6285708098155';
        $message = 'Test Fonnte dari CI4';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.fonnte.com/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: $token"
            ],
            CURLOPT_POSTFIELDS => [
                'target'  => $target,
                'message' => $message
            ]
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) {
            CLI::error("Curl Error: $error");
            return;
        }

        CLI::write("Response Fonnte:", 'green');
        CLI::print($response);
    }
}
