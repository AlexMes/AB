<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class DozaTeam implements DeliversLeadToDestination, CollectsCallResults
{
    protected $offer;
    protected $token;

    public function __construct($configuration = null)
    {
        $this->offer = $configuration['offer'];
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://dozateam.com/api/ext/add.json?id='.$this->token, $payload = [
            'id'    => 'auto',
            'offer' => $this->offer,
            'ip'    => $lead->ip,
            'name'  => $lead->firstname,
            'last'  => $lead->lastname,
            'phone' => $lead->phone,
            'email' => $lead->getOrGenerateEmail(),
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') === 'ok';
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
        return data_get($this->response->json(), 'uid');
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::post('https://dozateam.com/api/ext/list.json?id='.$this->token, [
            'from' => $since->addWeeks($page - 1)->toDateString(),
            'to'   => $since->addWeeks($page)->toDateString(),
        ])->json())->map(fn ($item) => new CallResult([
            'id'     => $item['uid'],
            'status' => $item['stage']
        ]));
    }
}
