<?php

namespace Pixelvide\Ops\Http\Middleware;

class DFPAuthorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if (app()->environment(['local'])) {
            $visitorToken = $request->cookie('_vidt');
            $dfp          = $request->cookie('dfp');
            $clientIp     = $request->ip();
            $userAgent    = $request->server('HTTP_USER_AGENT');
            echo $visitorToken;
            echo $dfp;
            echo $clientIp;
            echo $userAgent;
        }
        return $next($request);
    }
}