<?php

namespace App\Unity\Pipes;

use Closure;

class ValidateDate
{
    /**
     * @param array    $attributes
     * @param \Closure $next
     *
     * @return void
     */
    public function handle(array $attributes, Closure $next)
    {
        if (!empty($attributes['date'])) {
            return $next($attributes);
        }
    }
}
