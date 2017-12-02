<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AuthWechat
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
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 403);
            } else {
                return redirect()->guest('auth/wechat/login/' . $request->seat_id);
            }
        }
        return $next($request);
    }
}
