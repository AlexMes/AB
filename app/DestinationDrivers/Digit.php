<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Digit implements DeliversLeadToDestination
{
    protected $affId;
    protected $token;
    protected $funnel;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->affId  = $configuration['aff'];
        $this->token  = $configuration['token'];
        $this->funnel = $configuration['funnel'] ?? null;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://digitapi.net/leads', $payload = [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'password'   => 'ChangeMe123',
            'email'      => $lead->getOrGenerateEmail(),
            'funnel'     => $this->funnel ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'affid'      => $this->affId,
            'area_code'  => $lead->lookup->prefix,
            'phone'      => substr($lead->phone, strlen($lead->lookup->prefix)),
            '_ip'        => $lead->ip,
            'hitid'      => $lead->uuid ,
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'lead.id') !== null;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'extras.url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead.id');
    }
}
