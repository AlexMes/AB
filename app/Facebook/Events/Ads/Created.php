<?php

namespace App\Facebook\Events\Ads;

use App\Facebook\Ad;

class Created
{

    /**
     * Created ad instance
     *
     * @var \App\Facebook\Ad
     */
    public Ad $ad;

    /**
     * Created constructor.
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
