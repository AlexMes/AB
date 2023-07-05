<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Arion implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://api.capitalprotrade.com';
    protected $token;
    protected $response;
    protected $offerName;

    public function __construct($configuration = null)
    {
        $this->url       = $configuration['url'] ?? $this->url;
        $this->token     = $configuration['token'] ?? null;
        $this->offerName = $configuration['offer_name'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        //For save statuses for destination before adding pullResults
        if ($since->toDateString() < '2023-01-01') {
            return collect();
        }

        $since = $since->toImmutable();

        return collect(data_get(Http::withHeaders([
            'Authorization' => $this->token,
        ])->get($this->url . '/affiliates/leads', [
            'from' => $since->addWeeks($page - 1)->startOfDay()->timestamp,
            'to'   => $since->addWeeks($page)->startOfDay()->timestamp,
        ])->json(), 'data'))
            ->map(fn ($item) => new CallResult([
                'id'        => $item['id'],
                'status'    => $item['status'],
                'isDeposit' => $item['deposited'] ?? false,
            ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Authorization' => $this->token
        ])->post(sprintf('%s/affiliates/leads', $this->url), $payload = [
            'leads' => [
                [
                    'email'        => $lead->getOrGenerateEmail(),
                    'phone'        => $lead->phone,
                    'firstname'    => $lead->firstname,
                    'lastname'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                    'fullname'     => $lead->fullname,
                    'country'      => $lead->ipAddress->country_name,
                    'utm_campaign' => $lead->uuid,
                    'comment'      => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() . 'PHP_EOL' . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '',
                    'info'         => $this->offerName ?? $lead->offer->name,
                    'ip'           => $lead->ip,
                ]
            ]
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') === 200
            && data_get($this->response->json(), 'data.invalidCnt') == 0
            && data_get($this->response->json(), 'data.duplicateEmailsCnt') == 0
            && data_get($this->response->json(), 'data.duplicatePhonesCnt') == 0;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'data.valid.0.login_url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.valid.0.id');
    }
}
