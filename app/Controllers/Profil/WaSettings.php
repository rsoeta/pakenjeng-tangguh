<?php

namespace App\Controllers\Profil;

use App\Controllers\BaseController;
use App\Models\Dtsen\WaConfigModel;
use App\Models\Dtks\AuthModel;

class WaSettings extends BaseController
{
    protected $WaConfigModel;
    protected $userId;
    protected $authModel;

    public function __construct()
    {
        $this->WaConfigModel = new WaConfigModel();
        $this->authModel = new AuthModel();
        $this->userId  = session()->get('id');
    }

    /** ============================================================
     *  SIMPAN API KEY + DEVICE ID
     * ============================================================ */
    public function saveApi()
    {
        $save = [
            'user_id'     => $this->userId,
            'api_key'     => $this->request->getPost('api_key'),
            'device'      => $this->request->getPost('device'),
            'sender'      => $this->request->getPost('sender'),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $exists = $this->WaConfigModel->getConfig($this->userId);

        if ($exists)
            $this->WaConfigModel->update($exists['id'], $save);
        else
            $this->WaConfigModel->insert($save);

        return $this->response->setJSON(['status' => 'success']);
    }

    /** ============================================================
     *  TEST KIRIM API
     * ============================================================ */
    public function testApi()
    {
        $setting = $this->WaConfigModel->getConfig($this->userId);

        $wa = new \App\Libraries\WaService([
            'alatwa_api_key' => $setting['api_key'],
            'alatwa_device'  => $setting['device'],
            'alatwa_sender'  => $setting['sender'],

            // fallback
            'fonnte_token' => getenv('fonnte.token')  // kita simpan di .env
        ]);

        $number  = session()->get('nope');
        $message = "Test WA dari SINDEN.\nPengaturan WhatsApp siap digunakan.";

        $send = $wa->sendText($number, $message);

        return $this->response->setJSON($send);
    }

    /** ============================================================
     *  NORMALISASI NOMOR HP
     * ============================================================ */
    private function normalizeWaNumber($no)
    {
        $no = trim($no);

        // Jika mulai 08 → ubah ke 628
        if (strpos($no, '08') === 0) {
            return '62' . substr($no, 1);
        }

        // Jika mulai 8xxxxx → tambahkan 62
        if (strpos($no, '8') === 0) {
            return '62' . $no;
        }

        return $no;
    }

    /** ============================================================
     *  FUNGSI UNIVERSAL KIRIM WA (FINAL)
     * ============================================================ */
    private function sendWa($api_key, $device, $number, $message, $sender)
    {
        $url = "https://api.alatwa.com/send/text";

        $payload = [
            'api_key' => $api_key,
            'device'  => $device,
            'sender'  => $sender,   // wajib
            'number'  => $number,   // wajib
            'message' => $message
        ];

        $json = json_encode($payload);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_TIMEOUT        => 20
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            return [
                'status'  => false,
                'message' => "Curl Error: $err",
            ];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status'     => $httpCode == 200,
            'http_code'  => $httpCode,
            'response'   => json_decode($response, true),
            'raw'        => $response
        ];
    }

    /** ============================================================
     *  SIMPAN KONFIGURASI FONNTE
     * ============================================================ */
    public function saveFonnte()
    {
        $save = [
            'user_id'         => $this->userId,
            'fonnte_token'    => $this->request->getPost('fonnte_token'),
            'fonnte_sender'   => $this->request->getPost('fonnte_sender'),
            'fallback_enabled' => $this->request->getPost('fallback_enabled') ? 1 : 0,
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        $exists = $this->WaConfigModel->getConfig($this->userId);

        if ($exists)
            $this->WaConfigModel->update($exists['id'], $save);
        else
            $this->WaConfigModel->insert($save);

        return $this->response->setJSON(['status' => 'success']);
    }

    /** ============================================================
     *  TEST KIRIM FONNTE
     * ============================================================ */
    public function testFonnte()
    {
        $setting = $this->WaConfigModel->getConfig($this->userId);

        if (!$setting || !$setting['fonnte_token']) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Token Fonnte belum disimpan.'
            ]);
        }

        $number = session()->get('nope'); // nomor WA admin desa
        if (!$number) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Nomor WA Admin tidak ditemukan.'
            ]);
        }

        $message = "Test Fonnte OK — Pengaturan Fallback berhasil.";

        // --- kirim via Fonnte ---
        $client = \Config\Services::curlrequest();
        try {
            $send = $client->post('https://api.fonnte.com/send', [
                'headers' => [
                    'Authorization' => $setting['fonnte_token']
                ],
                'form_params' => [
                    'target' => $number,
                    'message' => $message,
                    'countryCode' => '62'
                ]
            ]);

            $body = json_decode($send->getBody(), true);

            return $this->response->setJSON([
                'status'   => true,
                'response' => $body
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /** ============================================================
     *  SIMPAN TEMPLATE PESAN GROUNDCHECK
     * ============================================================ */
    public function saveTemplate()
    {
        $template = $this->request->getPost('template');

        if (!$template) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Template tidak boleh kosong.'
            ]);
        }

        // Ambil config WA admin desa
        $exists = $this->WaConfigModel->getConfig($this->userId);

        // Data yang akan disimpan
        $save = [
            'user_id'             => $this->userId,
            'template_groundcheck' => $template,
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        if ($exists) {
            $this->WaConfigModel->update($exists['id'], $save);
        } else {
            $this->WaConfigModel->insert($save);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Template berhasil disimpan!'
        ]);
    }
}
