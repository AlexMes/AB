<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class HyperNet implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url = 'http://api.myadsworld.org';
    protected $affc;
    protected $bxc;
    protected $vtc;
    protected $token;
    protected $landingUrl;
    protected $response;


    public function __construct($configuration = null)
    {
        $this->url        = $configuration['url'] ?? $this->url;
        $this->affc       = $configuration['affc'] ?? null;
        $this->bxc        = $configuration['bxc'] ?? null;
        $this->vtc        = $configuration['vtc'] ?? null;
        $this->token      = $configuration['token'] ?? null;
        $this->landingUrl = $configuration['landing_url'] ?? null;
    }

    /**
     * Pull results from the destination
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(data_get(Http::withHeaders([
            'x-api-key' => $this->token
        ])->get(sprintf('%s/api/external/integration/lead', $this->url), [
            'from'   => $since->addWeeks($page - 1)->toISOString(),
            'to'     => $since->addWeeks($page)->toISOString(),
        ])->throw(), 'rows'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['id'],
                'status'      => $item['registration']['rawStatus'] ?? 'Unknown',
                'isDeposit'   => $item['isDeposited'],
                'depositDate' => $item['depositedAt'] !== null ? Carbon::parse($item['depositedAt'] !== null) : null,
                'depositSum'  => $item['isDeposited'] ? '151' : null,
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'x-api-key' => $this->token
        ])->post(sprintf('%s/api/external/integration/lead', $this->url), $payload = [
            'affc'    => $this->affc,
            'bxc'     => $this->bxc,
            'vtc'     => $this->vtc,
            'profile' => [
                'firstName' => $lead->firstname,
                'lastName'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'     => $lead->getOrGenerateEmail(),
                'password'  => sprintf("%s%s", Str::random(10), rand(10, 99)),
                'phone'     => $lead->phone,
            ],
            'subId_a'     => $lead->hasPoll() ? $lead->getPollAsUrl() : '',
            'ip'          => $lead->ip,
            'funnel'      => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'landingURL'  => $this->landingUrl ?? $this->getLandingUrl($lead),
            'geo'         => optional($lead->ipAddress)->country_code ?? optional($lead->lookup)->country ?? 'RU',
            'lang'        => 'RU',
            'landingLang' => 'RU',
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('response', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'success', false);
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'redirectUrl', null);
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'leadId', null);
    }

    /**
     * @param Lead $lead
     *
     * @return string|null
     */
    protected function getLandingUrl(Lead $lead): ?string
    {
        if ($this->affc === 'AFF-Z7BSEWNK65') {
            return 'https://funequa.info';
        }

        return $lead->domain;
    }
}
