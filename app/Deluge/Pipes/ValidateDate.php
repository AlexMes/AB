<?php

namespace App\Deluge\Pipes;

use Closure;

class ValidateDate
{
    /**
     * Determines is lead duplicate
     *
     * @param array    $attributes
     * @param \Closure $next
     *
     * @return void
     */
    public function handle(array $attributes, Closure $next)
    {
        if (
            !empty($attributes['date_start']) && !empty($attributes['date_end'])
            && $attributes['date_start'] === $attributes['date_end']
        ) {
            return $next($attributes);
        }
    }
}
