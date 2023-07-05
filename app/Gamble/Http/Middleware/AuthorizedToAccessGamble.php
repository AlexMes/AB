<?php

namespace App\Gamble\Http\Middleware;

use Closure;

class AuthorizedToAccessGamble
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
        if (auth('gamble')->check() && !auth('gamble')->user()->allowedToSeeGamble()) {
            auth('gamble')->logout();

            return abort(403, 'You are not allowed to access this application.');
        }

        if (auth('api')->check() && !auth('api')->user()->allowedToSeeGamble()) {
            return abort(401, 'You are not allowed to access this application.');
        }

        return $next($request);
    }
}
