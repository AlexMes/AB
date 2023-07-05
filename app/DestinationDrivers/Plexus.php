<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Plexus implements DeliversLeadToDestination, CollectsCallResults
{
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::post('https://crm.plexusfin.com/api/check', [
            'token' => $this->token,
            'from'  => $since->toDateString(),
            'to'    => now()->toDateString(),
            'page'  => $page
        ])->json())->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['status'],
            'isDeposit'   => $item['isFtd'],
            'depositDate' => $item['depositDate'],
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()->post('https://crm.plexusfin.com/api/lead', $payload = [
            'first_name'   => $lead->firstname,
            'last_name'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'        => $lead->phone,
            'email'        => $lead->getOrGenerateEmail(),
            'token'        => $this->token,
            'utm_country'  => $lead->ipAddress->country_code,
            'utm_source'   => $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'utm_medium'   => '',
            'utm_campaing' => '',
            'utm_content'  => '',
        ]);

        $lead->addEvent('payload', $payload);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->getExternalId() !== null;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id');
    }
}
