<?php

namespace App\Listeners\Leads;

use App\Events\Lead\Created;
use App\Integrations\Form;
use Illuminate\Contracts\Queue\ShouldQueue;

class RunFormSenders implements ShouldQueue
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
     * Send lead to forms
     *
     * @return void
     */
    private function send()
    {
        $this->lead->landing
            ->forms()
            ->active()
            ->each(function (Form $form) {
                $form->dispatch($this->lead);
            });
    }

    /**
     * Determine when Forms should be sent
     *
     * @return bool
     */
    protected function shouldSend(): bool
    {
        return $this->lead->isValid() && $this->lead->hasLanding();
    }
}
