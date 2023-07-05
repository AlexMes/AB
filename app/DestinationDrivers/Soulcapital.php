<?php

namespace App\DestinationDrivers;

use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Soulcapital implements Contracts\DeliversLeadToDestination, Contracts\CollectsCallResults
{
    private $response;
    protected const SC_TOKEN_CACHE_KEY = 'SOULCAPITAL_';

    private $url = 'https://soulcapitalgroup.com';
    private $token;
    private $login;
    private $password;
    private $countryCode = "RUS";
    private $callCenterId;
    private $affManagerAdminId;
    private $retAdminId  = null;
    private $saleAdminId = null;

    public function __construct($configuration = null)
    {
        $this->url               = $configuration['url'] ?? $this->url;
        $this->token             = $configuration['token'];
        $this->login             = $configuration['login'];
        $this->password          = $configuration['password'];
        $this->countryCode       = $configuration['country_code'] ?? $this->countryCode;
        $this->callCenterId      = $configuration['callCenterId'];
        $this->affManagerAdminId = $configuration['affManagerAdminId'];
        $this->retAdminId        = $configuration['retAdminId'] ?? $this->retAdminId;
        $this->saleAdminId       = $configuration['saleAdminId'] ?? $this->saleAdminId;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $countItem = 200;

        return collect(data_get(Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->retrieveToken()
        ])->get($this->url . '/api/v1/admin/users', [
            'limit' => $countItem,
            'skip'  => $countItem * ($page - 1),
        ])->json(), 'data.items', []))->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['ProcessingStatus']['name'],
            'isDeposit'   => $item['isFirstDeposit'],
            'depositDate' => $item['isFirstDeposit'] ? $item['firstTimeDepositDate'] : null,
            'depositSum'  => $item['isFirstDeposit'] ? $item['firstTimeDepositAmount'] : null,
        ]));
    }

    private function payload($lead): array
    {
        $payload = [
            'email'             => $lead->getOrGenerateEmail(),
            'phone'             => $lead->phone,
            'firstName'         => $lead->firstname,
            'lastName'          => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'country'           => $this->countryCode,
            'ipAddress'         => $lead->ip,
            'callCenterId'      => $this->callCenterId,
            'affManagerAdminId' => $this->affManagerAdminId,
            'retAdminId'        => $this->retAdminId,
            'saleAdminId'       => $this->saleAdminId,
            'utm_source'        => $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'utm_medium'        => '',
            'utm_campaing'      => '',
            'utm_content'       => '',
            'sub_id'            => $lead->uuid,
        ];

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post(
            $this->url . '/api/v1/public/registration?password=' . $this->token,
            $this->payload = $this->payload($lead)
        );

        $lead->addEvent('payload', $this->payload);
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
        return data_get($this->response->json(), 'data.autologinUrl');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.user.id');
    }

    private function retrieveToken()
    {
        if (app('cache')->has(self::SC_TOKEN_CACHE_KEY . $this->login)) {
            return app('cache')->get(self::SC_TOKEN_CACHE_KEY . $this->login);
        }

        $token =  Http::post(
            $this->url . '/api/v1/public/login/admin',
            [
                'email'    => $this->login,
                'password' => $this->password,
            ]
        )->offsetGet('data')['token'];

        return app('cache')
            ->remember(
                self::SC_TOKEN_CACHE_KEY . $this->login,
                now()->addHours(2),
                fn () => $token
            );
    }
}
