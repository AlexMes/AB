<?php

namespace App\Http\Middleware;

use Closure;

class ProperPourerIp
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
        if ($request->ip() === config('services.pourer.ip')) {
            return $next($request);
        }

        return response(['message' => 'access denied'], 403);
    }
}
