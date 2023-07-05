<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Illuminate\Support\Facades\Http;

class CareCorp implements DeliversLeadToDestination
{
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withToken($this->token)->post('https://carecorp.work/api/lead/add', [
            'name'       => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'      => $lead->phone,
            'email'      => $lead->getOrGenerateEmail(),
            'ip'         => $lead->ip,
            'lang'       => 'RU',
            'funnel'     => $lead->offer->name,
            'country_id' => optional($lead->lookup)->country ?? $lead->ipAddress->country,
            'source_id'  => 1,
            'comment'    => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $ans) => $ans->getQuestion().' => '. $ans->getAnswer())->implode(PHP_EOL) : '',
        ]);

        $lead->addEvent('payload', $this->response->json());
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
        return data_get($this->response->json(), 'autologin_url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }
}
