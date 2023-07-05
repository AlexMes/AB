<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Str;

class MediaPunks implements DeliversLeadToDestination
{
    protected $token;
    protected $id;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
        $this->id    = $configuration['id'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://api.meapi.net/leads', [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname,
            'password'   => ucfirst(Str::random(4)).rand(10, 99),
            'email'      => $lead->getOrGenerateEmail(),
            'funnel'     => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'affid'      => $this->id,
            'area_code'  => $lead->lookup->prefix,
            'phone'      => substr($lead->phone, strlen($lead->lookup->prefix)),
            '_ip'        => $lead->ip,
            'hitid'      => $lead->uuid,
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
        return null;
    }
}
