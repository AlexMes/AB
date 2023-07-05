<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Mfury implements DeliversLeadToDestination, CollectsCallResults
{
    private $response;
    private $payload;

    private $url = 'https://core.mfury.xyz';
    private $token;
    private $deskId;
    private $passport;
    private $citizenship;
    private $source;
    private $funnel;
    private $countryId = 183; //default ru
    private $advSource2;
    private $advSource3;

    public function __construct($configuration = null)
    {
        $this->url             = $configuration['url'] ?? $this->url;
        $this->token           = $configuration['token'];
        $this->deskId          = $configuration['desk_id'] ?? null;
        $this->passport        = $configuration['passport'] ?? null;
        $this->citizenship     = $configuration['citizenship'] ?? null;
        $this->source          = $configuration['source'] ?? null;
        $this->funnel          = $configuration['funnel'] ?? null;
        $this->advSource2      = $configuration['adv_source_2'] ?? null;
        $this->advSource3      = $configuration['adv_source_3'] ?? null;
        $this->countryId       = $configuration['country_id'] ?? $this->countryId;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        if ($page > 1) {
            return collect([]);
        }

        $since->toImmutable();
        $result = collect([]);
        $cursor = null;

        do {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])
                ->get($this->url . '/api/affiliate/v1/clients', [
                    'created_from' => $since->subDays(1)->format('Y-m-d H:i'),
                    'created_to'   => now()->format('Y-m-d H:i'),
                    'per_page'     => 200,
                    'cursor'       => $cursor,
                ]);

            $cursor = data_get($response->json(), 'cursor.next');
            $items  = data_get($response->json(), 'data', []);

            foreach ($items as $item) {
                $result->push(new CallResult([
                    'id'          => $item['uuid'],
                    'status'      => $item['client_status_name'],
                    'isDeposit'   => $item['client_status_name'] === 'depositor',
                ]));
            }
        } while ($cursor && $items);

        return $result;
    }

    private function payload($lead)
    {
        $payload = [
            "clients" => [
                [
                    "first_name"       => $lead->firstname,
                    "last_name"        => $lead->lastname ?? $lead->middlename,
                    "middle_name"      => $lead->middlename,
                    "email"            => $lead->getOrGenerateEmail(),
                    "phone"            => '+' . $lead->phone,
                    "password"         => "Es13BrDeq",
                    "passport"         => $this->passport,
                    "citizenship"      => $this->citizenship ?? $lead->ipAddress->country_name,
                    "adv_source"       => $this->source ?? Str::before($lead->offer->name, '_'),
                    "adv_funnel"       => $this->funnel ?? 'https://' . $lead->domain,
                    "desk_id"          => $this->deskId,
                    "country_id"       => $this->countryId,
                    "affiliate_fields" => [
                        "click_id"     => $lead->clickid,
                        "adv_source_2" => $this->advSource2,
                        "adv_source_3" => $this->advSource3,
                    ],
                ],
            ],
        ];

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post(
            $this->url . '/api/affiliate/v1/clients',
            $this->payload = $this->payload($lead)
        );

        $lead->addEvent('payload', $this->payload);
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
        //When client use adblock: Invalid authorization link. Use your login and password to login
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post($this->url . '/api/affiliate/v1/clients/login', [
            'phone'    => $this->payload['clients']['0']['phone'],
            'password' => $this->payload['clients']['0']['password'],
        ]);

        if ($response->successful()) {
            return data_get($response->json(), 'data.auth_url');
        }

        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.0.uuid');
    }
}
