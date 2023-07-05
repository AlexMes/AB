<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class UkrTraff implements DeliversLeadToDestination, CollectsCallResults
{
    protected $affiliate;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->affiliate = $configuration['affiliate'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://statistic.icu/send-params-land', $payload = [
            'api'          => true,
            'affiliate'    => $this->affiliate,
            'campaign'     => 'tesla-platform',
            'ext-campaign' => $lead->domain.'?utm_source=t',
            'country'      => optional($lead->ipAddress)->country_code,
            'first-name'   => $lead->firstname,
            'last-name'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'        => $lead->getOrGenerateEmail(),
            'phone'        => $lead->phone,
            'ip'           => $lead->ip,
            'source'       => 1,
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->response->offsetGet('success');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'autologin');
    }

    public function getExternalId(): ?string
    {
        return null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect();
    }
}
