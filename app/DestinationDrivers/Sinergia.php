<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Sinergia implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url = 'https://sinergia-api.net';
    protected $custom;
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url       = $configuration['url'] ?? $this->url;
        $this->token     = $configuration['token'];
        $this->custom[1] = $configuration['custom1'] ?? null;
        $this->custom[2] = $configuration['custom2'] ?? null;
        $this->custom[3] = $configuration['custom3'] ?? null;
        $this->custom[4] = $configuration['custom4'] ?? null;
        $this->custom[5] = $configuration['custom5'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withHeaders([
            'Api-Key' => $this->token
        ])->get($this->url . '/api/v2/leads', [
            'fromDate' => $since->toDateTimeString(),
            'toDate'   => now()->addDay()->toDateTimeString(),
            'page'     => $page
        ])->offsetGet('items'))->map(fn ($item) => new CallResult([
            'id'          => $item['leadRequestIDEncoded'],
            'status'      => $item['saleStatus'],
            'isDeposit'   => $item['hasFTD'],
            'depositDate' => $item['FTDdate'] ?? now()->toDateString(),
            'depositSum'  => $item['depositAmount'] ?? null
        ]));
    }

    public function payload(Lead $lead)
    {
        $payload = [
            'email'        => $lead->getOrGenerateEmail(),
            'firstName'    => $lead->firstname,
            'lastName'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'password'     => Str::random(8) . rand(10, 99) . 'Si',
            'phone'        => $lead->phone,
            'ip'           => $lead->ip,
            'comment'      => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $a) => $a->getQuestion().' => '.$a->getAnswer())->implode(PHP_EOL) : null,
            'offerName'    => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'offerWebsite' => $lead->domain . '?utm_source=t',
        ];

        foreach ($this->custom as $key => $item) {
            if ($item !== null) {
                $payload['custom' . $key] = $item;
            }
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->withHeaders([
            'Api-Key' => $this->token
        ])->post($this->url . '/api/v2/leads', $payload = $this->payload($lead));

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
        return data_get($this->response->json(), 'details.redirect.url') ?? null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'details.leadRequest.ID');
    }
}
