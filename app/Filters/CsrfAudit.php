<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CsrfAudit implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Catat setiap POST request untuk audit keamanan
        if ($request->getMethod(true) === 'POST') {
            $uri = current_url();
            $ip  = $request->getIPAddress();
            $agent = $request->getUserAgent()->getBrowser();

            log_message('debug', "[CSRF-AUDIT] POST dari {$ip} via {$agent} ke {$uri}");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action required
    }
}
