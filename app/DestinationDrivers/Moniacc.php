<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class Moniacc implements DeliversLeadToDestination
{
    protected $response;
    protected $offer;
    protected $campaign;


    public function __construct($configuration = null)
    {
        $this->offer    = $configuration['offer'];
        $this->campaign = $configuration['campaign'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://track.moniacc.com', [
            'firstname'        => $lead->firstname,
            'lastname'         => $lead->lastname,
            'email'            => $lead->getOrGenerateEmail(),
            'phone'            => $lead->phone,
            'offer_name'       => $this->offer,
            'campaign_id'      => $this->campaign,
            'sub1'             => $lead->uuid,
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'success');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'result.url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'result.id');
    }
}
