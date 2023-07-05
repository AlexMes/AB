<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class ClicksAff implements DeliversLeadToDestination
{
    protected $url;
    protected $token;

    protected $response;


    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'];
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'X-Api-Auth' => $this->token,
        ])->post($this->url, [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'      => $lead->getOrGenerateEmail(),
            'phone'      => $lead->phone,
            'country'    => optional($lead->ipAddress)->country_code ?? optional($lead->lookup)->country,
            'language'   => 'EN',
            'password'   => 'Acx12T4',
            'utm_source' => 'BITCOIN CODE',
            'ip'         => $lead->ip,
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
        return data_get($this->response->json(), 'data.autologin_url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.unique_id');
    }
}
