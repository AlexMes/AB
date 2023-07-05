<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SensumGlobal implements DeliversLeadToDestination, CollectsCallResults
{
    protected const SEX = [
        1 => 'male',
        2 => 'female',
    ];

    protected const SG_TOKEN_CACHE_KEY = 'SENSUMGLOBALTOKEN';
    protected string $url              = 'https://back.sensumglobal.crmjoker.com';
    protected $login;
    protected $password;
    protected $languageId;
    protected $countryId;
    protected $source;

    public function __construct($configuration = null)
    {
        $this->url       = $configuration['url'] ?? $this->url;
        $this->login     = $configuration['login'];
        $this->password  = $configuration['password'];
        $this->language  = $configuration['language_id'] ?? 133; //RU
        $this->countryId = $configuration['country_id'] ?? 643; //RU
        $this->source    = $configuration['source'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(data_get(Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
        ])->post($this->url . '/partners/api/v1/clients', [
            'registrationDateFrom' => $since->toDateString(),
            'registrationDateTo'   => now()->addDay()->toDateString(),
            'page'                 => $page,
            'per_page'             => 100
        ])->json(), 'data'))->map(fn ($item) => new CallResult([
            'id'          => $item['client_uuid'],
            'status'      => $item['salesStatus'],
            'isDeposit'   => $item['firstDeposit'],
            'depositDate' => $item['firstDeposit'] ? Carbon::parse($item['firstDepositDate'])->toDateString() : null,
        ]));
    }

    public function payload(Lead $lead): array
    {
        $payload = [
            'email'      => $lead->getOrGenerateEmail(),
            'phone'      => $lead->phone,
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'password'   => 'ChangeMe1234#',
            'language'   => $this->languageId,
            'gender'     => array_key_exists($lead->gender_id, self::SEX) ? self::SEX[$lead->gender_id] : 'Unknown',
            'country_id' => $this->countryId,
            'city'       => optional($lead->ipAddress)->city,
            'source'     => $this->source ?? $lead->offer->name,
            'referral'   => $lead->domain
        ];

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->withHeaders([
            'Authorization' => 'Bearer ' . $this->getToken(),
        ])->post(
            $this->url . '/partners/api/v1/client/create',
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
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'uuid');
    }

    private function getToken()
    {
        if (app('cache')->has(self::SG_TOKEN_CACHE_KEY . $this->login)) {
            return app('cache')->get(self::SG_TOKEN_CACHE_KEY . $this->login);
        }

        $token =  Http::post(
            $this->url . '/partners/api/v1/login',
            [
                'email'    => $this->login,
                'password' => $this->password,
            ]
        )->offsetGet('token');
        ;

        return app('cache')
            ->remember(
                self::SG_TOKEN_CACHE_KEY . $this->login,
                now()->addMinutes(30),
                fn () => $token
            );
    }
}
