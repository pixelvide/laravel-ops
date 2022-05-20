<?php

namespace Pixelvide\Ops\Http\Controllers;

use Illuminate\Routing\Controller;
use Pixelvide\Ops\DFPGateway;
use Pixelvide\Ops\DFPRequest;

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
        try {
            $visitorIp    = \Request::ip();
            $visitorUa    = \Request::header('user-agent');
            $visitorToken = \Request::cookie('_vidt');
            $dfpRequest   = new DFPRequest();
            $dfpRequest->setAction('AddDevice');
            $dfpRequest->setAppId(env('DFP_GATEWAY_APP_ID'));
            $dfpRequest->setVisitorIp($visitorIp)
                ->setVisitorToken($visitorToken)
                ->setVisitorUa($visitorUa);
            $dfpGw  = new DFPGateway();
            $dfpRes = $dfpGw->send($dfpRequest);
            print_r($dfpRes);
        } catch (\Exception $exception) {
            print_r($exception);
        }
    }
}