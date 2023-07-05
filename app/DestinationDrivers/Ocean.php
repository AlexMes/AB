<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Ocean implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'];
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Authorization' => $this->token,
        ])
            ->post($this->url.'/affiliates/leads', $payload = [
                'email'        => $lead->getOrGenerateEmail(),
                'phone'        => '+'.$lead->phone,
                'firstname'    => $lead->firstname,
                'lastname'     => $lead->lastname,
                'fullname'     => $lead->fullname,
                'country'      => $lead->ipAddress->country_name,
                'utm_campaign' => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'info'         => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() . PHP_EOL . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '',
            ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') === 200;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'data.valid')[0]['login_url'];
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.valid')[0]['id'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withHeaders([
            'Authorization' => $this->token,
        ])->get($this->url.'/affiliates/leads', [
            'page' => $page,
            'from' => $since->startOfDay()->timestamp,
        ])->json())->map(fn ($item) => new CallResult([
            'id'        => $item['id'],
            'status'    => $item['status'],
            'isDeposit' => $item['deposited'] ?? false,
        ]));
    }
}
