<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use App\Trail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Finutrade implements DeliversLeadToDestination
{
    protected $token;
    protected $campaign;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token    = $configuration['token'];
        $this->campaign = $configuration['campaign'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://crm.finutrade.com/api/users/trading-platform/create-client?api_key='.$this->token, [
            'country'       => $lead->ipAddress->country_name,
            'campaign_id'   => $this->campaign,
            'currency_code' => 'USD',
            'email'         => $lead->getOrGenerateEmail(),
            'first_name'    => $lead->firstname,
            'last_name'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'         => $lead->phone,
            'comment'       => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'gmt_timezone'  => '02:00',
            'password'      => 'ChangeMe123!',
            'lang'          => 'RU',
            'note'          => $lead->hasPoll()
            ? $lead->pollResults()
                ->map(fn (PoolAnswer $p) => $p->getQuestion().' => '. $p->getAnswer())
                ->implode(PHP_EOL) : '',
        ]);

        app(Trail::class)->add('Registration reponse is '.$this->response->body());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->getExternalId() !== null ;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get(Http::post('https://crm.finutrade.com/api/users/erp/login-link?api_key='.$this->token, [
            'user_id' => $this->getExternalId(),
        ])->json(), 'link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'client_id');
    }
}
