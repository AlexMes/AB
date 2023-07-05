<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;
use Illuminate\Support\Str;

class GenerateUuid implements LeadProcessingPipe
{
    /**
     * Generate UUID for the lead
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return mixed
     */
    public function handle(Lead $lead, Closure $next)
    {
        $lead->uuid = Str::uuid()->toString();

        return $next($lead);
    }
}
