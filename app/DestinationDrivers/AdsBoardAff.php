<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class AdsBoardAff implements DeliversLeadToDestination
{
    protected string $url = 'https://uleads.app';
    protected string $apiKey;

    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url    = $configuration['url'] ?? $this->url;
        $this->apiKey = $configuration['api_key'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->asForm()->post(
            sprintf('%s/leads/register', $this->url),
            $payload = $this->payload($lead)
        );

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function payload(Lead $lead): array
    {
        $payload = [
            'api_key'   => $this->apiKey,
            'firstname' => $lead->firstname,
            'lastname'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'     => $lead->phone,
            'email'     => $lead->getOrGenerateEmail(),
            'poll'      => $lead->poll,
            'ip'        => $lead->ip,
        ];

        return $payload;
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->getExternalId() !== null;
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
        return data_get($this->response->json(), 'lead.id');
    }
}
