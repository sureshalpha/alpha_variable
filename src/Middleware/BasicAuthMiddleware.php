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

        // configに指定された日付の型チェック
        $toAtString = config('kitchen.basicauth_to_at');
        $dateFormatErrorMessage = 'Basic認証の期限日が正しく入力されていません。YYYYMMDD,Y/M/D H:i:s形式やfalseを指定してください。';
        if(empty($toAtString)){
            // Basic認証期限日が指定されていなければ認証せず通過
            return $next($request);
        }

        try {
            $toAt = new Carbon($toAtString);
            // dump($toAtString);
            if ( preg_match('/^[0-9]{8}$/', $toAtString) || preg_match('/^[0-9]{4}[-\/][0-9]{2}[-\/][0-9]{2}$/', $toAtString)){
                // 日付のみ YYYYMMDD, Y-M-D
                // 日付のみの場合、23:59:59までを範囲にする
                $toAt->setHour(23)->setMinute(59)->setSecond(59);
            }elseif(preg_match('/^[0-9]{14}$/', $toAtString) || preg_match('/^[0-9]{4}[-\/][0-9]{2}[-\/][0-9]{2}\s[0-9]{2}[:-][0-9]{2}[:-][0-9]{2}$/', $toAtString)){
                // 日付＋時分秒 YYYYMMDDHHIISS, Y-M-D H:i:s
            }else{
                dump($dateFormatErrorMessage);
            }

            // 期限日より先であれば認証せず通過
            if(Carbon::now()->gte($toAt)){
                return $next($request);
            }

        } catch (\Throwable $th) {
            // 日付以外が入力された場合
            dump($dateFormatErrorMessage);
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
