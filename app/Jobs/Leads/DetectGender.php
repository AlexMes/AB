<?php

namespace App\Jobs\Leads;

use App\Lead;
use App\Services\GenderApi\GenderAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectGender implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Lead instance
     *
     * @var \App\Lead
     */
    protected $lead;

    /**
     * Limit number of attemtps
     *
     * @var int
     */
    public $tries = 2;

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
     *
     * @param \App\Services\GenderApi\GenderAPI $api
     *
     * @return void
     */
    public function handle(GenderApi $api)
    {
        $this->lead->recordAs(Lead::GENDER_DETECTED)->update([
            'gender_id' => $this->lead->fullname === null
                ? 0
                : $api->detect($this->lead->fullname),
        ]);
    }
}
