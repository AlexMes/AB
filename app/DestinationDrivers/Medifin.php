<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class Medifin implements DeliversLeadToDestination, CollectsCallResults
{
    public const MD_TOKEN_CACHE_KEY = 'MEDIFIN_';
    protected string $url           = 'https://affiliate.medifin.live';
    protected $response;
    protected string $username;
    protected string $password;
    protected $affiliateId;
    protected $ownerId;

    public function __construct($configuration = null)
    {
        $this->username    = $configuration['username'];
        $this->password    = $configuration['password'];
        $this->affiliateId = $configuration['affiliate_id'];
        $this->url         = $configuration['url'] ?? $this->url;
        $this->ownerId     = $configuration['owner_id'] ?? null;
    }

    /**
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(data_get(Http::withHeaders([
            'AuthToken' => $this->retrieveToken(),
        ])
            ->get(
                $this->url . '/api/aff/itemsfor/Accounts',
                [
                    'AffiliateId'                       => $this->affiliateId,
                    'pageIndex'                         => $page,
                    'pageSize'                          => 250,
                    'items.CreatedDate.comparationType' => 1,
                    'items.CreatedDate.value[0]'        => $since->toDateString(),
                    'items.CreatedDate.value[1]'        => now()->toDateString(),
                    'flags.useLabels'                   => 'true',
                ]
            ), 'data'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['Id'],
                    'status'      => $item['StatusId'],
                    'isDeposit'   => $item['HasDeposit'],
                    'depositDate' => Carbon::parse($item['FirstDepositDate'])->toDateString(),
                    'depositSum'  => '151'
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'AuthToken' => $this->retrieveToken(),
        ])
            ->post($this->url . '/api/aff/accounts', $payload = [
                'FirstName'   => $lead->firstname,
                'LastName'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'Email'       => $lead->getOrGenerateEmail(),
                'Country'     => optional($lead->ipAddress)->country_code ?? 'RU',
                'Phone'       => $lead->formatted_phone,
                'AffiliateId' => $this->affiliateId,
                'OwnerId'     => $this->ownerId,
                'Referrer'    => $lead->domain,
                'IpAddress'   => $lead->ip,
                'CampaignId'  => Str::before(optional(optional($lead->offer)->getOriginalCopy())->name, '_'),
            ]);

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
        return data_get($this->response->json(), 'Id');
    }

    /**
     * Pull auth token
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return \Illuminate\Contracts\Cache\Repository|mixed|string
     */
    private function retrieveToken()
    {
        if (app('cache')->has(self::MD_TOKEN_CACHE_KEY . $this->username)) {
            return app('cache')->get(self::MD_TOKEN_CACHE_KEY . $this->username);
        }

        $token =  Http::post(
            $this->url . '/api/affiliate/generateauthtoken',
            [
                'userName' => $this->username,
                'password' => $this->password,
            ]
        )->offsetGet('token');

        return app('cache')
            ->remember(
                self::MD_TOKEN_CACHE_KEY . $this->username,
                now()->addMinutes(15),
                fn () => $token
            );
    }
}
