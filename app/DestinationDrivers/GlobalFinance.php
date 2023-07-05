<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class GlobalFinance implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://api.globallfinance.com';
    protected ?string $token;
    protected ?string $divisionName;

    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->token         = $configuration['token'] ?? null;
        $this->divisionName  = $configuration['division_name'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::get($this->url . '/leads', [
            'token'           => $this->token,
            'page'            => $page,
            'limit'           => 250,
            'registered_from' => $since->toDateString(),
        ])->throw()->offsetGet('data'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['id'],
                'status'      => $item['status'],
                'isDeposit'   => $item['deposited_at'] !== null,
                'depositDate' => $item['deposited_at'],
                'depositSum'  => '151',
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->post(sprintf('%s/leads?token=%s', $this->url, $this->token), $payload = [
            'fname'         => $lead->firstname,
            'lname'         => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'         => $lead->getOrGenerateEmail(),
            'tel'           => '+' . $lead->phone,
            'password'      => ucfirst(Str::random(10)) . rand(10, 99),
            'division_name' => $this->divisionName ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'division_url'  => 'https://' . $lead->domain,
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'id') !== null;
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
        return data_get($this->response->json(), 'id');
    }
}
