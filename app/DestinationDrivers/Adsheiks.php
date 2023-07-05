<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class Adsheiks implements DeliversLeadToDestination
{
    protected $response;
    protected $token;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'token' => $this->token,
        ])
            ->post('https://crm.filthyapi.com/api/lead/create', [
                'token'        => $this->token,
                'fname'        => $lead->firstname,
                'lname'        => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'        => $lead->getOrGenerateEmail(),
                'ip'           => $lead->ip,
                'click_id'     => $lead->uuid,
                'referrer_url' => $lead->domain.'/?utm_source=1',
                'phone_code'   => $lead->lookup->prefix,
                'phone'        => substr($lead->phone, strlen($lead->lookup->prefix)),
                'notes'        => $lead->hasPoll() ? $lead->getPollAsUrl() : '',
            ]);

        $lead->addEvent('crm-response', [
            'status' => $this->response->status(),
            'body'   => $this->response->json()
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'statusCode') === 1;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id');
    }
}
