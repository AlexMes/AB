<?php

namespace App\Facebook\Jobs;

use App\Facebook\AdSet;
use App\Facebook\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StopCampaignAdSets implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Campaign
     */
    protected $campaign;

    /**
     * Create a new job instance.
     *
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->campaign
            ->adSets()
            ->where('effective_status', '=', 'ACTIVE')
            ->get()
            ->each(function ($adSet) {
                Log::debug("AdSet stopped id:{$adSet->id}");

                /** @var AdSet $adSet */
                $adSet->stop();
            });
    }

    /**
     * @return array
     */
    public function tags()
    {
        return [
            'campaign_id' => $this->campaign->id
        ];
    }
}
