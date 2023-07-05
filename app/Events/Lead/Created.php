<?php

namespace App\Events\Lead;

use App\Lead;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Created
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Lead model
     *
     * @var \App\Lead
     */
    public $lead;

    /**
     * Create a new event instance.
     *
     * @param \App\Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
