<?php

namespace App\Jobs\Leads;

use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectOffer implements ShouldQueue
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
        if ($this->lead->offer_id !== null) {
            return;
        }

        if ($this->lead->hasAffiliate()) {
            $this->lead->recordAs(Lead::OFFER_DETECTED)->update([
                'offer_id' => $this->lead->affiliate->offer_id,
            ]);
        } elseif ($this->lead->hasLanding()) {
            $this->lead->recordAs(Lead::OFFER_DETECTED)->update([
                'offer_id' => $this->lead->landing->offer_id,
            ]);
        } elseif ($this->lead->campaign_id !== null) {
            $this->lead->recordAs(Lead::OFFER_DETECTED)->update([
                'offer_id' => $this->lead->campaign->offer_id,
            ]);
        }
    }
}
