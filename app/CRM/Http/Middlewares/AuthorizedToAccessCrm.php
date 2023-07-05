<?php

namespace App\CRM\Http\Middlewares;

use Closure;

class AuthorizedToAccessCrm
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth('crm')->check()) {
            return $next($request);
        }

        if (
            auth('web')->check()
            && auth('web')->user()->allowedToSeeCrm()
        ) {
            return $next($request);
        }

        return redirect()->route('login')->withErrors([
            'access' => 'You are not allowed to access this application.'
        ]);
    }
}
