<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role_id');

        /**
         * RULE:
         * Role 1 (superadmin) → bebas
         * Role 2 (admin) → bebas
         * Role 3 (operator) → TIDAK BOLEH
         * Role >3 (viewer/petugas entri) → TIDAK BOLEH
         */
        if ($role > 3) {
            return redirect()->to(base_url('lockscreen'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // optional
    }
}
