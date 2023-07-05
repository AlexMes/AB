<?php

namespace App\Listeners\Leads;

use App\Events\Lead\Created;
use App\Lead;
use App\SmsCampaign;
use Illuminate\Contracts\Queue\ShouldQueue;

class RunSmsCampaign implements ShouldQueue
{
    /**
     * @var \App\Lead
     */
    protected $lead;

    /**
     * Handle the event.
     *
     * @param Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        $this->lead = $event->lead->refresh();

        if ($this->shouldSend()) {
            $this->send();
        }
    }

    /**
     * Send SMS message to lead
     *
     * @return void
     */
    private function send()
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
