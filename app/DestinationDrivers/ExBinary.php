<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class ExBinary implements DeliversLeadToDestination
{
    protected $response;
    protected $token;
    protected $email;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->email = $lead->getOrGenerateEmail();

        $this->response = Http::post('https://fxlead.exbinarybiz.com/api/v1/lead/create?api_key='.$this->token, [
            'name'  => $lead->fullname,
            'email' => $this->email,
            'phone' => $lead->phone,
            'ip'    => $lead->ip,
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->response->offsetGet('result');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return sprintf('https://static.olymptrade.com/lands/cpa-form-en/?noapp=&ref=cpa_kf_1952264&traffic=1&utm_campaign=fxlead&utm_content=&utm_medium=cpa&utm_source=1952264&utm_term=1952264&lead=%s', $this->email);
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead.id', null);
    }
}
