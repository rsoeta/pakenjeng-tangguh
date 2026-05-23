<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MenuFilterDtks implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Harus login
        if (!session()->get('logDtks')) {
            return redirect()->to(base_url('logout'));
        }

        $MenuModel = new \App\Models\Dtks\MenuModel();

        // Ambil segmen modul utama
        $seg1 = $request->getUri()->getSegment(1); // dtsen
        $seg2 = $request->getUri()->getSegment(2); // reminder-monitor
        $module = ($seg1 === 'dtsen') ? $seg2 : $seg1;

        // Ambil hanya modul utama dari tb_menu
        $menu = $MenuModel->where('tm_url', $module)->first();

        /**
         * 🔥 RULE PENTING:
         * Jika modul TIDAK ada di tb_menu → jangan blokir.
         * Karena itu biasanya AJAX endpoint dan bukan menu.
         */
        if (!$menu) {
            return; // biarkan lewat
        }

        // Jika menu dimatikan
        if ($menu['tm_status'] == 0) {
            return redirect()->to(base_url('lockscreen'));
        }

        /**
         * 🔥 Hak akses role:
         * role_id 1 (Superadmin Kabupaten) → BOLEH
         * role_id 2 (Admin Kecamatan) → BOLEH
         * role_id 3 (Admin Desa/Operator) → BOLEH
         * role_id 4 (Petugas RW/RT) → TIDAK BOLEH
         *
         * Artinya:
         * role_id 4 harus ditolak jika tm_grup_akses < 4
         */
        if (session()->get('role_id') > $menu['tm_grup_akses']) {
            return redirect()->to(base_url('lockscreen'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // optional
    }
}
