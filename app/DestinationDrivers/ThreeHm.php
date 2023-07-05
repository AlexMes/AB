<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class ThreeHm implements DeliversLeadToDestination
{
    protected $campaign;
    protected $page;
    protected $source;
    protected $url;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->campaign = $configuration['campaign'];
        $this->page     = $configuration['page'];
        $this->source   = $configuration['source'];
        $this->url      = $configuration['url'] ?? null;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()
            ->acceptJson()
            ->post('https://3head-media.com/api/lead', $payload = [
                'first_name' => $lead->firstname,
                'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'campaign'   => $this->campaign,
                'email'      => $lead->getOrGenerateEmail(),
                'phone'      => $lead->phone,
                'landing'    => $this->url ?? 'https://'.$lead->domain.'?utm_source=t',
                'country'    => $lead->ipAddress->country_code,
                'vertical'   => 'Forex',
                'page'       => $this->page,
                'source'     => $this->source,
                'ip'         => $lead->ip,
            ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'Status') === 'Success';
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'Link');
    }

    public function getExternalId(): ?string
    {
        return null;
    }
}
