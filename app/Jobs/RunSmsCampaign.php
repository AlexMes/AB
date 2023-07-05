<?php

namespace App\Jobs;

use App\Lead;
use App\SmsCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunSmsCampaign implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var SmsCampaign
     */
    protected $campaign;

    /**
     * DetectCampaignOffer constructor.
     *
     * @param SmsCampaign $campaign
     */
    public function __construct(SmsCampaign $campaign)
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
        Lead::query()
            ->valid()
            ->where('landing_id', $this->campaign->landing_id)
            ->where(
                'created_at',
                'like',
                sprintf("%s%%", now()->subMinutes($this->campaign->after_time)->format('y-m-d h:i'))
            )
            ->get()
            ->each(function (Lead $lead) {
                $this->campaign->dispatch($lead);
            });
    }
}
