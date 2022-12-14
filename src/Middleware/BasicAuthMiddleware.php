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

        if(empty(config('kitchen.basicauth_to_at')) || Carbon::now()->gte(new Carbon(config('kitchen.basicauth_to_at')))){
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
