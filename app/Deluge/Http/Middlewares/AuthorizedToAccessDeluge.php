<?php

namespace App\Deluge\Http\Middlewares;

use App\User;
use Closure;

class AuthorizedToAccessDeluge
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
        if (auth('web')->check() && auth('web')->user()->allowedToSeeDeluge()) {
            // if (auth()->id() === 89) {
            //     auth($guard)->user()->actAs(User::ADMIN);
            // }

            return $next($request);
        }

        auth('web')->logout();

        return redirect()->route('deluge.login')->withErrors([
            'access' => 'You are not allowed to access this application.'
        ]);
    }
}
