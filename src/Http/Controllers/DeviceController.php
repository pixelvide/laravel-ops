<?php

namespace Pixelvide\Ops\Http\Controllers;

use Illuminate\Routing\Controller;

class DeviceController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $this->addDevice();
        return view('ops::device-layout');
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