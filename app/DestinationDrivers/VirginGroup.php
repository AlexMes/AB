<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class VirginGroup implements DeliversLeadToDestination, CollectsCallResults
{
    private const STATUS_NEW      = 1;
    private const STATUS_HOLD     = 2;
    private const STATUS_ACCEPTED = 3;
    private const STATUS_REJECTED = 4;
    private const STATUS_NONVALID = 5;

    private const STATUSES = [
        self::STATUS_NEW      => 'Leads pending and in processing',
        self::STATUS_HOLD     => 'Leads on hold',
        self::STATUS_ACCEPTED => 'Accepted leads',
        self::STATUS_REJECTED => 'Rejected leads',
        self::STATUS_NONVALID => 'Nonvalid leads',
    ];

    protected $response;

    protected $url = 'https://virgin-group.team';
    protected $token;
    protected $campaignId;
    protected $companyName;
    protected $geo;
    protected $answersInText;
    protected $offerNameInComm;

    public function __construct($configuration = null)
    {
        $this->url             = $configuration['url'] ?? $this->url;
        $this->token           = $configuration['token'];
        $this->campaignId      = $configuration['campaign_id'] ?? null;
        $this->companyName     = $configuration['company_name'] ?? null;
        $this->geo             = $configuration['geo'] ?? 'RUS';
        $this->answersInText   = $configuration['answers_in_text'] ?? true;
        $this->offerNameInComm = $configuration['offer_name_in_comment'] ?? false;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        if ($page > 1) {
            return collect();
        }

        return collect(data_get(Http::get($this->url . '/api/v1/get-leads', [
            'auth_token' => $this->token,
        ])->json(), 'leads'))
            ->map(fn ($item) => new CallResult([
                'id'     => $item['id'],
                'status' => $item['client_type_id'] === 1
                    ? 'Deposit'
                    : (
                        array_key_exists($item['call_status'], self::STATUSES)
                            ? self::STATUSES[$item['call_status']]
                            : 'NONE'
                    ),
                'isDeposit' => $item['client_type_id'] === 1,
            ]));
    }

    public function payload(Lead $lead)
    {
        $payload = [
            'auth_token'   => $this->token,
            'first_name'   => $lead->firstname,
            'last_name'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'        => $lead->getOrGenerateEmail(),
            'ip'           => $lead->ip,
            'geo'          => $this->geo,
            'phone'        => $lead->phone,
            'company_name' => $this->companyName,
            'campaign_id'  => $this->campaignId,
            'password'     => 'Sa010406'
        ];

        if ($this->offerNameInComm) {
            $payload['comment'] = $lead->offer->name;
        } else {
            $payload['comment'] = $this->answersInText ? $lead->getPollAsText() : $lead->getPollAsUrl();
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url . '/api/v1', $payload = $this->payload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', [
            'body' => $this->response->body(),
            'json' => $this->response->json(),
        ]);
    }

    public function isDelivered(): bool
    {
        return ($this->response->successful() || data_get($this->response->json(), '0.original.success')) && $this->getExternalId() !== null;
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
        return data_get($this->response->json(), 'data.id', data_get($this->response->json(), '0.original.data.id'));
    }
}
