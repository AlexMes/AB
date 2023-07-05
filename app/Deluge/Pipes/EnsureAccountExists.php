<?php

namespace App\Deluge\Pipes;

use App\ManualAccount;
use Closure;

class EnsureAccountExists
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
        if (ManualAccount::whereAccountId($attributes['account_id'])->exists()) {
            return $next($attributes);
        }
    }
}
