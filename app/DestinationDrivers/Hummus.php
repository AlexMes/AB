<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Hummus implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url      = 'https://humus-network.com';
    protected string $source   = 'Office2';
    protected string $campaign = 'RU';
    protected ?string $page;
    protected ?string $landing;
    protected ?string $webTeam;
    protected $apiKey;

    protected $response;
    protected $getExternalId = null;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'] ?? $this->url;
        $this->source   = $configuration['source'] ?? $this->source;
        $this->campaign = $configuration['campaign'] ?? $this->campaign;
        $this->webTeam  = $configuration['web_team'] ?? null;
        $this->page     = $configuration['page'] ?? null;
        $this->landing  = $configuration['landing'] ?? null;
        $this->apiKey   = $configuration['api_key'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        if (!$this->apiKey) {
            return collect();
        }

        return collect(Http::get($this->url . '/api/statuses-ftd', [
            'api_key'   => $this->apiKey,
            'date_from' => $since->toDateString(),
            'date_to'   => now()->toDateString(),
            'per_page'  => 500,
            'page'      => $page,
        ])->throw()->offsetGet('data'))->map(function ($item) {
            return new CallResult([
                'id'          => Carbon::parse($item['created_at'])->toDateString() . $item['email'],
                'status'      => $item['status'],
                'isDeposit'   => (bool)$item['is_ftd'],
                'depositDate' => $item['ftd_date'],
                'depositSum'  => '151',
            ]);
        });
    }

    protected function payload(Lead $lead): array
    {
        $payload = [
            'first_name'  => $lead->firstname,
            'last_name'   => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'campaign'    => $this->campaign,
            'email'       => $lead->getOrGenerateEmail(),
            'phone'       => $lead->phone,
            'landing'     => $this->landing ?? ('https://' . $lead->domain),
            'country'     => optional($lead->ipAddress)->country_code ?? 'RU',
            'vertical'    => 'Forex',
            'page'        => $this->page ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'source'      => $this->source,
            'ip'          => $lead->ip,
            'description' => $lead->getPollAsText(),
        ];

        if (Str::contains($this->url, 'everestraffic')) {
            $payload['web_team'] = $this->webTeam;
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->asForm()->post(
            sprintf('%s/api/lead', $this->url),
            $payload = $this->payload($lead)
        );

        $this->getExternalId = now()->toDateString() . $payload['email'];

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() &&
            (data_get($this->response->json(), 'Status') === 'Success' || $this->forceDelivered());
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'Link');
    }

    public function getExternalId(): ?string
    {
        return $this->isDelivered() ? $this->getExternalId : null;
    }

    protected function forceDelivered(): bool
    {
        if (Str::contains($this->url, 'everestraffic.com')) {
            return false;
        }

        return data_get($this->response->json(), 'Error') === 'Offer not selected';
    }
}
