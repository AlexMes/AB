<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class VoipPlus implements DeliversLeadToDestination
{
    protected $url;
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'];
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post($this->url, [
            'name'  => $lead->fullname,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'token' => $this->token,
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
