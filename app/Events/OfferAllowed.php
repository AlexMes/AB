<?php

namespace App\Events;

use App\Offer;
use App\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferAllowed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Offer
     */
    public Offer $offer;

    /**
     * @var User
     */
    public User $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Offer $offer, User $user)
    {
        $this->offer = $offer;
        $this->user  = $user;
    }
}
