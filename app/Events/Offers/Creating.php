<?php

namespace App\Events\Offers;

use App\Offer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Creating
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Offer
     */
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
