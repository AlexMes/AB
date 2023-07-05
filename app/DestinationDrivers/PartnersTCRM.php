<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PartnersTCRM implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url = 'https://al.rcrm.app';
    protected $token;
    protected $campaignToken;
    protected $partnerId;
    protected $campaignId;
    protected $sourceId;
    protected $lang;

    protected $response;

    public function __construct($configuration = null)
    {
        $this->url           = $configuration['url'] ?? $this->url;
        $this->token         = $configuration['token'];
        $this->campaignToken = $configuration['campaign_token'];
        $this->partnerId     = $configuration['partner_id'] ?? null;
        $this->campaignId    = $configuration['campaign_id'] ?? null;
        $this->sourceId      = $configuration['source_id'] ?? null;
        $this->lang          = $configuration['lang'] ?? 'RU';
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->get($this->url . '/api/partner-leads', [
                'since'   => $since->toDateTimeString(),
                'until'   => now()->toDateTimeString(),
                'perPage' => 100,
                'page'    => $page,
            ])->throw()->offsetGet('data'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['internal_id'],
                    'status'      => $item['status'],
                    'isDeposit'   => (bool)$item['deposited_at'],
                    'depositDate' => $item['deposited_at'],
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Authorization'  => 'Bearer ' . $this->token,
            'Campaign-Token' => $this->campaignToken
        ])->post($this->url . '/api/partner-leads', $payload = [
            'first_name'           => $lead->firstname,
            'last_name'            => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'                => $lead->phone,
            'email'                => $lead->getOrGenerateEmail(),
            'IP'                   => $lead->ip,
            'registration_website' => $lead->domain . '/?utm_source=1',
            'language'             => $this->lang,
            'sub1'                 => $lead->getPollAsText(),
            'partner_id'           => $this->partnerId,
            'campaign_id'          => $this->campaignId,
            'source_id'            => $this->sourceId,
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
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead.internal_lead_id');
    }
}
