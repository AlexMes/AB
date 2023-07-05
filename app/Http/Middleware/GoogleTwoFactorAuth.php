<?php

namespace App\Http\Middleware;

use Closure;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class GoogleTwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure                  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!app(Authenticator::class)->boot($request)->isAuthenticated()) {
            if ($request->getHost() === config('deluge.domain')) {
                return redirect()->route('deluge.google-tfa.login');
            }

            return redirect()->route('google-tfa.login');
        }

        return $next($request);
    }
}
