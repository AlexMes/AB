<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Axela implements DeliversLeadToDestination, CollectsCallResults
{
    protected $response;
    protected string $url = "https://api.axela.network";
    protected $campaign;
    protected $secret;
    protected $language;
    protected ?string $landingUrl;
    protected ?string $sendQuizTo = "sub1";

    public function __construct($configuration = null)
    {
        $this->url        = $configuration['url'] ?? $this->url;
        $this->campaign   = $configuration['campaign'];
        $this->secret     = $configuration['secret'];
        $this->language   = $configuration['language'] ?? null;
        $this->landingUrl = $configuration['landing_url'] ?? null;
        $this->sendQuizTo = $configuration['send_quiz_to'] ?? $this->sendQuizTo;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(data_get(Http::withHeaders([
            'X-Secret-Key' => $this->secret
        ])->post($this->url . '/api/v1/api-lead/statistic', [
            'date_from' => $since->toDateString(),
            'date_to'   => now()->toDateString(),
            'per_page'  => 250,
            'date_type' => 'registration',
            'page'      => $page,
        ])->throw()->json(), 'result.leads'))->map(fn ($item) => new CallResult([
            'id'           => $item['id'],
            'status'       => $item['adv_status'],
            'isDeposit'    => $item['deposit_date'] !== null,
            'deposit_date' => Carbon::parse($item['deposit_date'])->toDateString(),
        ]));
    }

    protected function payload(Lead $lead)
    {
        $payload = [
            'campaign_identifier' => $this->campaign,
            'first_name'          => $lead->firstname,
            'last_name'           => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'               => $lead->getOrGenerateEmail(),
            'phone'               => $lead->phone,
            'ip'                  => $lead->ip,
        ];

        if ($this->language) {
            $payload['iso_2'] = $this->language;
        }

        if ($this->sendQuizTo) {
            $payload[$this->sendQuizTo] = $lead->hasPoll()
                ? $lead->getPollAsUrl()
                : null;
            $payload['landing_url'] = $this->landingUrl ?? $lead->domain;
        } else {
            $payload['landing_url'] = $lead->hasPoll()
                ? $lead->pollResults()->map(fn ($result) => [str_replace(' ', '_', $result->getQuestion()) => $result->getAnswer()])->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)
                : $lead->domain;
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'X-Secret-Key' => $this->secret,
        ])->post($this->url . '/api/v1/api-lead/create', $payload = $this->payload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful()
         && $this->response->offsetExists('success')
         && $this->response->offsetGet('success')
         && data_get($this->response->json(), 'result.is_valid')
         && data_get($this->response->json(), 'result.is_accepted');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        if ($this->isDelivered() && $this->response->offsetExists('result')) {
            return data_get($this->response->json(), 'result.url');
        }

        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->isDelivered() && $this->response->offsetExists('result')) {
            return data_get($this->response->json(), 'result.id');
        }

        return null;
    }
}
