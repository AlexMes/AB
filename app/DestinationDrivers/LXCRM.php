<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class LXCRM implements DeliversLeadToDestination, CollectsCallResults
{
    protected const LXCRM_TOKEN_CACHE_KEY = 'LXCRM_TOKEN_';
    protected string $url                 = 'https://affiliate365.tradingcrm.com:4477';
    protected $login;
    protected $password;
    protected $affiliateId;
    protected $country;
    protected $subAffiliate;
    protected $campaignId;
    protected $tag;
    protected $tagOne;
    protected $language;

    protected $response;

    public function __construct($configuration = null)
    {
        $this->url          = $configuration['url'] ?? $this->url;
        $this->login        = $configuration['login'];
        $this->password     = $configuration['password'];
        $this->affiliateId  = $configuration['affiliate_id'] ?? null;
        $this->country      = $configuration['iso_country'] ?? null;
        $this->subAffiliate = $configuration['sub_affiliate'] ?? null;
        $this->campaignId   = $configuration['campaign_id'] ?? null;
        $this->tag          = $configuration['tag'] ?? null;
        $this->tagOne       = $configuration['tag1'] ?? null;
        $this->language     = $configuration['language'] ?? "RU";
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Api-Version'   => '4.0'
        ])->get($this->url . '/accounts', [
            'WHERE[createdOn][min]' => $since->toDateString(),
            'WHERE[createdOn][max]' => now()->addDay()->toDateString(),
            'LIMIT[Skip]'           => (500 * ($page - 1)) + 1,
            'LIMIT[Take]'           => 500,
        ])->json())->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['leadStatus'],
            'isDeposit'   => $item['ftdExists'],
        ]));
    }

    public function payload(Lead $lead): array
    {
        $payload = [
            'firstName'              => $lead->firstname,
            'lastName'               => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'                  => $lead->getOrGenerateEmail(),
            'phone'                  => $lead->phone,
            'affiliateTransactionId' => $this->affiliateId,
            'isoCountry'             => $this->country ?? optional($lead->ipAddress)->country_code ?? 'RU',
            'subAffiliate'           => $this->subAffiliate,
            'campaignId'             => $this->campaignId,
            'tag'                    => $this->tag,
            'tag1'                   => $this->tagOne,
            'language'               => $this->language,
            'ip'                     => $lead->ip,
            'password'               => 'ChangeMe1234',
        ];

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Api-Version'   => '4.0'
        ])->post(
            $this->url . '/accounts/registrationwithsso',
            $payload = $this->payload($lead)
        );

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
        return data_get($this->response->json(), 'url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'accountId');
    }

    private function getToken()
    {
        if (app('cache')->has(self::LXCRM_TOKEN_CACHE_KEY . $this->login)) {
            return app('cache')->get(self::LXCRM_TOKEN_CACHE_KEY . $this->login);
        }

        $token =  Http::post(
            $this->url . '/token',
            [
                'userName' => $this->login,
                'password' => $this->password,
            ]
        )->offsetGet('Token');

        return app('cache')
            ->remember(
                self::LXCRM_TOKEN_CACHE_KEY . $this->login,
                now()->addMinutes(30),
                fn () => $token
            );
    }
}
