<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class JagCrm implements DeliversLeadToDestination
{
    protected $login;
    protected $password;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->login    = $configuration['login'];
        $this->password = $configuration['password'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()->withHeaders([
            'login'    => $this->login,
            'password' => $this->password,
        ])->post('https://jagcrm.com/Api/affiliate/V1/clients', [
            'firstName' => $lead->firstname,
            'lastName'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'country'   => $lead->lookup->country,
            'email'     => $lead->getOrGenerateEmail(),
            'password'  => 'Af3'.Str::random(6),
            'phone'     => $lead->phone,
            'source'    => $lead->domain,
            'ip'        => $lead->ip,
            'language'  => 'ru',
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->ok() && data_get($this->response->json(), 'profileUUID') !== null;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'redirectUrl.link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'profileUUID') ;
    }
}
