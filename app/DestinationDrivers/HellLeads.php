<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HellLeads implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url  = 'https://api.hell-leads.com';
    protected ?string $gi;
    protected ?string $token;
    protected $sendQuiz;
    protected $affParam1;
    protected $affParam2;
    protected $affParam3;
    protected $affParam4;
    protected $affParam5;

    public bool $nullInterval = false;
    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url       = $configuration['url'] ?? $this->url;
        $this->gi        = $configuration['gi'] ?? null;
        $this->token     = $configuration['token'] ?? null;
        $this->sendQuiz  = $configuration['send_quiz'] ?? false;
        $this->affParam1 = $configuration['aff_param1'] ?? null;
        $this->affParam2 = $configuration['aff_param2'] ?? null;
        $this->affParam3 = $configuration['aff_param3'] ?? null;
        $this->affParam4 = $configuration['aff_param4'] ?? null;
        $this->affParam5 = $configuration['aff_param5'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable()->subWeek();

        $data = data_get(Http::withHeaders([
            'api-key' => $this->token,
        ])->post(sprintf('%s/get_lead_status/', $this->url), [
            'rangeFrom' => $since->addWeeks($page - 1)->toDateString(),
            'rangeTo'   => $since->addWeeks($page)->toDateString(),
        ])->throw(), 'info.transactions');

        $this->nullInterval = empty($data) && $since->addWeeks($page)->lessThanOrEqualTo(now());

        return collect($data)->map(function ($item) {
            return new CallResult([
                'id'          => $item['transaction_id'],
                'status'      => $item['conversion_status'],
                'isDeposit'   => in_array($item['conversion_status'], ['ftd', 'FTD'])
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'api-key' => $this->token,
        ])->post(
            sprintf('%s/v2/create_lead/', $this->url),
            $payload = $this->payload($lead)
        );

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function payload(Lead $lead): array
    {
        $payload = [
            'gi'          => $this->gi,
            'email'       => $lead->getOrGenerateEmail(),
            'firstname'   => $lead->firstname,
            'lastname'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'country'     => optional($lead->ipAddress)->country_code ?? 'RU',
            'phone'       => '+' . $lead->phone,
            'ip'          => $lead->ip,
            'sub_id1'     => $lead->uuid,
            'aff_param1'  => $this->affParam1 ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
        ];

        if ($this->sendQuiz || $this->affParam2) {
            $payload['aff_param2'] = $this->affParam2 ?? $lead->getPollAsText();
        }
        if ($this->affParam3) {
            $payload['aff_param3'] = $this->affParam3;
        }
        if ($this->affParam4) {
            $payload['aff_param4'] = $this->affParam4;
        }
        if ($this->affParam5) {
            $payload['aff_param5'] = $this->affParam5;
        }

        return $payload;
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
        return data_get($this->response->json(), 'info.autologin');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'info.lead_id');
    }
}
