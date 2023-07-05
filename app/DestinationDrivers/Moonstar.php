<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Moonstar implements DeliversLeadToDestination, CollectsCallResults
{
    protected $token;
    protected $url;
    protected $source;
    protected $campaign;
    protected $landing;
    protected $response;
    protected $subId;

    public function __construct($configuration = null)
    {
        $this->token    = $configuration['token'] ?? null ;
        $this->url      = $configuration['url'] ?? null ;
        $this->source   = $configuration['source'] ?? null ;
        $this->campaign = $configuration['campaign'] ?? null;
        $this->landing  = $configuration['landing'] ?? null;
        $this->page     = $configuration['page'] ?? null;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url.'/lead', $payload = [
            'full_name'   => $lead->fullname,
            'email'       => $lead->getOrGenerateEmail(),
            'phone'       => $lead->phone,
            'ip'          => $lead->ip,
            'landing'     => $this->landing($lead),
            'source'      => $this->source,
            'country'     => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            'page'        => $this->page,
            'campaign'    => $this->campaign,
            'vertical'    => 'Forex',
            'description' => $this->subId = $lead->uuid,
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * @param Lead $lead
     *
     * @return string
     */
    public function landing(Lead $lead)
    {
        if ($this->landing) {
            return $this->landing;
        }

        return $lead->domain . '?utm_source=t';
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'Status') === 'Success';
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'Link');
    }

    public function getExternalId(): ?string
    {
        return $this->subId;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::withToken($this->token)->get($this->url.'/web-master/leads', [
            'date_start_registration' => $since->subDay()->toDateString(),
            'date_end_registration'   => now()->toDateString(),
            'page'                    => $page,
            'per_page'                => 250,
        ])->offsetGet('data'))->map(fn ($item) => new CallResult([
            'id'          => $item['description'],
            'status'      => $item['status'],
            'isDeposit'   => filter_var($item['is_deposit'], FILTER_VALIDATE_BOOL),
            'depositDate' => Carbon::parse($item['deposited_at']),
        ]));
    }
}
