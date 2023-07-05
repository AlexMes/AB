<?php

namespace App\Pipes\Leads;

use App\Lead;
use Closure;
use Illuminate\Support\Str;

class GenerateUuid
{
    /**
     * Determines is lead duplicate
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return void
     */
    public function handle(Lead $lead, Closure $next)
    {
        $lead->uuid = Str::uuid()->toString();

        return $next($lead);
    }
}
