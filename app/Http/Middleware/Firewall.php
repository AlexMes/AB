<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class Firewall
{
    /**
     * URLs to exclude from the middleware
     *
     * @var string[]
     */
    protected $except = [
        'api/external/accounts',
    ];

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
        if ($request->user() === null || $this->inExceptArray($request)) {
            return $next($request);
        }

        /** @var \Illuminate\Database\Eloquent\Collection $rules */
        $rules = Cache::remember(
            sprintf('whitelist-%d', $request->user()->id),
            now()->addHours(2),
            fn () => $request->user()->firewall()->pluck('ip')
        );

        if ($rules->isEmpty()) {
            return $next($request);
        }

        if ($rules->contains($request->ip())) {
            return $next($request);
        }

        return abort(403, 'You are not allowed to access app from your current IP');
    }

    /**
     * @param $request
     *
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
