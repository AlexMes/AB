<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Globinc implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://my.globinc.ai';
    protected string $token;
    protected $response;
    protected $utmCampaign;
    protected $utmSource;
    protected $utmMedium;
    protected $utmContent;

    public function __construct($configuration = null)
    {
        $this->url         = $configuration['url'] ?? $this->url;
        $this->token       = $configuration['token'];
        $this->utmCampaign = $configuration['utm_campaign'] ?? null;
        $this->utmSource   = $configuration['utm_source'] ?? null;
        $this->utmMedium   = $configuration['utm_medium'] ?? null;
        $this->utmContent  = $configuration['utm_content'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(data_get(Http::withHeaders([
            'Authorization' => ' Bearer ' . $this->token,
        ])->get($this->url . '/api/affiliate/register', [
            'from'     => $since->unix(),
            'to'       => now()->addDay()->unix(),
            'per_page' => 500,
            'page'     => $page,
        ]), 'items'))->map(fn ($item) => new CallResult([
            'id'          => $item['uuid'],
            'status'      => $item['status'],
            'isDeposit'   => $item['deposited'],
            'depositDate' => $item['deposited_at'] ? Carbon::parse($item['deposited_at'])->toDateString() : null,
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Authorization' => ' Bearer ' . $this->token,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])->post($this->url . '/api/affiliate/register', $payload = [
            'email'        => $lead->getOrGenerateEmail(),
            'phone'        => '+' . $lead->phone,
            'first_name'   => $lead->firstname,
            'last_name'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'utm_campaign' => $this->utmCampaign,
            'utm_source'   => $this->utmSource,
            'utm_medium'   => $this->utmMedium,
            'utm_content'  => $this->utmContent,
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
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
        return data_get($this->response->json(), 'redirectUrl');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'client_id');
    }
}
