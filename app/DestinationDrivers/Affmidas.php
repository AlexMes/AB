<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Affmidas implements DeliversLeadToDestination, CollectsCallResults
{
    public bool $nullInterval = false;
    protected $url            = 'https://leads.affmidas.com';
    protected $token;
    protected $response;
    protected $language;
    protected $funnel;

    public function __construct($configuration = null)
    {
        $this->token    = $configuration['token'];
        $this->url      = $configuration['url'] ?? $this->url;
        $this->language = $configuration['language'] ?? 'RU';
        $this->funnel   = $configuration['funnel'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        $response = Http::get($this->url . '/api/get-statuses', [
            'key'  => $this->token,
            'from' => $since->addDays($page - 1)->startOfDay()->toDateTimeString(),
            'to'   => $since->addDays($page)->endOfDay()->toDateTimeString(),
        ])->json();

        $this->nullInterval = empty($response) && $since->addDays($page)->endOfDay()->lessThanOrEqualTo(now());

        return collect($response)->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['status'],
            'isDeposit'   => $item['ftd'],
            'depositDate' => $item['ftd'] ? Carbon::parse($item['ftd_date'])->toDateString() : null,
        ]));
    }

    protected function payload(Lead $lead): array
    {
        $payload = [
            'key'       => $this->token,
            'firstname' => $lead->firstname,
            'lastname'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'     => $lead->getOrGenerateEmail(),
            'phone'     => $lead->phone,
            'funnel'    => $this->funnel ?? $this->source($lead),
            'geo'       => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            'lang'      => $this->language,
            'ip2'       => $lead->ip,
            'cid'       => $lead->uuid,
        ];

        if (Str::contains($this->url, 'cryptotrafpar')) {
            $payload['s1'] = $lead->getPollAsText();
        } else {
            $payload['comment'] = $lead->getPollAsText();
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url . '/api/get-form', $payload = $this->payload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', [
            'status' => $this->response->status(),
            'body'   => $this->response->body(),
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') == 1;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'autologin_link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id');
    }

    /**
     * @param Lead $lead
     *
     * @return string
     */
    protected function source(Lead $lead)
    {
        if (Str::contains($lead->offer->name, 'GAZPROMMILLER')) {
            return 'GAZPROMBANK_QUIZ';
        }

        return str_replace(['_SHM', '_JRD'], '', $lead->offer->description ?? $lead->offer->name);
    }
}
