<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OneXCpa implements DeliversLeadToDestination, CollectsCallResults
{
    public bool $nullInterval = false;
    protected string $url     = 'https://1xcpa.com';
    protected $offer;
    protected $statusGetType = false;
    protected $token;

    public function __construct($configuration = null)
    {
        $this->url           = $configuration['url'] ?? $this->url;
        $this->offer         = $configuration['offer'];
        $this->token         = $configuration['token'];
        $this->statusGetType = $configuration['status_get_type'] ?? $this->statusGetType;
    }

    protected function payload(Lead $lead): array
    {
        $payload = [
            'id'    => $lead->uuid,
            'offer' => $this->offer,
            'ip'    => $lead->ip,
            'name'  => $lead->firstname,
            'last'  => $lead->lastname,
            'phone' => $lead->phone,
            'email' => $lead->getOrGenerateEmail(),
        ];

        if (Str::contains($this->url, 'afftenor.info')) {
            $payload['comm'] = $lead->getPollAsText();
        }

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post(
            sprintf('%s/api/ext/add.json?id=%s', $this->url, $this->token),
            $payload = $this->payload($lead)
        );

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') === 'ok';
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

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->max('2022-11-01')->toImmutable();

        $response = Http::post(sprintf('%s/api/ext/list.json?id=%s', $this->url, $this->token), [
            'from' => $since->addWeeks($page - 1)->toDateString(),
            'to'   => $since->addWeeks($page)->toDateString(),
        ])->json();

        $this->nullInterval = empty($response) && $since->addWeeks($page)->lessThanOrEqualTo(now());

        return collect($response)->map(fn ($item) => new CallResult([
            'id'        => $item['uid'],
            'status'    => $item['custom'],
            'isDeposit' => $this->statusGetType
                ? $item['stage'] === 'approve' && $item['phase'] === 3 && $item['status'] === 10
                : in_array($item['custom'], ['DEP', 'sup_Work', 'sup_WORK', 'sup_DONE', 'sup_NEW', 'ret_New', 'ret_NEW', 'ret_Negative', 'ret_Transfer', 'ret_WORK' ,'Deposit']),
        ]));
    }
}
