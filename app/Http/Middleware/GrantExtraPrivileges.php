<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class GrantExtraPrivileges
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param null|mixed               $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // if (auth($guard)->check() && auth($guard)->id() === 132) {
        //     auth($guard)->user()->actAs(User::SUPPORT);
        // }

        return $next($request);
    }
}
