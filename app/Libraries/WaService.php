<?php

namespace App\Libraries;

use Config\Services;

class WaService
{
    protected $alatwaApiKey;
    protected $alatwaDevice;
    protected $alatwaSender;
    protected $fonnteToken;
    protected $timeout = 15;

    // tambahkan ini
    protected $httpTimeout = 15;

    public function __construct(array $config = [])
    {
        // Fonnte token
        // $this->fonnteToken = env("fonnte.token");
        $model = new \App\Models\Dtsen\WaConfigModel();

        $this->fonnteToken = $config['fonnte_token'] ?? getenv('fonnte.token');

        // kita ambil config id=1 (atau sesuai kebutuhan)
        $cfg = $model->find(1);

        $this->alatwaApiKey = $cfg['api_key'] ?? null;
        $this->alatwaDevice = $cfg['device'] ?? null;
        $this->alatwaSender = $cfg['sender'] ?? null;

        // FONNTE TOKEN DARI DATABASE
        $this->fonnteToken = $cfg['fonnte_token'] ?? null;

        // normalisasi
        $this->alatwaApiKey = trim($this->alatwaApiKey ?? '');
        $this->alatwaDevice = trim($this->alatwaDevice ?? '');
        $this->alatwaSender = trim($this->alatwaSender ?? '');
        $this->fonnteToken  = trim($this->fonnteToken ?? '');
    }

    // public function sendText(string $to, string $message): array
    // {
    //     $no = $this->normalizeNumber($to);

    //     // Try alatwa first
    //     if ($this->alatwaApiKey && $this->alatwaDevice && $this->alatwaSender) {
    //         $alatwa = $this->sendViaAlatwa($no, $message);
    //         if ($alatwa['status'] === true) {
    //             return ['status' => true, 'provider' => 'alatwa', 'raw' => $alatwa];
    //         }
    //     }

    //     // Fallback to Fonnte
    //     if ($this->fonnteToken) {
    //         $fonnte = $this->sendViaFonnte($no, $message);
    //         if ($fonnte['status'] === true) {
    //             return ['status' => true, 'provider' => 'fonnte', 'raw' => $fonnte];
    //         }
    //         return ['status' => false, 'provider' => 'fonnte', 'error' => $fonnte];
    //     }

    //     return ['status' => false, 'provider' => 'none', 'error' => 'No valid provider'];
    // }

    public function sendText(string $number, string $message): array
    {
        return $this->sendViaFonnte($number, $message);
    }

    protected function sendViaAlatwa(string $number, string $message): array
    {
        $url = 'https://api.alatwa.com/send/text';

        $payload = [
            'api_key' => $this->alatwaApiKey,
            'device'  => $this->alatwaDevice,
            'sender'  => $this->alatwaSender,
            'number'  => $number,
            'message' => $message
        ];

        try {
            $client = \Config\Services::curlrequest(['timeout' => $this->timeout]);
            $res = $client->post($url, [
                'json' => $payload,
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $body = json_decode($res->getBody(), true);

            if (($res->getStatusCode() === 200) && isset($body['status']) && $body['status'] === 'success') {
                return ['status' => true, 'response' => $body];
            }

            return ['status' => false, 'response' => $body];
        } catch (\Throwable $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendViaFonnte(string $number, string $message): array
    {
        $token = $this->fonnteToken;

        if (!$token) {
            return [
                'status' => false,
                'provider' => 'fonnte',
                'error' => 'Token Fonnte tidak ditemukan'
            ];
        }

        $url = "https://api.fonnte.com/send";

        $client = Services::curlrequest([
            'timeout' => 15,
        ]);

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => $token
                ],
                'form_params' => [
                    'target'  => $number,
                    'message' => $message
                ]
            ]);

            $json = json_decode($response->getBody(), true);

            if (isset($json['status']) && $json['status'] === true) {
                return [
                    'status' => true,
                    'provider' => 'fonnte',
                    'response' => $json
                ];
            }

            return [
                'status' => false,
                'provider' => 'fonnte',
                'error' => $json
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'provider' => 'fonnte',
                'exception' => $e->getMessage()
            ];
        }
    }

    protected function normalizeNumber(string $n): string
    {
        $n = trim($n);
        if (preg_match('/^0(\d+)$/', $n, $m)) return '62' . $m[1];
        if (strpos($n, '+') === 0) return substr($n, 1);
        return $n;
    }
}
