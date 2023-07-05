<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GlobalWise implements DeliversLeadToDestination
{
    public const GW_TOKEN_CACHE_KEY = 'gw-auth-token';
    protected string $url           = 'www.united-asset-finance.com';
    protected string $affiliateId;
    protected int $ownerId;
    private string $username;
    private string $password;
    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $externalId = null;

    /**
     * GlobalWise constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        $this->affiliateId = $configuration['affiliate_id'];
        $this->ownerId     = $configuration['owner_id'];
        $this->username    = $configuration['username'];
        $this->password    = $configuration['password'];
    }

    /**
     * @param \App\Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Throwable
     */
    public function send(Lead $lead): void
    {
        $response = Http::post(sprintf('%s/Account/RegisterApiAccount', $this->url), [
            'FirstName'   => $lead->firstname,
            'LastName'    => $lead->lastname ?? $lead->middlename ?? 'none',
            'Email'       => $lead->email ?? Str::slug($lead->fullname, '') . rand(1, 99) . '@gmail.com',
            'Country'     => optional($lead->ipAddress)->country_code ?? 'RU',
            'Phone'       => $lead->formatted_phone,
            'AffiliateId' => $this->affiliateId,
            'OwnerId'     => $this->ownerId,
            'Referrer'    => $lead->domain,
            'IpAddress'   => $lead->ip,
            'IpCountry'   => optional($lead->ipAddress)->country_code ?? 'RU',
            'CampaignId'  => Str::before(optional(optional($lead->offer)->getOriginalCopy())->name, '_'),
        ])->throw()->json();

        $this->isSuccessful = $response['IsSuccessed'];

        if (!$this->isSuccessful) {
            $this->error = $response['ErrorMessage'];
        } else {
            $this->externalId = $response['AccountId'];
        }
    }

    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return array|mixed
     */
    public function collectFtdForPeriod(Carbon $since, Carbon $until)
    {
        return Http::withHeaders([
            'AuthToken' => $this->retrieveToken()
        ])
            ->get(
                sprintf(
                    '%s/Affiliate/api/home/itemsfor/Accounts?',
                    $this->getAffiliateEndpoint()
                ),
                [
                    'pageIndex'                          => 1,
                    'pageSize'                           => 250,
                    'items.CreatedDate.comparationType'  => 1,
                    'items.CreatedDate.value[0]'         => $since->format('m/d/Y'),
                    'items.CreatedDate.value[1]'         => $until->format('m/d/Y'),
                    'items.HasDeposit.comparationType'   => 1,
                    'items.HasDeposit.value[0]'          => true,
                ]
            )->throw()->json()['data'];
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
        if (app('cache')->has(self::GW_TOKEN_CACHE_KEY)) {
            return app('cache')->get(self::GW_TOKEN_CACHE_KEY);
        }

        $response =  Http::post(
            sprintf(
                '%s/api/affiliate/generateauthtoken',
                $this->getAffiliateEndpoint()
            ),
            [
                'userName' => $this->username,
                'password' => $this->password,
            ]
        )->throw()->json();

        return app('cache')
            ->remember(
                self::GW_TOKEN_CACHE_KEY,
                now()->addMinutes(15),
                fn () => $response['token']
            );
    }

    /**
     * @return string
     */
    private function getAffiliateEndpoint(): string
    {
        return Str::replaceFirst('www', 'affiliate', $this->url);
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
