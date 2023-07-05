<?php

namespace App\DestinationDrivers;

use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Belmar implements Contracts\DeliversLeadToDestination, Contracts\CollectsCallResults
{
    protected $response;
    protected $url = 'https://crm.belmar.pro';
    protected $token;
    protected $countryCode;
    protected $boxId;
    protected $offerId;
    protected $landingUrl;
    protected $language;
    protected $shortAnswer;
    protected $custom1;
    protected $custom2;
    protected $custom3;
    protected $nameToLower;

    public function __construct($configuration = null)
    {
        $this->url         = $configuration['url'] ?? $this->url;
        $this->token       = $configuration['token'];
        $this->countryCode = $configuration['countryCode'] ?? null;
        $this->boxId       = $configuration['box_id'] ?? null;
        $this->offerId     = $configuration['offer_id'] ?? null;
        $this->landingUrl  = $configuration['landing_url'] ?? null;
        $this->language    = $configuration['language'] ?? 'RU';
        $this->custom1     = $configuration['custom1'] ?? null;
        $this->custom2     = $configuration['custom2'] ?? null;
        $this->custom3     = $configuration['custom3'] ?? null;
        $this->shortAnswer = $configuration['short_answer'] ?? false;
        $this->nameToLower = $configuration['name_to_lower'] ?? false;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withHeaders([
            'token' => $this->token,
        ])
            ->post($this->url . '/api/v1/getstatuses', [
                'date_from' => $since->toDateTimeString(),
                'date_to'   => now()->toDateTimeString(),
                'page'      => $page - 1,
                'limit'     => 500,
            ])->throw()->offsetGet('data'))->map(function ($item) {
                return new CallResult([
                    'id'        => $item['id'],
                    'status'    => $item['status'] ?: 'New',
                    'isDeposit' => (bool)$item['ftd'],
                ]);
            });
    }

    protected function payload($lead): array
    {
        $payload = [
            'firstName'   => $lead->firstname,
            'lastName'    => $lead->lastname ?? 'Unknown',
            'phone'       => $lead->phone,
            'email'       => $lead->getOrGenerateEmail(),
            'countryCode' => $this->countryCode ?? optional($lead->ipAddress)->country_code ?? 'RU',
            'box_id'      => $this->boxId,
            'offer_id'    => $this->offerId,
            'landingURL'  => $this->landingUrl ?? $lead->domain,
            'ip'          => $lead->ip,
            'password'    => 'ChangeMe123!',
            'language'    => $this->language,
            'click_id'    => $lead->clickid,
            'custom1'     => $this->custom1,
            'custom2'     => $this->custom2,
            'custom3'     => $this->custom2,
        ];

        if ($this->shortAnswer) {
            $payload['quizAnswers'] = $lead->hasPoll() ? $lead->getPollAsUrl() : '';
        } else {
            $payload['quizAnswers'] = $lead->getPollAsText();
        }

        if ($this->nameToLower) {
            $payload['firstName'] = strtolower($payload['firstName']);
            $payload['lastName']  = strtolower($payload['lastName']);
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'token'        => $this->token,
            'Content-Type' => 'application/json'
        ])->post($this->url . '/api/v1/addlead', $payload = $this->payload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
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
        return data_get($this->response->json(), 'autologin');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id');
    }
}
