<?php

namespace Kitamula\Kitchen\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class IpRestrictionMiddleware
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
        $toAtString = config('kitchen.ip_restriction_to_at');
        $dateFormatErrorMessage = 'IP制限の期限日が正しく入力されていません。YYYYMMDD,Y/M/D H:i:s形式やfalseを指定してください。';
        if(empty($toAtString)){
            // IP制限期限日が指定されていなければ認証せず通過
            dump('期限未設定時');

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
                dump('expired');

                return $next($request);
            }

        } catch (\Throwable $th) {
            // 日付以外が入力された場合
            dump($dateFormatErrorMessage);
            return $next($request);
        }

        // .envで設定したIPに、リクエスト元IPが含まれていなければリダイレクト
        $ips = explode(',', config('kitchen.ip_restriction_allow_ips'));
        if (in_array('*', $ips)) {
            dump('IPチェックの結果通過');
            return $next($request);
        }
        if (!in_array($request->ip(), $ips)) {
            return abort('403'); //リダイレクト先
        }
        return $next($request);
    }
}
