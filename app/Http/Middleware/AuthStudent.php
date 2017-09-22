<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AuthStudent
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
        if (Auth::guest() || Auth::user()->is_admin != 0) {
            if ($request->ajax()) {
                return response('Unauthorized.', 403);
            } else {
                return redirect()->guest('/');
            }
        }
        return $next($request);
    }
}
