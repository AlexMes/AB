<?php

namespace App\Jobs;

use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullLeadTrafficSource implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Lead
     */
    protected Lead $lead;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead->load('click.binom');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->lead->hasAffiliate()) {
            return;
        }

        $click = optional(optional($this->lead->click)->binom)->getClick($this->lead->clickid);

        $this->lead->saveTrafficSourceFromClick($click);
    }
}
