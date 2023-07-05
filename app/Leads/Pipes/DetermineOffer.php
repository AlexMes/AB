<?php

namespace App\Leads\Pipes;

use App\Lead;
use App\Offer;
use Closure;

class DetermineOffer implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        if ($lead->offer !== null && $offer = Offer::whereUuid($lead->offer)->first()) {
            $lead->offer_id = $offer->id;
        }

        unset($lead->offer);

        return $next($lead);
    }
}
