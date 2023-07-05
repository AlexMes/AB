<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class ClickOut implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://api.dashfx.net';
    protected ?string $token;

    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'] ?? $this->url;
        $this->token = $configuration['token'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get($this->url . '/api/publisher/leads', [
            // no limit in docs, default 7 prev days
            /*'from'  => $since->toDateString(),*/
            'from'  => $since->addWeeks($page - 1)->toDateString(),
            'to'    => $since->addWeeks($page)->toDateString(),
        ])->throw()->offsetGet('data'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['internalClickId'],
                'status'      => $item['status'],
                'isDeposit'   => $item['ftdDate'] !== null,
                'depositDate' => $item['ftdDate'],
                'depositSum'  => $item['revenue'],
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()
            ->asJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])
            ->post(sprintf('%s/api/publisher/lead', $this->url), $payload = [
                'firstname' => $lead->firstname,
                'lastname'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'     => $lead->getOrGenerateEmail(),
                'telephone' => '+' . $lead->phone,
                'country'   => optional($lead->ipAddress)->country_code ?? 'RU',
                /*'source'    => Str::before($lead->offer->name, '_'),*/
                'source'    => 'https://' . $lead->domain,
                'clickId'   => $lead->uuid,
                'password'  => ucfirst(Str::random(10)) . rand(10, 99),
                'ipAddress' => $lead->ip,
            ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'data.internalClickId') !== null;
    }

    public function getError(): ?string
    {
        return $this->response->status() === 401
            ? 'Unauthorized'
            : $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'data.redirectUrl');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.internalClickId');
    }
}
