<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Str;

class Hyper implements DeliversLeadToDestination
{
    protected $affc;
    protected $bxc;
    protected $token;
    protected $response;


    public function __construct($configuration = null)
    {
        $this->affc  = $configuration['affc'];
        $this->bxc   = $configuration['bxc'];
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'x-api-key' => $this->token
        ])->post('https://hypernet.pro/api/affiliate/integration/lead', [
            'affc'        => $this->affc,
            'bxc'         => $this->bxc,
            'password'    => sprintf("%s%s", Str::random(10), rand(10, 99)),
            'firstName'   => $lead->firstname,
            'lastName'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'       => $lead->getOrGenerateEmail(),
            'phone'       => $lead->phone,
            'ip'          => $lead->ip,
            'funnel'      => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'landingURL'  => $lead->domain,
            'geo'         => optional($lead->ipAddress)->country_code ?? optional($lead->lookup)->country ?? 'RU',
            'lang'        => 'RU',
            'landingLang' => 'RU',
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'success', false);
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'redirectUrl', null);
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'leadId', null);
    }
}
