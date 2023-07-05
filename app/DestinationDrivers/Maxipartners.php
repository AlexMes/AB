<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Str;

class Maxipartners implements DeliversLeadToDestination
{
    protected $username;
    protected $password;
    protected $campaignId;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->username   = $configuration['username'];
        $this->password   = $configuration['password'];
        $this->campaignId = $configuration['campaign'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post('https://affapi.umarkets.biz', [
            'api_username'        => $this->username,
            'api_password'        => $this->password,
            'MODULE'              => 'Customer',
            'command'             => 'Add',
            'jsonResponse'        => true,
            'campaignId'          => $this->campaignId,
            'Phone'               => $lead->formatted_phone,
            'FirstName'           => $lead->firstname,
            'LastName'            => $lead->lastname ?? 'Unknown',
            'email'               => $lead->getOrGenerateEmail(),
            'currency'            => 'USD',
            'password'            => Str::random(10).rand(10, 99),
            'businessUnitName'    => 'Umarkets-Russian-Dnepr-Partners',
            'countryISO'          => 'RU',
        ]);
    }

    public function isDelivered(): bool
    {
        if ($this->response->successful()) {
            if ($this->response->offsetExists('status')) {
                return $this->response->json()['status']['operation_status'] === 'successful';
            }
        }

        return false;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        if ($this->isDelivered()) {
            return $this->response->json()['status']['Customer']['data_authKey'];
        }

        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->isDelivered()) {
            return 'https://www.umarkets.biz/ru/my-account/?amstoken='.$this->response->json()['status']['Customer']['data_crm_id'];
        }

        return null;
    }
}
