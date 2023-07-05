<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Log;

class VaniInvest implements DeliversLeadToDestination, CollectsCallResults
{
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $response = Http::withToken($this->token)->get('https://api.vaninvestment.io/affiliates/leads');

        Log::info($response, ['vani']);

        return collect();
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->post('https://api.vaninvestment.io/affiliates/leads', [
            'firstname' => $lead->firstname,
            'lastname'  => $lead->lastname,
            'fullname'  => $lead->fullname,
            'country'   => optional($lead->lookup)->country_name,
            'email'     => $lead->getOrGenerateEmail(),
            'phone'     => $lead->phone,
            'info'      => $lead->domain,
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') === 200;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        try {
            return $this->response->json()['data']['valid'][0]['login_url'];
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getExternalId(): ?string
    {
        try {
            return $this->response->json()['data']['valid'][0]['id'];
        } catch (\Throwable $th) {
            return null;
        }
    }
}
