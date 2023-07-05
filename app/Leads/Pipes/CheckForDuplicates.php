<?php

namespace App\Leads\Pipes;

use App\Affiliate;
use App\Lead;
use App\Offer;
use App\Trail;
use Closure;
use Illuminate\Validation\ValidationException;

class CheckForDuplicates implements LeadProcessingPipe
{
    /**
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return mixed
     */
    public function handle(Lead $lead, Closure $next)
    {
        if ($this->isDuplicate($lead)) {
            throw ValidationException::withMessages([
                'general' => [
                    'ClickID or phone number was previously sent by you. Submission rejected.'
                ]
            ]);
        }

        return $next($lead);
    }

    protected function isDuplicate(Lead $lead): bool
    {
        if ($lead->hasAffiliate() && $this->affiliateAllowsDuplicates($lead->affiliate)) {
            app(Trail::class)->add('Duplicates allowed for affiliate.');

            return false;
        }

        if ($lead->hasLanding() && $lead->landing->allow_duplicates) {
            app(Trail::class)->add('Duplicates allowed for landing.');
            app(Trail::class)->add($lead->domain);

            return false;
        }

        if ($this->offerAllowsDuplicates($lead->offer)) {
            app(Trail::class)->add('Duplicates allowed for offer.');

            return false;
        }

        $result = Lead::query()
            ->withTrashed()
            ->when(
                $lead->hasAffiliate(),
                fn ($query) => $query->where("leads.affiliate_id", $lead->affiliate_id)
            )
            ->when(
                $lead->hasLanding(),
                fn ($query) => $query->where("leads.landing_id", $lead->landing_id)
            )
            ->where(
                fn ($query) => $query
                    ->where("leads.phone", $lead->phone)
                    ->when(
                        $lead->clickid,
                        fn ($q) => $q->orWhere("leads.clickid", $lead->clickid)
                    )
            )->exists();

        if (! $result) {
            app(Trail::class)->add(sprintf('Duplicate not found for lead. landing %s, phone %s clickid %s', $lead->domain, $lead->phone, $lead->clickid ?? 'none'));
        }

        return $result;
    }

    protected function offerAllowsDuplicates(Offer $offer):bool
    {
        if (in_array($offer->branch_id, [19])) {
            return true;
        }

        return $offer->allow_duplicates;
    }

    protected function affiliateAllowsDuplicates(Affiliate $affiliate):bool
    {
        if (in_array($affiliate->branch_id, [19])) {
            return true;
        }

        return false;
    }
}
