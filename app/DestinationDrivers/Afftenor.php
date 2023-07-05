<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Afftenor implements DeliversLeadToDestination, CollectsCallResults
{
    protected $token;
    protected $offer_id;
    protected $answersInText;
    protected $url = 'https://afftenor.info';
    public bool $nullInterval;
    protected $statusField = 'stage';

    public function __construct($configuration = null)
    {
        $this->url           = $configuration['url'] ?? $this->url;
        $this->token         = $configuration['token'];
        $this->offer_id      = $configuration['offer_id'];
        $this->answersInText = $configuration['answers_in_text'] ?? false;
        $this->statusField   = $configuration['status_field'] ?? $this->statusField;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable()->subWeek();

        $response = Http::post($this->url . '/api/ext/list.json?id=' . $this->token, [
            'from' => $since->addWeeks($page - 1)->startOfDay()->toDateString(),
            'to'   => $since->addWeeks($page)->endOfDay()->toDateString(),
        ])->json();
        $this->nullInterval = !count($response) && $since->addWeeks($page)->lessThanOrEqualTo(now());

        return collect($response)->map(fn ($item) => new CallResult([
            'id'        => $item['uid'],
            'status'    => $item[$this->statusField] ?? 'undefined',
            'isDeposit' => in_array($item['custom'] ?? '', ['DEPOSITOR', 'deposit', 'depositor', 'Depozit', 'Depositor']),
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url . '/api/ext/add.json?id=' . $this->token, $payload = [
            'id'        => 'auto',
            'offer'     => $this->offer_id,
            'ip'        => $lead->ip,
            'name'      => $lead->firstname,
            'last'      => $lead->lastname,
            'phone'     => $lead->phone,
            'email'     => $lead->getOrGenerateEmail(),
            'country'   => optional($lead->ipAddress)->country_code,
            'comm'      => $lead->hasPoll() ? ($this->answersInText ? $lead->getPollAsText() : $this->answers($lead)) : '',
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * Determine is lead delivered
     *
     * @return boolean
     */
    public function isDelivered(): bool
    {
        return $this->response->status() === 200 && data_get($this->response->json(), 'status') === 'ok';
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

    /**
     * @param Lead $lead
     *
     * @return string
     */
    protected function answers(Lead $lead)
    {
        return $lead->getPollAsUrl();
    }
}
