<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MenuFilterDtks implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $MenuModel = new \App\Models\Dtks\MenuModel();
        // if logDtks is not set in session and uri from tb_menu column tm_status is 0 then redirect to login page
        if (session()->get('logDtks')) {

            $uri = $request->getUri()->getSegment(1);
            if ($uri == 'index.php') {
                $uri = $request->getUri()->getSegment(2);
            } else {
                $uri = $request->getUri()->getSegment(1);
            }
            // dd($uri);
            $menu = $MenuModel->getMenu($uri);

            foreach ($menu as $key => $value) {
                // dd($value);
                if ($value['tm_status'] == 0 || $value['tm_grup_akses'] < session()->get('role_id')) {
                    return redirect()->to(base_url('lockscreen'));
                }
            }
        } elseif (!session()->get('logDtks')) {
            return redirect()->to(base_url('logout'));
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
