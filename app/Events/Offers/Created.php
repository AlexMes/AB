<?php

namespace App\Events\Offers;

use App\Offer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Created
{
    use Dispatchable;
    use SerializesModels;

    public Offer $offer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }
}
