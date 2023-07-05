<?php

namespace App\Deluge\Http\Middlewares;

use App\AdsBoard;
use Closure;

class CheckOffice
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
        if (app()->environment('local')) {
            return $next($request);
        }


        if ($request->user()->hasRole(['admin','head','support'])) {
            return $next($request);
        }

        if (in_array($request->ip(), AdsBoard::DELUGE_OFFICE_IPS)) {
            return $next($request);
        }

        // if ($request->user()->id === 89) {
        //     return $next($request);
        // }

        auth('web')->logout();

        return redirect()->route('deluge.login')->withErrors([
            'access' => 'You are not allowed to access this application from current IP.'
        ]);
    }
}
