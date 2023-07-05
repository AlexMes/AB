<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CrypIM implements DeliversLeadToDestination, CollectsCallResults
{
    protected $hash;
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->hash  = $configuration['hash'];
        $this->token = $configuration['token'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withToken($this->token)->get('https://cryp.im/api/v1/web/registrations', [
            'date_from' => $since->startOfDay()->toDateTimeString(),
            'page'      => $page,
        ])->offsetGet('data'))->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['status'],
            'isDeposit'   => $item['depositsed'] ?? false,
            'depositDate' => Carbon::parse($item['ftd_date'] ?? now())->toDateString(),
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withToken($this->token)->post('https://cryp.im/api/v1/web/conversion', [
            'flow_hash'  => $this->hash,
            'landing'    => $lead->domain.'?utm_source=t',
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'      => $lead->getOrGenerateEmail(),
            'phone'      => '+'.$lead->phone,
            'ip'         => $lead->ip,
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'result') === 'success';
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'redirect_url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'registration_id');
    }
}
