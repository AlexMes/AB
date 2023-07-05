<?php

namespace App\Jobs\Leads;

use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectTrafficSource implements ShouldQueue
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
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->lead->hasLanding()) {
            $this->lead->recordAs(Lead::TRAFFIC_SOURCE_DETECTED)->update([
                'traffic_source_id' => $this->lead->landing->traffic_source_id,
            ]);
        } elseif ($this->lead->hasAffiliate()) {
            $this->lead->recordAs(Lead::TRAFFIC_SOURCE_DETECTED)->update([
                'traffic_source_id' => $this->lead->affiliate->traffic_source_id,
            ]);
        }
    }
}
