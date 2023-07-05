<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Str;

class RedCrm implements DeliversLeadToDestination
{
    protected string $url = 'https://api.redcrm.space/api/v1';
    protected ?string $token;
    protected ?string $source;
    protected $sendIp;

    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url    = $configuration['url'] ?? $this->url;
        $this->token  = $configuration['token'] ?? null;
        $this->source = $configuration['source'] ?? null;
        $this->sendIp = $configuration['send_ip'] ?? false;
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function getPayload(Lead $lead): array
    {
        $payload = [
            'client' => [
                'client_name'    => $lead->firstname,
                'client_surname' => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'client_phone'   => $lead->phone,
                'client_email'   => $lead->getOrGenerateEmail(),
                'client_source'  => $this->source ?? $this->source($lead),
                'country'        => optional($lead->ipAddress)->country_code ?? 'RU',
                'source_domen'   => $lead->domain,
                "pixel"          => "Pixel",
                "utm"            => "UTM",
                'client_comment' => $lead->getPollAsText(),
            ],
        ];

        if ($this->sendIp) {
            $payload['client']['client_ip'] = $lead->ip;
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()
            ->withHeaders([
                'X-Api-Key' => $this->token,
            ])
            ->post(sprintf('%s/', $this->url), $payload = $this->getPayload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    protected function source(Lead $lead)
    {
        $name = Str::before($lead->offer->getOriginalCopy()->name, '_');

        if (Str::contains($this->token, '59717314104') && Str::contains($name, 'GAZPROMMILLER')) {
            return 'GAZQUIZ';
        }

        return $name;
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
        return data_get($this->response->json(), 'url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }
}
