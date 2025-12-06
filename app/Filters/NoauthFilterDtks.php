<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class NoauthFilterDtks implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // if (session()->get('logDtks') && session()->get('status') == 1) {
        //     return redirect()->to(site_url('pages'));
        // }

        if (session()->get('logDtks') && session()->get('status') == 1) {

            $previousUrl = session()->get('redirectUrl');

            if ($previousUrl) {
                session()->remove('redirectUrl');
                return redirect()->to($previousUrl);
            }

            return redirect()->to(base_url('dashboard'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
