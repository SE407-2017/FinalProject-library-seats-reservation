<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AuthAdmin
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
        if (!Auth::check() || Auth::user()->is_admin != 1) {
            if ($request->ajax()) {
                return response('Unauthorized.', 403);
            } else {
                return redirect()->guest('/forbidden');
            }
        }
        return $next($request);
    }
}
