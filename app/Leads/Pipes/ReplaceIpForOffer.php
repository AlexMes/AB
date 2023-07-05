<?php

namespace App\Leads\Pipes;

use App\IpAddress;
use App\Lead;
use Closure;

class ReplaceIpForOffer implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        if ($lead->offer_id === 1975) {
            $lead->ip = optional(IpAddress::whereCountryCode('RU')->orderByRaw('random()')->first())->ip;
        }

        return $next($lead);
    }
}
