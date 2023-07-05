<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Revolut implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'];
        $this->token = $configuration['token'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::get($this->url.'/leads', [
            'apiToken'    => $this->token,
            'page'        => $page,
            'createdFrom' => $since->startOfDay()->toDateTimeString(),
        ])->throw()->offsetGet('data'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['uuid'],
                'status'      => $item['status'],
                'isDeposit'   => filter_var($item['ftd'], FILTER_VALIDATE_BOOLEAN) ?? false,
                'depositDate' => $item['ftd_date'],
                'depositSum'  => '151',
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->post($this->url.'/leads/register?apiToken='.$this->token, $payload = [
            'first_name' => strtok($lead->firstname, " "),
            'last_name'  => strtok($lead->lastname, " ") ?? strtok($lead->middlename, " ") ?? 'Unknown',
            'email'      => $lead->getOrGenerateEmail(),
            'phone'      => '+'.$lead->phone,
            'password'   => 'ChangeMe123!',
            'division'   => preg_replace('/[0-9]+/', '', Str::before($lead->offer->getOriginalCopy()->name, '_')),
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'uuid') !== null;
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
        return data_get($this->response->json(), 'uuid');
    }
}
