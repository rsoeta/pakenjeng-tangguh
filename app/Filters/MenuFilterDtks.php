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
        $roleId = session()->get('role_id');

        // Ambil segmen URI
        $uri = $request->getUri();
        $seg1 = $uri->getSegment(1);
        $seg2 = $uri->getTotalSegments() > 1 ? $uri->getSegment(2) : '';

        // 1️⃣ Coba cari Exact Match (contoh: pembaruan-keluarga/pemulihan)
        $fullPath = trim($seg1 . '/' . $seg2, '/');
        $menu = $MenuModel->where('tm_url', $fullPath)->first();

        // 2️⃣ Jika tidak ada exact match, cari berdasarkan Segmen 1 (atau Segmen 2 jika dtsen)
        if (!$menu) {
            $module = ($seg1 === 'dtsen') ? $seg2 : $seg1;
            $menu = $MenuModel->where('tm_url', $module)->first();
        }

        // 3️⃣ Jika MASIH TIDAK ADA di tb_menu (Sapu Jagat / Wildcard Security)
        if (!$menu) {
            // 🚨 Daftar prefix URL operasional yang HARAM diakses oleh Role > 4 (Auditor)
            // Walaupun sub-URL nya tidak terdaftar di tb_menu (seperti /draft, /detail, /tambah)
            $restrictedPrefixes = [
                'pembaruan-keluarga',
                'master-kks',
                'bansos-kks',
                'usulan-bansos',
                'dokumentasi',
                'dtsen-se'
            ];

            if (in_array($seg1, $restrictedPrefixes) && $roleId > 4) {
                return redirect()->to(base_url('lockscreen')); // 🛑 Tendang ke halaman Lockscreen
            }

            // Jika bukan area terlarang, biarkan lewat (mungkin murni fungsi AJAX endpoint public)
            return;
        }

        // Jika menu dimatikan dari database
        if ($menu['tm_status'] == 0) {
            return redirect()->to(base_url('lockscreen'));
        }

        // 4️⃣ Pengecekan hak akses reguler dari tb_menu
        if ($roleId > $menu['tm_grup_akses']) {
            return redirect()->to(base_url('lockscreen'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // optional
    }
}
