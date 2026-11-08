<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\GenModel;

class GlobalViewDataFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $genModel = new GenModel();
        $statusRole = $genModel->getStatusRole();

        // Cari nama role berdasarkan session role_id
        $roleId = session()->get('role_id');
        $roleName = 'Guest';
        if ($roleId) {
            foreach ($statusRole as $r) {
                if ((int)$r['id_role'] === (int)$roleId) {
                    $roleName = $r['nm_role'];
                    break;
                }
            }
        }

        // 🧱 Data global yang ingin disebarkan ke semua view
        $globals = [
            'statusRole' => $statusRole,
            'roleName'   => $roleName,
            'user_login' => session()->get(),
            'namaApp'    => 'SINDEN-DTSEN'
        ];

        // 🔧 Gunakan renderer service, bukan view()
        $renderer = service('renderer');
        foreach ($globals as $key => $val) {
            $renderer->setVar($key, $val);
        }

        // tidak mengubah request atau response
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak perlu apa-apa
    }
}
