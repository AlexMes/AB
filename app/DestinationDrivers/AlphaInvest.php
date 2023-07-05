<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class AlphaInvest implements DeliversLeadToDestination
{
    protected string $url = 'https://partner.alpha-invest.uno';
    protected $affiliateId;
    protected $source;

    public function __construct($configuration = null)
    {
        $this->url          = $configuration['url'] ?? $this->url;
        $this->source       = $configuration['source'];
        $this->affiliateId  = $configuration['affiliate_id'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post($this->url . '/api/index.php', $payload = [
            'affiliate_id' => $this->affiliateId,
            'domain'       => 'https://' . $lead->domain,
            'firstname'    => $lead->firstname,
            'lastname'     => $lead->lastname,
            'email'        => $lead->getOrGenerateEmail(),
            'phone'        => '+' . $lead->phone,
            'source'       => $this->source,
            'ip'           => $lead->ip,
            'message'      => $lead->getPollAsText(),
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful()
            && data_get($this->response->json(), 'msg') === 'Record Insert Sucessfully';
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
