<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Services\MessageBird\MessageBird;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class PremiumFinsol implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://affiliate.premiumfinsol-crm.com/api';
    protected ?string $affiliateId;
    protected ?int $ownerId;
    protected ?string $token;
    protected ?string $username;
    protected ?string $password;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $link       = null;
    protected ?string $externalId = null;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->affiliateId = $configuration['AffiliateId'] ?? 'Office2_ru';
        $this->ownerId     = $configuration['OwnerId'] ?? 499;
        $this->username    = $configuration['username'] ?? 'Office2_ru';
        $this->password    = $configuration['password'] ?? '321c132c1cDCSA';
        $this->token       = Cache::remember(
            'premium-finsol-access-token',
            now()->addMinutes(30),
            fn () => $this->generateToken($this->username, $this->password)
        );
    }

    /**
     * Collect results from the API
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::withHeaders([
            'AuthToken'     => $this->token,
            'content-type'  => 'application/json',
            //        ])->get(sprintf('%s/aff/itemsfor/Leads', $this->url), [
        ])->get(sprintf('%s/aff/itemsfor/Accounts', $this->url), [
            'AffiliateId'             => $this->affiliateId,
            'OwnerId'                 => $this->ownerId,
            'FirstRegistrationDate'   => $since->addWeeks($page - 1)->format('YYYY-MM-DD'),
            'pageIndex'               => $page,
            'pageSize'                => 500,
        ])
            ->throw()->offsetGet('data'))->map(fn ($item) => new CallResult([
                'id'          => $item['Id'],
                'status'      => $item['StatusId'],
                'isDeposit'   => $item['HasDeposit'],
                'depositDate' => Carbon::parse($item['FirstDepositDate'])->toDateString(),
                'depositSum'  => '151',
            ]));
    }

    /**
     * @param Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $code        = $this->getPhoneCode($lead->formatted_phone);
        $countryCode = $lead->ipAddress->country_code;

        if ($code === null && $countryCode === null) {
            return;
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withHeaders([
                'AuthToken'     => $this->token,
                'content-type'  => 'application/json',
            ])
//            ->post(sprintf('%s/aff/leads', $this->url), [
            ->post(sprintf('%s/aff/accounts', $this->url), [
                'AffiliateId'   => $this->affiliateId,
                'FirstName'     => $lead->firstname,
                'LastName'      => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'Country'       => $countryCode,
                'Email'         => $lead->getOrGenerateEmail(),
                'OwnerId'       => $this->ownerId,
                'CampaignId'    => $lead->domain,
                'Phone'         => substr($lead->formatted_phone, strlen($code)),
                'IpAddress'     => $lead->ip,
            ]);

        if ($response->successful()) {
            $this->isSuccessful = true;
            $this->externalId   = $response['Id'];
        } else {
            $this->isSuccessful = false;
            $this->error        = $response->body();
        }
    }

    /**
     * @param \Carbon\Carbon $startDate
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectFtdSinceDate(Carbon $startDate)
    {
        return Http::withHeaders([
            'AuthToken' => $this->token
        ])->get(sprintf('%s/aff/itemsfor/Deposits', $this->url), [
            'Date'      => $startDate->subWeek()->format('YYYY-MM-DD'),
            'pageIndex' => 1,
            'pageSize'  => 1000,
        ])->throw()->json();
    }

    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->link;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    protected function getPhoneCode(string $phone)
    {
        $service = new MessageBird(config('services.messagebird.key'));

        try {
            $result = $service->lookup($phone);

            return $result['countryPrefix'] ?? null;
        } catch (Throwable $exception) {
            $this->error        = $exception->getMessage();
            $this->isSuccessful = false;

            return null;
        }
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return mixed
     */
    protected function generateToken(string $username, string $password)
    {
        $response = Http::asJson()
            ->post(sprintf('%s/affiliate/generateauthtoken', $this->url), [
                'userName' => $username,
                'password' => $password,
            ]);

        return $response->json()['token'];
    }
}
