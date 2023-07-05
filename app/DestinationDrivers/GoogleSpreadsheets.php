<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Jobs\PushLeadToManagerSpreadSheet;
use App\Lead;

class GoogleSpreadsheets implements DeliversLeadToDestination
{
    /**
     * GoogleSpreadsheets constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        // No config required here
    }

    /**
     * Dispatch lead to destination
     *
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        // pushes last assignment, not good but ...
        PushLeadToManagerSpreadSheet::dispatchNow($lead->current_assignment);
    }

    /**
     * @return bool
     */
    public function isDelivered(): bool
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return null;
    }
}
