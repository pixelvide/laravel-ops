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
        $addDeviceRes = $this->addDevice();
        return view('ops::device-layout', [
            'addDevice' => $addDeviceRes,
        ]);
    }

    private function addDevice()
    {
        try {
            $dfpRequest = new DFPRequest();
            $dfpRequest->setAction('AddDevice')
                ->setAppId(env('DFP_GATEWAY_APP_ID'))
                ->setVisitorIp(\Request::ip())
                ->setVisitorToken(\Request::cookie('_vidt'))
                ->setVisitorUa(\Request::header('user-agent'));
            $dfpGw = new DFPGateway();
            return $dfpGw->send($dfpRequest);
        } catch (\Exception $exception) {
            report($exception);
        }
        return [];
    }

    public function verifyDevice()
    {
        try {
            $dfpRequest = new DFPRequest();
            $dfpRequest->setAction('VerifyDevice')
                ->setAppId(env('DFP_GATEWAY_APP_ID'))
                ->setVisitorIp(\Request::ip())
                ->setVisitorToken(\Request::cookie('_vidt'))
                ->setVisitorUa(\Request::header('user-agent'))
                ->addExtraParams('deviceToken', \Request::input('deviceToken'))
                ->addExtraParams('deviceAuthToken', \Request::input('authToken'));
            $dfpGw = new DFPGateway();
            return $dfpGw->send($dfpRequest);
        } catch (\Exception $exception) {
            print_r($exception);
        }
        return [];
    }
}