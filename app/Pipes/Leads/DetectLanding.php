<?php

namespace App\Pipes\Leads;

use App\Domain;
use App\Lead;
use Closure;

class DetectLanding
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
        $lead->landing_id = $this->getLandingId($lead);

        return $next($lead);
    }

    /**
     * Determine if we have leads with same click
     * and phone number
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    protected function getLandingId(Lead $lead)
    {
        return optional(Domain::landing()->where('url', 'https://' . $lead->domain)->first())->id;
    }
}
