<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Webhook extends Controller
{
    public function deploy()
    {
        // 🔐 1. Atur Password Rahasia
        $secret_token = "]rgeC.d(V2Y[tGAz";

        // Tangkap token dari URL (baik saat GET maupun POST)
        $token = $this->request->getGet('token');

        // Cek kecocokan token
        if ($token !== $secret_token) {
            return $this->response->setStatusCode(403)->setBody("Akses ditolak! Token tidak valid.");
        }

        // 🚀 2. Eksekusi Git Pull
        // ROOTPATH akan otomatis menunjuk ke folder utama Sinden (di atas folder public)
        $rootPath = ROOTPATH;

        // Jalankan perintah pindah ke folder Sinden, lalu eksekusi git pull
        $output = shell_exec("cd {$rootPath} && git reset --hard HEAD && git clean -fd && git pull origin main 2>&1");

        // 🖨️ 3. Tampilkan hasil log-nya
        $html = "<pre>🚀 Menjalankan Git Pull di Sinden...\n\n";
        $html .= htmlspecialchars($output ?? 'Tidak ada output dari server.');
        $html .= "</pre>";

        return $html;
    }
}
