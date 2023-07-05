<?php


namespace App\LeadAssigner\Checks;

use App\Lead;
use Closure;

class EnsureLeadIsValid
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
        if ($lead->isValid()) {
            $next($lead);
        }
    }
}
