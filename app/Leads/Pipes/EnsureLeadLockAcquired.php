<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;
use Illuminate\Support\Facades\Cache;

class EnsureLeadLockAcquired implements LeadProcessingPipe
{
    /**
     * Ensure that we distribute lead not faster
     * than once in 20 seconds
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return mixed|void
     */
    public function handle(Lead $lead, Closure $next)
    {
        if ($this->acquired($lead)) {
            return $next($lead);
        }
    }

    /**
     * Get that lock, babe
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    protected function acquired(Lead $lead): bool
    {
        return Cache::lock(sprintf("app-leads-distribution-%s", $lead->id), 1)->get();
    }
}
