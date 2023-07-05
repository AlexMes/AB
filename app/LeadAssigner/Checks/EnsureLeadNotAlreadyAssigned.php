<?php


namespace App\LeadAssigner\Checks;

use App\Lead;
use App\LeadOrderAssignment;
use Closure;

class EnsureLeadNotAlreadyAssigned
{
    /**
     * Check that lead not already assigned to someone
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return void
     */
    public function handle(Lead $lead, Closure $next)
    {
        if (LeadOrderAssignment::where('lead_id', $lead->id)->doesntExist()) {
            return $next($lead);
        }
    }
}
