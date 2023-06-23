<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilterDtks implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here
        if (!session()->get('logDtks')) {
            // Setelah login berhasil, arahkan pengguna ke halaman sebelumnya
            return redirect()->to(base_url('login'));
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
        // if (session()->get('logDtks') == true) {
        //     //     return redirect()->to(base_url('pbb/user'));
        //     $previousPage = session()->get('previousPage');
        //     return redirect()->to($previousPage);
        // }
    }
}
