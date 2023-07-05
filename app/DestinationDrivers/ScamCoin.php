<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ScamCoin implements DeliversLeadToDestination, CollectsCallResults
{
    protected $id;
    protected $token;
    protected $response;
    protected $flow;
    protected $offer;
    public bool $nullInterval;

    public function __construct($configuration = null)
    {
        $this->id    = $configuration['id'];
        $this->token = $configuration['token'];
        $this->flow  = $configuration['flow'];
        $this->offer = $configuration['offer'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $response = Http::post('https://scamcoin.me/api/wm/lead.json?id=' . sprintf('%s-%s', $this->id, $this->token), [
            'offer' => $this->offer,
            'flow'  => $this->flow,
            'day'   => $since->addDays($page - 1)->toDateTimeString(),
        ])->json();

        $this->nullInterval = empty($response) && $since->addDays($page)->lessThanOrEqualTo(now());

        return collect($response)->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['stage']
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://scamcoin.me/api/wm/push.json?id='.sprintf('%s-%s', $this->id, $this->token), $payload = [
            'flow'  => $this->flow,
            'offer' => $this->offer,
            'ip'    => $lead->ip,
            'so'    => $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'name'  => $lead->fullname,
            'phone' => $lead->phone,
            'email' => $lead->getOrGenerateEmail(),
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', $this->response->json());
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
        return data_get($this->response->json(), 'id');
    }
}
