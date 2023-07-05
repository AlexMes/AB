<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;

class EnsureLeadIsValid implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        if ($lead->isValid()) {
            return $next($lead);
        }
    }
}
