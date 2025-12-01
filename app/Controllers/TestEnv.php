<?php

namespace App\Controllers;

class TestEnv extends BaseController
{
    public function index()
    {
        dd(getenv('fonnte.token'));
    }

    public function testWa()
    {
        // Controller quick test
        $svc = new \App\Libraries\WaService();
        $res = $svc->sendText('085708098155', 'Test fallback WA dari SINDEN');
        dd($res);
    }
}
