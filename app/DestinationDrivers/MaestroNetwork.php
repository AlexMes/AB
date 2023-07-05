<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class MaestroNetwork implements DeliversLeadToDestination
{
    protected $owner;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->owner = $configuration['owner'] ?? null;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post('https://maestro-network.com/api/save_lead.php', [
            "pass"       => 'xoi082isatyxm',
            "first_name" => $lead->firstname,
            "last_name"  => $lead->lastname ?? 'Unknown',
            "phone"      => $lead->phone,
            "email"      => $lead->getOrGenerateEmail(),
            "country"    => 'RU',
            "offer"      => 'GAZ',
            "owner"      => 'max.pibkin43@gmail.com',
            "ip"         => $lead->ip,
            "subid1"     => '',
            "subid2"     => '',
            "subid3"     => '',
            "subid4"     => '',
            "subid5"     => '',
            "pay_id"     => '6',
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->status() === 200 && $this->response->body() === 'true';
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
