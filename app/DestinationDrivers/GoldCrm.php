<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoldCrm implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url = 'http://goldcrm.pro';
    protected $token;
    protected $databaseId = 6;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url        = $configuration['url'] ?? $this->url;
        $this->token      = $configuration['token'];
        $this->databaseId = $configuration['database_id'] ?? $this->databaseId;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        // no pagination at all, returns all at once I suppose.
        // also, we can do a fake pagination, get all and ->skip()->take() on the collection...
        if ($page > 1) {
            return collect();
        }

        $since = $since->toImmutable();

        return collect(data_get(Http::withToken($this->token, 'Token')->get(sprintf('%s/api/leads', $this->url), [
            /*'from' => $since->addWeeks($page - 1)->startOfDay()->toDateTimeString(),
            'to'   => $since->addWeeks($page)->endOfDay()->toDateTimeString(),*/
        ])->json(), 'leads'))->map(function ($item) {
            return new CallResult([
                'id'        => $item['id'],
                'status'    => $item['status'],
                'isDeposit' => in_array($item['status'], ['Депозит']),
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $info = sprintf(
            "Offer: %s\n%s",
            Str::before($lead->offer->getOriginalCopy()->name, '_'),
            $lead->getPollAsText()
        );

        $this->response = Http::withToken($this->token, 'Token')
            ->post(sprintf('%s/api/leads/', $this->url), $payload = [
                'name'        => $lead->fullname,
                'email'       => $lead->getOrGenerateEmail(),
                'phone'       => $lead->phone,
                'geo'         => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
                'database_id' => $this->databaseId,
                'info'        => $info,
            ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', [
            'status' => $this->response->status(),
            'body'   => $this->response->body(),
        ]);
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
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id');
    }
}
