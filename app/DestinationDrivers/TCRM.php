<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TCRM implements DeliversLeadToDestination
{
    /**
     * URL of the form
     *
     * @var string
     */
    protected string $url;
    protected ?Response $response = null;
    protected ?string $error;

    public function __construct($configuration = null)
    {
        $this->url = $configuration['url'];
    }

    /**
     * Send lead to destination
     *
     * @param \App\Lead $lead
     */
    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url, [
            'firstname' => $lead->firstname,
            'lastname'  => $lead->lastname,
            'phone'     => $lead->formatted_phone,
            'email'     => $lead->email ?? null,
        ]);

        $this->error = $this->response->successful() ? null : $this->response->body();
    }

    /**
     * Determine is lead successfully delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->error === null;
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Get redirect url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * Get ID from the external system
     *
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->response->offsetGet('id');
    }
}
