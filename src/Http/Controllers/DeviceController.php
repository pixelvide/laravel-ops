<?php

namespace Pixelvide\Ops\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Pixelvide\Ops\DFPGateway;
use Pixelvide\Ops\DFPRequest;

class DeviceController extends Controller
{
    /**
     * @return mixed
     */
    public function index(Request $request)
    {
        $addDeviceRes = [];
        try {
            $dfpRequest = new DFPRequest();
            $dfpRequest->setAction('AddDevice')
                ->setAppId(env('DFP_GATEWAY_APP_ID'))
                ->setVisitorIp($request->getClientIp())
                ->setVisitorToken($request->cookie('_vidt'))
                ->setVisitorUa($request->userAgent());
            $dfpGw        = new DFPGateway();
            $addDeviceRes = $dfpGw->send($dfpRequest);
        } catch (\Exception $exception) {
            report($exception);
            $addDeviceRes['errorMessage'] = $exception->getMessage();
        }
        return view('ops::device-layout', [
            'addDevice' => $addDeviceRes,
        ]);
    }

    public function verifyDevice(Request $request)
    {
        $verifyDeviceRes = [];
        try {
            $dfpRequest = new DFPRequest();
            $dfpRequest->setAction('VerifyDevice')
                ->setAppId(env('DFP_GATEWAY_APP_ID'))
                ->setVisitorIp($request->getClientIp())
                ->setVisitorToken($request->cookie('_vidt'))
                ->setVisitorUa($request->userAgent())
                ->addExtraParams('deviceToken', $request->input('deviceToken'))
                ->addExtraParams('deviceAuthToken', $request->input('authToken'));
            $dfpGw           = new DFPGateway();
            $verifyDeviceRes = $dfpGw->send($dfpRequest);
        } catch (\Exception $exception) {
            report($exception);
            $verifyDeviceRes['errorMessage'] = $exception->getMessage();
        }
        return view('ops::device-layout', [
            'verifyDevice' => $verifyDeviceRes,
        ]);
    }
}