<?php

namespace App\Unity\Pipes;

use App\UnityApp;
use Closure;

class EnsureAppExists
{
    /**
     * @param array    $attributes
     * @param \Closure $next
     *
     * @return void
     */
    public function handle(array $attributes, Closure $next)
    {
        if (UnityApp::whereId($attributes['app_id'])->exists()) {
            return $next($attributes);
        }
    }
}
