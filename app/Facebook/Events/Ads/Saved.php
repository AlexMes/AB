<?php

namespace App\Facebook\Events\Ads;

use App\Facebook\Ad;

class Saved
{
    /**
     * Ad instance
     *
     * @var \App\Facebook\Ad
     */
    public $ad;

    /**
     * CampaignCreated constructor.
     *
     * @param \App\Facebook\Ad $ad
     *
     * @return void
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }
}
