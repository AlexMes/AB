<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NormalTraffic implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://normal-traffic.com';
    protected $token;
    protected $lang;
    protected $funnel;
    protected $user_id;
    protected $source;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url     = $configuration['url'] ?? $this->url;
        $this->token   = $configuration['token'];
        $this->lang    = $configuration['lang'];
        $this->funnel  = $configuration['funnel'];
        $this->user_id = $configuration['user_id'] ?? null;
        $this->source  = $configuration['source'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::acceptJson()->withToken($this->token)->get($this->url . '/api/lead/get/statuses', [
            'date_from' => $since->addWeeks($page - 1)->toDateString(),
            'date_to'   => $since->addWeeks($page)->toDateString(),
        ])->throw()->json())->map(function ($item) {
            return new CallResult([
                'id'          => $item['id'],
                'status'      => $item['status'],
                'isDeposit'   => $item['status'] === 'Deposit',
                'depositDate' => null,
                'depositSum'  => $item['status'] === 'Deposit' ? '151' : null,
            ]);
        });
    }

    protected function payload(Lead $lead)
    {
        $payload = [
            'name'       => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'      => $lead->phone,
            'email'      => $lead->getOrGenerateEmail(),
            'ip'         => $lead->ip,
            'lang'       => $this->lang,
            'country_id' => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            'funnel'     => $this->funnel,
            'source_id'  => 1,
            'comment'    => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() . PHP_EOL . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '',
        ];
        if ($this->user_id && $this->source) {
            $payload['user_id'] = $this->user_id;
            $payload['source']  = $this->source;
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->asForm()->withToken($this->token)->post($this->url . '/api/lead/add', $payload = $this->payload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->getExternalId() !== null
            || Str::contains($this->getError(), 'Offers not found');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'autologin');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }
}
