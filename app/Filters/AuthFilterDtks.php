<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilterDtks implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika belum login DTSEN
        if (!session()->get('logDtks')) {
            return redirect()->to(base_url('logout'));
        }

        // Jika role_id tidak ditemukan (session rusak)
        if (!session()->get('role_id')) {
            return redirect()->to(base_url('logout'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // optional
    }
}
