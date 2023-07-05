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

class PlanetInv implements DeliversLeadToDestination, CollectsCallResults
{
    protected $username;
    protected $password;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
    }


    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withHeaders([
            'AuthToken' => $this->getToken(),
        ])->get('https://affiliate.planetinv.com/api/aff/itemsfor/Leads?flags.useLabels=true&pageSize=500&pageIndex='.$page)
            ->throw()->offsetGet('data'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['Id'],
                    'status'      => $item['StatusId'],
                    'isDeposit'   => false,
                    'depositDate' => null,
                    'depositSum'  => '151',
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'AuthToken' => $this->getToken(),
        ])->post('https://affiliate.planetinv.com/api/aff/accounts', [
            'FirstName'   => $lead->firstname,
            'LastName'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'Phone'       => $lead->phone,
            'Email'       => $lead->getOrGenerateEmail(),
            'Country'     => optional($lead->ipAddress)->country_code ?? 'RU',
            'AffiliateId' => 'MandarinRU',
            'OwnerId'     => 506,
            'CampaignId'  => Str::contains($lead->offer->name, 'GAZ') ? 'Gazprom' : 'Tinkoff',
            'Description' => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().'-> '.$question->getAnswer())->implode('|') : ''
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful();
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
        return data_get($this->response->json(), 'Id', null);
    }

    /**
     * Get auth token for the integration
     *
     * @return string
     */
    protected function getToken():string
    {
        return Http::post('https://affiliate.planetinv.com/api/affiliate/generateauthtoken', [
            'userName' => $this->username,
            'password' => $this->password
        ])->offsetGet('token');
    }
}
