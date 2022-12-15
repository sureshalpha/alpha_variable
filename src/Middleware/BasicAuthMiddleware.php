<?php

namespace Kitamula\Kitchen\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class BasicAuthMiddleware
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle(Request $request, Closure $next)
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        if(empty(config('kitchen.basicauth_to_at'))){
            // Basic認証期限日が指定されていなければ認証せず通過
            return $next($request);
        }

        try {
            $toAt = new Carbon(config('kitchen.basicauth_to_at'));

            // 期限日より先であれば認証せず通過
            if(Carbon::now()->gte($toAt)){
                return $next($request);
            }

        } catch (\Throwable $th) {
            // 日付以外が入力された場合
            dump('Basic認証の期限日が正しく入力されていません。YYYYMMDD形式やfalseを指定してください。');
            return $next($request);
        }

        if ($username == config('kitchen.basicauth_user') && $password == config('kitchen.basicauth_password')) {
            return $next($request);
        }

        abort(401, "Enter username and password.", [
            header('WWW-Authenticate: Basic realm="Sample Private Page"'),
            header('Content-Type: text/plain; charset=utf-8')
        ]);
    }
}
