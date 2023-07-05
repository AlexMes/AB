<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BillType implements DeliversLeadToDestination
{
    protected $source;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->source = $configuration['source'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->post('https://moneybutton.click/api/v3/lead', $payload = [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? 'Unknown',
            'from_url'   => $lead->offer->description ?? $lead->domain,
            'phone'      => $lead->phone,
            'email'      => $lead->getOrGenerateEmail(),
            'source_id'  => $this->source,
            'autologin'  => true,
            'geo'        => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            'ip'         => $lead->ip,
            'comments'   => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() . PHP_EOL . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : ''
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', [
            'status' => $this->response->status(),
            'body'   => $this->response->body(),
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'success');
    }

    public function getError(): ?string
    {
        return Str::limit($this->response->body(), 1000);
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'autologin');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id');
    }
}
