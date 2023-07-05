<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MediaPro implements DeliversLeadToDestination
{
    protected $response;

    public function __construct($configuration = null)
    {
        // nothing to do here
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post('https://mediapro.top/api/leads', [
            'phone'        => $lead->phone,
            'full_name'    => $lead->fullname,
            'email'        => $lead->getOrGenerateEmail(),
            'ip'           => $lead->ip,
            'landing'      => $lead->domain,
            'landing_name' => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'source'       => 'XLeads',
            'country'      => optional($lead->ipAddress)->country_code ?? 'RU',
            'user_id'      => 15
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
        return data_get($this->response->json(), 'link_auto_login');
    }

    public function getExternalId(): ?string
    {
        return null;
    }
}
