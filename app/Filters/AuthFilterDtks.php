<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilterDtks implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Jika belum login DTSEN
        if (!session()->get('logDtks')) {
            return redirect()->to(base_url('logout'));
        }

        // 2. Jika role_id tidak ditemukan (session rusak)
        if (!session()->get('role_id')) {
            return redirect()->to(base_url('logout'));
        }

        // 🚀 3. SATPAM SINGLE-DEVICE LOGIN & ANTI-PENYUSUP
        // Ambil data dari sesi browser yang sedang aktif
        $userId = session()->get('id');
        $sessionToken = session()->get('session_token');

        if ($userId && $sessionToken) {
            $db = \Config\Database::connect();

            // Cek ke database apakah token di browser ini masih terdaftar di tabel login
            $cekSesi = $db->table('dtks_users_login')
                ->where('dul_du_id', $userId)
                ->where('dul_token', $sessionToken)
                ->get()
                ->getRow();

            // Jika token tidak ditemukan! 
            // (Artinya: Jenderal baru saja login dari perangkat lain, ATAU Jenderal baru saja menekan tombol logout all / ganti password)
            if (!$cekSesi) {
                // Hancurkan session ilegal ini secara fisik
                session()->destroy();

                // Nyalakan session baru sesaat HANYA untuk membawa pesan error ke halaman login
                session()->start();
                session()->setFlashdata('message', [
                    'type' => 'error',
                    'text' => 'Sesi berakhir! Akun Anda telah login dari perangkat lain atau password baru saja diubah.'
                ]);

                // Tendang paksa ke halaman login
                return redirect()->to(base_url('login'));
            }

            // (Opsional) Jenderal bisa meng-uncomment baris di bawah ini jika ingin 
            // merekam rekam jejak aktivitas terakhir user di database (dul_last_activity) setiap kali mereka pindah halaman.
            // $db->table('dtks_users_login')
            //    ->where('dul_id', $cekSesi->dul_id)
            //    ->update(['dul_last_activity' => date('Y-m-d H:i:s')]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // optional
    }
}
