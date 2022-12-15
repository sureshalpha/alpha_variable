<?php

namespace Kitamula\Kitchen\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class IpRestriction
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        // .envで設定したIPに、リクエスト元IPが含まれていなければリダイレクト
        $ips = explode(',', config('kitchen.allow_ips'));
        if (in_array('*', $ips)) {
            return $next($request);
        }
        if (!in_array($request->ip(), $ips)) {
            return abort('403'); //リダイレクト先
        }
        return $next($request);
    }
}
