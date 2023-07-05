<?php

namespace App\Leads\Pipes;

use App\Affiliate;
use App\Lead;
use Closure;

class DetermineAffiliate implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        /** @var Affiliate $affiliate */
        $affiliate = Affiliate::select(['id','offer_id'])
            ->where('api_key', $lead->api_key)
            ->first();

        if ($affiliate !== null) {
            $lead->affiliate_id = $affiliate->id;
            if ($lead->offer_id === null) {
                $lead->offer_id = $affiliate->offer_id;
            }
            $lead->user_id      = $this->userFor($affiliate);
        }

        unset($lead->api_key);

        return $next($lead);
    }

    protected function userFor(Affiliate $affiliate)
    {
        if ($affiliate->branch_id === 19) {
            return 233;
        }

        if ($affiliate->branch_id === 20) {
            return 236;
        }

        return null;
    }
}
