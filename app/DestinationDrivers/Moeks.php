<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Moeks implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $response;
    protected $error;

    public function __construct($configuration = null)
    {
        $this->url = $configuration['url'];
    }

    public function send(Lead $lead): void
    {
        try {
            $this->response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json'
            ])->post('https://moekstrades.com/api/leads', [
                'first_name' => $lead->firstname,
                'last_name'  => $lead->lastname,
                'email'      => $lead->getOrGenerateEmail(),
                'password'   => Str::random(12).rand(10, 99),
                'phone'      => $lead->phone,
                'source'     => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            ]);
        } catch (\Throwable $th) {
            $this->error = $th->getMessage();
        }
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'success');
    }

    public function getError(): ?string
    {
        return $this->error ?? $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'uuid');
    }

    /**
     * Collect statuses from the API
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::get('https://moeks-trade.net/api/leads', [
            'page'        => $page
        ])->throw()->offsetGet('data'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['uuid'],
                'status'      => $item['status'],
                'isDeposit'   => $item['ftd'] ?? false,
                'depositDate' => $item['ftd_date'],
                'depositSum'  => '151',
            ]);
        });
    }
}
