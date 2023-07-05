<?php

namespace App\Jobs\Leads;

use App\Integrations\Form;
use App\Lead;
use App\SmsCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectSenders implements ShouldQueue
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
        $this->lead = $lead->refresh();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->shouldSend()) {
            $this->sendSms();
            $this->sendToForm();
        }
    }

    public function sendToForm()
    {
        $this->lead->landing
            ->forms()
            ->active()
            ->each(function (Form $form) {
                $form->dispatch($this->lead);
            });
    }

    /**
     * Send SMS message to lead
     *
     * @return void
     */
    private function sendSms()
    {
        $this->lead->landing
            ->smsCampaigns()
            ->active()
            ->each(function (SmsCampaign $campaign) {
                $campaign->dispatch($this->lead);
            });
    }

    /**
     * Determine when SMS should be sent
     *
     * @return bool
     */
    protected function shouldSend(): bool
    {
        return $this->lead->isValid() && $this->lead->hasLanding();
    }
}
