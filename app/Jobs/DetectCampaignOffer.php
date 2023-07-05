<?php

namespace App\Jobs;

use App\Facebook\Campaign;
use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class DetectCampaignOffer implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;

    /**
     * @var \App\Facebook\Campaign
     */
    protected $campaign;

    /**
     * DetectCampaignOffer constructor.
     *
     * @param \App\Facebook\Campaign $campaign
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Handle a job
     *
     * @return void
     */
    public function handle()
    {
        $offer = Offer::all(['id','name'])
            ->sortByDesc(fn ($offer, $key) => strlen($offer['name']))
            ->filter(fn ($offer) => Str::contains(Str::lower($this->campaign->name), sprintf("-%s-", Str::lower($offer->name))))
            ->first();

        if ($offer !== null) {
            $this->campaign->update(['offer_id' => $offer->id]);
        }
    }
}
