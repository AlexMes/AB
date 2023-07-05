<?php

namespace App\Diagnostic\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DevelopersOnly
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->id === 1) {
            return $next($request);
        }

        abort(403);
    }
}
