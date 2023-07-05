<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Affcountry implements DeliversLeadToDestination
{
    protected $goalId;
    protected $afffiliateId;

    /**
     * Undocumented variable
     *
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;

    public function __construct($configuration = null)
    {
        $this->goalId       = $configuration['goal'];
        $this->afffiliateId = $configuration['affiliate'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://track.affcountry.com/s2s/new_lead/create_by_goal', [
            'goal_id'      => $this->goalId,
            'affiliate_id' => $this->afffiliateId,
            'ip'           => $lead->ip,
            'email'        => $lead->getOrGenerateEmail(),
            'firstname'    => $lead->firstname,
            'lastname'     => $lead->lastname,
            'phone'        => $lead->formatted_phone,
            'custom2'      => $lead->domain,
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful();
    }

    public function getError(): ?string
    {
        return Str::limit($this->response->body(), 255);
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->response->offsetExists('info')) {
            return $this->response->offsetGet('info')['lead_id'];
        }

        return null;
    }
}
