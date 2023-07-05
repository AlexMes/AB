<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class Board implements DeliversLeadToDestination
{
    protected $response;
    protected $token;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://uleads.app/leads/register', [
            'firstname' => $lead->firstname,
            'lastname'  => $lead->lastname,
            'email'     => $lead->getOrGenerateEmail(),
            'phone'     => $lead->phone,
            'api_key'   => $this->token,
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'lead.id') !== null;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'redirect');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead.id');
    }
}
