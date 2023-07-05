<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SafeTrade implements DeliversLeadToDestination
{
    /**
     * @var string
     */
    protected $url = 'https://crm.stradecrm.com/api/v2/lead';

    /**
     * Offer key
     *
     * @var string
     */
    protected $key;

    /**
     * Authorization token
     *
     * @var mixed
     */
    protected $token;

    /**
     * Response object
     *
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;

    /**
     * SafeTrade constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
        $this->key   = $configuration['key'];
    }

    /**
     * Send the lead to destination
     *
     * @param \App\Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function send(Lead $lead): void
    {
        $this->response = Http::withToken($this->token)->post($this->url, [
            'first_name'   => $lead->firstname,
            'last_name'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone_number' => $lead->formatted_phone,
            'country'      => optional($lead->ipAddress)->country ?? 'RU',
            'email'        => $lead->getOrGenerateEmail(),
            'address'      => 'Unknown',
            'referer'      => $lead->domain,
            'token'        => $this->key,
        ]);
    }

    /**
     * Determines is delivery ok
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->response->successful();
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return Str::limit($this->response->body(), 250);
    }

    /**
     * Get redirect url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return Str::limit($this->response->offsetGet('autoLoginLink') ?? null, 250);
    }

    /**
     * Get external id
     *
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->response->offsetGet('leadId') ?? null;
    }
}
