<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class Epc implements DeliversLeadToDestination
{
    protected $response;
    protected $token;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()->post('https://partners.epc.club/api/v1/leads/create', [
            'api_token'       => $this->token,
            'first_name'      => $lead->firstname,
            'last_name'       => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'           => $lead->getOrGenerateEmail(),
            'prefix'          => $lead->lookup->prefix,
            'phone'           => substr($lead->formatted_phone, strlen($lead->lookup->prefix)),
            'page_name'       => 'demo',
            'actual_url'      => $lead->domain,
            'country_iso'     => $lead->lookup->country,
            'language'        => 'RU',
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful();
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.id', null);
    }
}
