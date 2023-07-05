<?php

namespace App\Leads\Pipes;

use App\Domain;
use App\Lead;
use Closure;

class DetermineLanding implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        if ($lead->affiliate_id !== null) {
            return $next($lead);
        }

        $landing = Domain::select(['id','offer_id'])
            ->landing()
            ->where('url', sprintf("https://%s", $lead->domain))
            ->orWhere('url', $lead->domain)
            ->first();

        if ($landing !== null) {
            $lead->landing_id = $landing->id;
            if ($lead->offer_id === null) {
                $lead->offer_id = $landing->offer_id;
            }
        }

        return $next($lead);
    }
}
