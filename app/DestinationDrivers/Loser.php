<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;

class Loser implements DeliversLeadToDestination
{
    public function __construct($configuration = null)
    {
    }

    public function send(Lead $lead): void
    {
    }

    public function isDelivered(): bool
    {
        return false;
    }

    public function getError(): ?string
    {
        return null;
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        return null;
    }
}
