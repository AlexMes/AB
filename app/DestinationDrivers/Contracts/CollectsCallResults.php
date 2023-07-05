<?php

namespace App\DestinationDrivers\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface CollectsCallResults
{

    /**
     * Pull results from the destination
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1):Collection;
}
