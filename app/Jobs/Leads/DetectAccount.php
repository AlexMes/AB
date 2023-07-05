<?php

namespace App\Jobs\Leads;

use App\Lead;
use App\ManualCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectAccount implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Lead
     */
    protected $lead;

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
        if ($this->lead->campaign_id !== null) {
            $this->lead->recordAs(Lead::ACCOUNT_DETECTED)->update([
                'account_id' => optional($this->lead->campaign)->account_id ?? ManualCampaign::whereId($this->lead->campaign_id)->value('account_id'),
            ]);
        }
    }
}
