<?php
// TODO: Cookie syncing - https://www.internetkatta.com/share-cookies-or-local-storage-data-between-cross-domain
namespace Pixelvide\Ops\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
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

    public function verify(Request $request)
    {
        $verifyDeviceRes = [];
        try {
            $visitorToken = $_COOKIE['_vidt'] ?? '';
            $dfpRequest   = new DFPRequest();
            $dfpRequest->setAction('VerifyDeviceV2')
                ->setAppId(env('DFP_GATEWAY_APP_ID'))
                ->setVisitorIp($request->getClientIp())
                ->setVisitorToken($visitorToken)
                ->setVisitorUa($request->userAgent())
                ->addExtraParams('verifyDevice', [
                    "authToken" => $request->input('authToken'),
                ])
                ->addExtraParams('verifyRequest', [
                    "method"                  => $request->method(),
                    "content-type"            => $request->header("Content-Type"),
                    "host"                    => $request->getHost(),
                    "x-pixcorp-authorization" => $request->header("X-PixCorp-Authorization"),
                    "x-pixcorp-date"          => $request->header("X-PixCorp-Date"),
                    "x-pixcorp-expires"       => $request->header("X-PixCorp-Expires"),
                    "x-pixcorp-version"       => $request->header("X-PixCorp-Version"),
                    "requestUri"              => $request->getPathInfo(),
                ]);
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

    private function addDevice(Request $request)
    {
        $addDeviceRes = [];
        try {
            $visitorToken = $_COOKIE['_vidt'] ?? '';
            $dfpRequest   = new DFPRequest();
            $dfpRequest->setAction('AddDevice')
                ->setAppId(env('DFP_GATEWAY_APP_ID'))
                ->setVisitorIp($request->getClientIp())
                ->setVisitorToken($visitorToken)
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
            $visitorToken = $_COOKIE['_vidt'] ?? '';
            $dfpRequest   = new DFPRequest();
            $dfpRequest->setAction('VerifyDevice')
                ->setAppId(env('DFP_GATEWAY_APP_ID'))
                ->setVisitorIp($request->getClientIp())
                ->setVisitorToken($visitorToken)
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
        $verifyRequestRes = [
            "effect" => "deny",
        ];
        try {
            $visitorToken = $_COOKIE['_vidt'] ?? '';
            $dfpCacheKey  = 'dfp:'.$userId.':'.$visitorToken;
            if (!(Cache::has($dfpCacheKey))) {
                $dfpRequest = new DFPRequest();
                $dfpRequest->setAction('VerifyRequest')
                    ->setAppId(env('DFP_GATEWAY_APP_ID'))
                    ->setVisitorIp($request->getClientIp())
                    ->setVisitorToken($visitorToken)
                    ->setVisitorUa($request->userAgent())
                    ->addExtraParams('userId', strval($userId));
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

    public function verifyRequestV2(Request $request, $userId)
    {
        $verifyRequestRes = [
            "effect" => "deny",
        ];
        try {
            $visitorToken = $_COOKIE['_vidt'] ?? '';
            $dfpRequest   = new DFPRequest();
            $dfpRequest->setAction('VerifyRequestV2')
                ->setAppId(env('DFP_GATEWAY_APP_ID'))
                ->setVisitorIp($request->getClientIp())
                ->setVisitorToken($visitorToken)
                ->setVisitorUa($request->userAgent())
                ->addExtraParams('userId', strval($userId))
                ->addExtraParams('verifyRequest', [
                    "method"                  => $request->method(),
                    "content-type"            => $request->header("Content-Type"),
                    "host"                    => $request->getHost(),
                    "x-pixcorp-authorization" => $request->header("X-PixCorp-Authorization"),
                    "x-pixcorp-date"          => $request->header("X-PixCorp-Date"),
                    "x-pixcorp-expires"       => $request->header("X-PixCorp-Expires"),
                    "x-pixcorp-version"       => $request->header("X-PixCorp-Version"),
                    "requestUri"              => $request->getPathInfo(),
                ]);
            $dfpGw            = new DFPGateway();
            $verifyRequestRes = $dfpGw->send($dfpRequest);
        } catch (\Exception $exception) {
            report($exception);
            $verifyRequestRes['errorMessage'] = $exception->getMessage();
        }
        if (!array_key_exists("effect", $verifyRequestRes,)) {
            $verifyRequestRes["key"] = "deny";
        }
        return $verifyRequestRes;
    }
}