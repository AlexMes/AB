<?php

namespace App\DestinationDrivers;

use App\AdsBoard;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CryptoCenter implements DeliversLeadToDestination
{
    protected $response;
    protected $token;
    protected $campaign;


    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'];
        $this->token    = $configuration['token'];
        $this->campaign = $configuration['campaign'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post($this->url.'/api/users/trading-platform/create-client?api_key='.$this->token, [
            'country'       => $lead->lookup->country,
            'currency_code' => 'USD',
            'email'         => $lead->getOrGenerateEmail(),
            'first_name'    => $lead->firstname,
            'last_name'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'         => $lead->phone,
            'gmt_timezone'  => '04:00',
            'password'      => Str::random(12),
            'campaign_id'   => $this->campaign,
            'free_text'     => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'note'          => Str::before($lead->offer->getOriginalCopy()->name, '_'),
        ]);
        AdsBoard::report($this->response->body());
    }

    public function isDelivered(): bool
    {
        return $this->response->ok();
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get(Http::post($this->url.'/api/users/erp/login-link?api_key='.$this->token, [
            'user_id' => $this->getExternalId()
        ])->json(), 'link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'client_id');
    }
}
