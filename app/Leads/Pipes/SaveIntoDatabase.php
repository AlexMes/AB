<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;

class SaveIntoDatabase implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        $lead->save();

        return $next($lead);
    }
}
