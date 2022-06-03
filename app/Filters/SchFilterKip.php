<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SchFilterKip implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here
        if (!session()->get('role_id') >= '2' || !session()->get('role_id') == '5') {
            return redirect()->to(base_url('lockscreen'));
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
        // if (session()->get('log') == true) {
        //     return redirect()->to(base_url('pbb/user'));
        // }
    }
}
