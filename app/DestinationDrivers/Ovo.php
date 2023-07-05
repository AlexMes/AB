<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Ovo implements DeliversLeadToDestination, CollectsCallResults
{
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post('https://toptimesnews.com', $payload = [
            'fname'    => $lead->firstname,
            'lname'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'country'  => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            'phone'    => $lead->phone,
            'email'    => $lead->getOrGenerateEmail(),
            'password' => 'ChangeMe123',
            'apikey'   => $this->token,
            'ip'       => $lead->ip,
            'dynamic'  => Str::before($lead->offer->getOriginalCopy()->name, '_'),
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', [
            'status' => $this->response->status(),
            'body'   => $this->response->json(),
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
        return data_get($this->response->json(), 'data.url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.userId');
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::get('https://toptimesnews.com/leads', [
            'apikey'   => $this->token,
            'fromdate' => $since->addWeeks($page - 1)->format('Y/m/d'),
            'page'     => $since->addWeeks($page)->format(''),
        ])->offsetGet('data'))->map(fn ($item) => new CallResult([
            'id'          => $item['userId'],
            'status'      => $item['call_status'],
            'isDeposit'   => Carbon::parse(data_get($item, 'ftd.Date'))->isCurrentYear(),
            'depositDate' => Carbon::parse(data_get($item, 'ftd.Date'))->toDateString()
        ]));
    }
}
