<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AttachCsrfHeader implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Tidak ada proses sebelum request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        try {
            $security = \Config\Services::security();

            // Pastikan CSRF service aktif
            if (method_exists($security, 'isCSRFEnabled') && $security->isCSRFEnabled()) {

                // Pastikan fungsi csrf_hash() tersedia
                if (function_exists('csrf_hash')) {
                    $token = csrf_hash();

                    if (!empty($token)) {
                        // Kirim token baru ke AJAX via header
                        $response->setHeader('X-CSRF-TOKEN', $token);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Log error tanpa mengganggu response
            log_message('error', '[AttachCsrfHeader] ' . $e->getMessage());
        }
    }
}
