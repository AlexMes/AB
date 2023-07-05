<?php

namespace App\DestinationDrivers\Contracts;

use App\Lead;

interface DeliversLeadToDestination
{
    /**
     * DeliversLeadToDestination constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null);

    /**
     * Sends lead to desired destination
     *
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void;

    /**
     * Determines is delivery was successful
     *
     * @return bool
     */
    public function isDelivered(): bool;

    /**
     * Gets delivery failed error
     *
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * Get link to redirect user to it
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string;

    /**
     * @return string|null
     */
    public function getExternalId(): ?string;
}
