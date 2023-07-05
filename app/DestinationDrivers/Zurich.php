<?php

namespace App\DestinationDrivers;

use App\Lead;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Zurich implements Contracts\DeliversLeadToDestination
{
    protected string $baseUrl = 'https://partner.zurichtradefinco.com';
    protected string $username;
    protected string $password;
    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $externalId = null;

    /**
     * @inheritDoc
     */
    public function __construct($configuration = null)
    {
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
    }

    /**
     * @inheritDoc
     */
    public function send(Lead $lead): void
    {
        $response = Http::withHeaders([
            'login'    => $this->username,
            'password' => $this->password
        ])->post(sprintf("%s/clients", $this->baseUrl), [
            'firstName' => $lead->firstname,
            'lastName'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'     => $lead->formatted_phone,
            'country'   => optional($lead->ipAddress)->country_code ?? 'RU',
            'email'     => $lead->getOrGenerateEmail(),
            'password'  => sprintf("%s%s", Str::random(12), rand(10, 999)),
            'ip'        => $lead->ip,
            'currency'  => 'USD',
            'language'  => 'ru',
        ]);

        if ($response->serverError() || $response->clientError()) {
            $this->error = $response->body();

            return;
        }

        $this->externalId = $response->offsetGet('profileUUID');

        $this->isSuccessful = true;
    }

    /**
     * @inheritDoc
     */
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * Collect deposits from destination
     *
     * @param \Illuminate\Support\Carbon $since
     *
     * @return array
     */
    public function pullFtd(Carbon $since)
    {
        return Http::withHeaders([
            'login'    => $this->username,
            'password' => $this->password
        ])->get(sprintf("%s/clients", $this->baseUrl), [
            'registrationDateFrom' => $since->toDateString(),
            'registrationDateTo'   => now()->toDateString(),
            'firstDeposit'         => true,
        ])->json();
    }
}
