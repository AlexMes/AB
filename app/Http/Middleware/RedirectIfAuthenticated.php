<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
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
        if (Auth::guard($guard)->check()) {
            if ($guard === 'crm') {
                return redirect()->intended(route('crm.assignments.index'));
            }

            if ($guard === 'web' && $request->routeIs('deluge.*')) {
                if (auth()->user()->isDeveloper()) {
                    return redirect()->route('deluge.bundles.index');
                }

                if (auth()->user()->isDesigner()) {
                    return redirect()->route('deluge.reports.performance');
                }

                if (auth()->user()->isFinancier()) {
                    return redirect()->route('deluge.credit-cards.index');
                }

                return redirect()->route('deluge.accounts.index');
            }

            return redirect()->intended('dashboard');
        }

        return $next($request);
    }
}
