<?php

namespace App\Events\Lead;

use App\Lead;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Lead
     */
    public Lead $lead;

    /**
     * Create a new event instance.
     *
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
