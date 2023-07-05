<?php


namespace App\LeadAssigner\Checks;

use App\Jobs\Leads\DetectOffer;
use App\Lead;
use Closure;

class EnsureLeadHaveOffer
{
    /**
     * Check that lead have order
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return void
     */
    public function handle(Lead $lead, Closure $next)
    {
        if (! $lead->hasOffer()) {
            DetectOffer::dispatchNow($lead);
        }

        $next($lead);
    }
}
