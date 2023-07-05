<?php

namespace App\Jobs\Leads;

use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Lead;
use App\ManualCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectCampaign implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Lead
     */
    private $lead;

    /**
     * Create a new job instance.
     *
     * @param \App\Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->findViaCampaign();
    }

    /**
     * Find campaign using adset
     *
     * @return bool
     */
    protected function findViaAdSet()
    {
        $adset = AdSet::where('name', $this->lead->utm_content)->first();

        if ($adset) {
            $this->lead->recordAs(Lead::CAMPAIGN_DETECTED)->update([
                'adset_id'    => $adset->id,
                'campaign_id' => $adset->campaign_id,
            ]);

            return true;
        }
    }

    /**
     * Fallback for detecting campaign
     *
     * @return bool
     */
    protected function findViaCampaign()
    {
        $campaign       = Campaign::where('name', $this->lead->utm_source)->first();
        $manualCampaign = ManualCampaign::where('name', $this->lead->utm_source)->first();

        if ($campaign || $manualCampaign) {
            $this->lead->recordAs(Lead::CAMPAIGN_DETECTED)->update([
                'campaign_id' => $campaign->id ?? $manualCampaign->id,
                'account_id'  => $campaign->account_id ?? $manualCampaign->account_id
            ]);

            return true;
        }
    }
}
