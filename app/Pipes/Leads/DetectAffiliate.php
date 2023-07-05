<?php

namespace App\Pipes\Leads;

use App\Affiliate;
use App\Lead;
use Closure;

class DetectAffiliate
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
        $affiliate = $this->getAffiliate($lead->requestData['api_key'] ?? null);

        if ($lead->api_key !== null) {
            $lead->affiliate_id = optional($affiliate)->id;
            $lead->offer_id     = optional($affiliate)->offer_id;
        };

        // Remove affiliate key from the fucking lead
        unset($lead->api_key);

        return $next($lead);
    }

    /**
     * Determine if we have leads with same click
     * and phone number
     *
     * @param $token
     *
     * @return \App\Affiliate | null
     */
    protected function getAffiliate($token)
    {
        return optional(Affiliate::whereApiKey($token)->first());
    }
}
