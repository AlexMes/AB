<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Str;

class Payments implements DeliversLeadToDestination
{
    protected $url;
    protected $login;
    protected $password;


    public function __construct($configuration = null)
    {
        $this->url = $configuration['url'];
    }

    public function send(Lead $lead): void
    {
        $this->login    = $lead->getOrGenerateEmail();
        $this->password = Str::random(16);

        $this->response = \Illuminate\Support\Facades\Http::asForm()->post($this->url.'/assets/ajax/registration.php', [
            "name"     => $lead->firstname,
            "surname"  => $lead->lastname,
            "login"    => $this->login,
            "email"    => $this->login,
            "phone"    => $lead->phone,
            "password" => $this->password
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
        return sprintf('%s/autologin.php?login=%s&password=%s', $this->url, $this->login, $this->password);
    }


    public function getExternalId(): ?string
    {
        return null;
    }
}
