<?php

namespace App\Pipes\Leads;

use App\Lead;
use Closure;

class DetectDuplicate
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
        if ($this->isDuplicate($lead) === false) {
            return $next($lead);
        }
    }

    /**
     * Determine if we have leads with same click
     * and phone number
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    protected function isDuplicate(Lead $lead)
    {
        if ($lead->phone === null && $lead->domain === 'webinariys.com') {
            return Lead::query()->withTrashed()->where('domain', 'webinariys.com')->where('email', $lead->email)->exists();
        }

        return Lead::query()
            ->withTrashed()
            ->when($lead->hasAffiliate(), fn ($query) => $query->where('affiliate_id', $lead->affiliate_id))
            ->when($lead->hasLanding(), fn ($query) => $query->where('landing_id', $lead->landing_id))
            ->where(function ($query) use ($lead) {
                return $query->where('phone', $lead->phone)
                    ->when($lead->clickid, fn ($q) => $q->orWhere('clickid', $lead->clickid));
            })
            ->exists();
    }
}
