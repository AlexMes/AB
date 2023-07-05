<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Histmo implements DeliversLeadToDestination
{
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::timeout(5)->post('https://histmo.com/api/v1/users', [
            'apiKey'       => $this->token,
            'name'         => $lead->firstname,
            'surname'      => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'        => $lead->getOrGenerateEmail(),
            'password'     => 'SaW'.Str::random(4).rand(10, 99),
            'phone_number' => $lead->phone,
            'country'      => $lead->lookup->country,
            'language'     => 'ru',
            'metrics'      => $lead->hasPoll() ? $lead->getPollAsUrl() : $lead->domain,
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') === 'success';
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'message.url_autologin');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'message.id');
    }
}
