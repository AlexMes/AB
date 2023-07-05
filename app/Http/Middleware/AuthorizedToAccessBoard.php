<?php

namespace App\Http\Middleware;

use Closure;

class AuthorizedToAccessBoard
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth('web')->check() && !auth('web')->user()->allowedToSeeBoard()) {
            auth('web')->logout();

            return abort(403, 'You are not allowed to access this application.');
        }

        if (auth('api')->check() && !auth('api')->user()->allowedToSeeBoard()) {
            return abort(401, 'You are not allowed to access this application.');
        }

        return $next($request);
    }
}
