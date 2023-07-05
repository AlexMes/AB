<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;

class EnsureLeadIsNotAssigned implements LeadProcessingPipe
{
    //todo: this class and all calls to it, MUST BE REMOVED once resell is done
    public function handle(Lead $lead, Closure $next)
    {
        if ($lead->assignments()->where('created_at', '>', now()->subDay())->doesntExist()) {
            return $next($lead);
        }
    }
}
