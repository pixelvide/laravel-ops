<?php

namespace Pixelvide\Ops\Http\Controllers;

use Illuminate\Routing\Controller;

class DFPController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $this->addDevice();
        return view('ops::layout');
    }

    private function addDevice()
    {
        $visitorIp    = \Request::ip();
        $visitorUa    = \Request::header('user-agent');
        $visitorToken = \Request::cookie('_vidt');
        echo $visitorToken;
        echo $visitorIp;
        echo $visitorUa;
    }
}