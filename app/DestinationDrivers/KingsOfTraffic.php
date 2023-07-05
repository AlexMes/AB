<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KingsOfTraffic implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://kings-of-traffic.biz';
    protected $response;
    protected $token;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['api_key'];
        $this->url   = $configuration['url'] ?? $this->url;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::withHeaders([
            'API-Key' => $this->token,
        ])->get($this->url . '/api/leads', [
            'dateFrom' => $since->addWeeks($page - 1)->toDateString(),
            'dateTo'   => $since->addWeeks($page)->toDateString(),
        ])
            ->offsetGet('data'))
            ->map(function ($item) {
                return new CallResult([
                    'id'          => $item['lead_id'],
                    'status'      => $item['status'],
                    'isDeposit'   => $item['status'] === 'CONVERTED',
                    'depositDate' => null,
                    'depositSum'  => '151',
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()
            ->withHeaders([
                'API-Key' => $this->token,
            ])
            ->post(sprintf('%s/api/', $this->url), $payload = [
                'api_key'      => $this->token,
                'referer'      => $lead->domain,
                'email'        => $lead->getOrGenerateEmail(),
                'phone'        => $lead->phone,
                'firstname'    => $lead->firstname,
                'lastname'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'fullname'     => $lead->fullname,
                'country'      => $lead->ipAddress->country_name,
                'utm_campaign' => $lead->uuid,
                'comment'      => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() . 'PHP_EOL' . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '',
                'info'         => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'ip'           => $lead->ip,
            ]);

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
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }
}
