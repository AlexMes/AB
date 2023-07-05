<?php


namespace App\LeadAssigner\Checks;

use App\Lead;
use Closure;
use Illuminate\Support\Str;

class EnsureSplitterEnabledOnLanding
{
    /**
     * Check that lead must be sent to the docs
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return void
     */
    public function handle(Lead $lead, Closure $next)
    {
        if (Str::startsWith(optional($lead->offer)->name, 'LO_')) {
            //todo remove this, temp solution for old leads.
            return $next($lead);
        }

        if ($lead->hasAffiliate() || $lead->hasLanding() && $lead->landing->splitterEnabled()) {
            return $next($lead);
        }
    }
}
