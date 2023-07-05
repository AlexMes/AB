<?php

namespace App\DestinationDrivers\Modifiers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Str;

class OffersNotFound implements DeliversLeadToDestination
{
    private DeliversLeadToDestination $destination;

    /**
     * DeliversLeadToDestination constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
    }

    /**
     * @param DeliversLeadToDestination $destination
     *
     * @return DeliversLeadToDestination
     */
    public static function wrap(DeliversLeadToDestination $destination): DeliversLeadToDestination
    {
        $wrapper              = new static();
        $wrapper->destination = $destination;

        return $wrapper;
    }

    /**
     * Sends lead to desired destination
     *
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $this->destination->send($lead);
    }

    /**
     * Determines is delivery was successful
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        $error = $this->destination->getError();
        if ($error !== null && Str::contains($error, 'Offers not found')) {
            return true;
        }

        return $this->destination->isDelivered();
    }

    /**
     * Gets delivery failed error
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->destination->getError();
    }

    /**
     * Get link to redirect user to it
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->destination->getRedirectUrl();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->destination->getExternalId();
    }
}
