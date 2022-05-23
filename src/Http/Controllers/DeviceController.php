<?php
// TODO: Cookie syncing - https://www.internetkatta.com/share-cookies-or-local-storage-data-between-cross-domain
namespace Pixelvide\Ops\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Pixelvide\Ops\DFPGateway;
use Pixelvide\Ops\DFPRequest;

class DeviceController extends Controller
{
    /**
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            return $this->verifyDevice($request);
        }
        return $this->addDevice($request);
    }

    private function addDevice(Request $request)
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

    private function verifyDevice(Request $request)
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

    public function verifyRequest(Request $request, $userId)
    {
        try {
            $visitorToken     = $request->cookie('_vidt');
            $dfpCacheKey      = 'dfp:'.$userId.':'.$visitorToken;

            if (!(Cache::has($dfpCacheKey))) {
                $dfpRequest = new DFPRequest();
                $dfpRequest->setAction('VerifyRequest')
                    ->setAppId(env('DFP_GATEWAY_APP_ID'))
                    ->setVisitorIp($request->getClientIp())
                    ->setVisitorToken($request->cookie('_vidt'))
                    ->setVisitorUa($request->userAgent())
                    ->addExtraParams('userId', $userId);
                $dfpGw            = new DFPGateway();
                $verifyRequestRes = $dfpGw->send($dfpRequest);
                Cache::put($dfpCacheKey, $verifyRequestRes, now()->addMinutes(30));
            }

            $verifyRequestRes = Cache::get($dfpCacheKey);
        } catch (\Exception $exception) {
            report($exception);
            $verifyRequestRes['errorMessage'] = $exception->getMessage();
        }
        return $verifyRequestRes;
    }
}