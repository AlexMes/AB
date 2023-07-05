<?php

namespace App\CRM\Http\Middlewares;

use App\Manager;
use Closure;
use Illuminate\Support\Facades\App;

class DetermineLocale
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
        if ($request->user() instanceof Manager) {
            if (in_array($request->user()->locale, ['en', 'ru'])) {
                App::setLocale($request->user()->locale);

                return $next($request);
            }
        }

        App::setLocale('ru');

        return $next($request);
    }
}
