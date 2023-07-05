<?php

namespace App\Http\Controllers\Adsets;

use App\Facebook\AdSet;
use App\Http\Controllers\Controller;

class Start extends Controller
{
    /**
     * Start adset
     *
     * @param \App\Facebook\AdSet $adset
     *
     * @return \App\Facebook\AdSet
     */
    public function __invoke(Adset $adset)
    {
        $this->authorize('update', $adset);

        return $adset->start();
    }
}
