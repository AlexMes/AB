<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Optimus implements DeliversLeadToDestination, CollectsCallResults
{
    private const OPTIMUS_TOKEN_CACHE_KEY = 'OPTIMUS_';
    protected $response;

    protected string $url = 'https://optimus-api.io';
    protected $login;
    protected $password;
    protected $country;
    protected $funnel;
    protected $affId;
    protected $source;

    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'] ?? $this->url;
        $this->login    = $configuration['login'];
        $this->password = $configuration['password'];
        $this->affId    = $configuration['aff_id'];
        $this->country  = $configuration['country'] ?? null;
        $this->funnel   = $configuration['funnel'] ?? null;
        $this->source   = $configuration['source'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(data_get(Http::withHeaders([
            'TraffleAuthorization' => 'TraffleBearer ' . $this->authToken(),
        ])->post($this->url . '/leads', [
            "filter" => [
                'from' => $since->addWeeks($page - 1)->toDateTimeString(),
                'to'   => $since->addWeeks($page)->toDateTimeString(),
            ]
        ])->json(), 'data'))->map(function ($item) {
            return new CallResult([
                'id'     => $item['user_id'],
                'status' => $item['saleStatus'],
            ]);
        });
    }

    public function payload(Lead $lead)
    {
        return [
            'tracking' => [
                'aff_id'     => $this->affId,
                'funnelname' => $this->funnel ?? $lead->offer->name,
                'source'     => $this->source,
            ],
            'lead' => [
                'fname'   => $lead->firstname,
                'lname'   => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'   => $lead->getOrGenerateEmail(),
                'phone'   => sprintf("+%s", $lead->formatted_phone),
                'country' => optional($lead->ipAddress)->country_code ?? $this->country,
            ]
        ];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'TraffleAuthorization' => 'TraffleBearer ' . $this->authToken(),
        ])->post($this->url . '/createlead', $payload = $this->payload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->getExternalId() !== null;
    }

    public function getError(): ?string
    {
        return data_get($this->response->json(), 'message', $this->response->body());
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'data.autologin');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.user_id');
    }

    public function collectFtdSinceDate(Carbon $startDate)
    {
        return data_get(Http::withHeaders([
            'TraffleAuthorization' => 'TraffleBearer ' . $this->authToken(),
            'Content-Type'         => 'application/json',
        ])->post($this->url . '/deposits', [
            "filter" => [
                'from' => $startDate->toDateTimeString(),
                'to'   => now()->toDateTimeString(),
            ]
        ])->json(), 'data');
    }

    private function authToken()
    {
        return cache()->remember(
            self::OPTIMUS_TOKEN_CACHE_KEY . $this->login,
            now()->addMinutes(4),
            function () {
                return data_get(Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->login . ":" . $this->password),
                ])->post($this->url . '/introduceyourself/login')->json(), 'token');
            }
        );
    }
}
