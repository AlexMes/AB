<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;

class FormatRussianNumbers implements LeadProcessingPipe
{
    /**
     * Foramt phone number if it is russian
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return void
     */
    public function handle(Lead $lead, Closure $next)
    {
        $lead->phone = $lead->formatted_phone;

        return $next($lead);
    }
}
