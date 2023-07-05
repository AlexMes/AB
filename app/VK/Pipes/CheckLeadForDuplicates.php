<?php

namespace App\VK\Pipes;

use App\Lead;
use Closure;

class CheckLeadForDuplicates
{
    /**
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return mixed
     */
    public function handle(Lead $lead, Closure $next)
    {
        if (!$this->isDuplicate($lead)) {
            return $next($lead);
        }
    }

    protected function isDuplicate(Lead $lead): bool
    {
        return Lead::query()
            ->withTrashed()
            ->where(fn ($query) => $query->where('phone', $lead->phone))
            ->exists();
    }
}
