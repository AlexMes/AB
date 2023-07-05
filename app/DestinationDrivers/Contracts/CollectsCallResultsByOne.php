<?php

namespace App\DestinationDrivers\Contracts;

use App\LeadDestination;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface CollectsCallResultsByOne
{
    /**
     * Pulls results by one from destination
     *
     * @param LeadDestination $destination
     *
     * @return Collection
     */
    public function pullResultsByOne(LeadDestination $destination, Carbon $since): Collection;
}
