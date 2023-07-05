<?php

namespace App\Events;

use App\Affiliate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AffiliateCreating
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Affiliate
     */
    public Affiliate $affiliate;

    /**
     * Create a new event instance.
     *
     * @param \App\Affiliate $affiliate
     */
    public function __construct(Affiliate $affiliate)
    {
        $this->affiliate = $affiliate;
    }
}
