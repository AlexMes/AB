<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Headsinvest implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $token;
    protected $division_name;
    protected $division_url;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url           = $configuration['url'];
        $this->token         = $configuration['token'];
        $this->division_name = $configuration['division_name'] ?? null;
        $this->division_url  = $configuration['division_url'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $url_body = '/leads';
        $query    = [
            'token'           => $this->token,
            'page'            => $page,
            'registered_from' => $since->startOfDay()->toDateTimeString(),
        ];
        if ($this->url == 'https://api.tricortrades.com') {
            $url_body = '/leads?apiToken=' . $this->token;
            $query    = [
                'page'        => $page,
                'createdForm' => $since->startOfDay()->toDateTimeString(),
            ];
        }

        return collect(Http::withHeaders([
            'Accept-Language' => 'en'
        ])->get($this->url . $url_body, $query)->throw()->offsetGet('data'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['id'],
                'status'      => $item['status'],
                'isDeposit'   => strtotime($item['deposited_at']) ?? false,
                'depositDate' => $item['deposited_at'],
                'depositSum'  => '151',
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $url_body       = '/leads?token=';
        $first_name     = 'fname';
        $last_name      = 'lname';
        $phone          = 'tel';
        $division_name  = 'division_name';
        if ($this->url == 'https://api.tricortrades.com') {
            $url_body       = '/leads/register?apiToken=';
            $first_name     = 'first_name';
            $last_name      = 'last_name';
            $phone          = 'phone';
            $division_name  = 'division';
        }

        $this->response = Http::withHeaders([
            'Accept-Language' => 'en'
        ])->post($this->url . $url_body . $this->token, $payload = [
            $first_name     => strtok($lead->firstname, " "),
            $last_name      => nullstr(strtok($lead->lastname, " ")) ?? nullstr(strtok($lead->middlename, " ")) ?? 'Unknown',
            'email'         => $lead->getOrGenerateEmail(),
            $phone          => '+' . $lead->phone,
            'password'      => 'ChangeMe123',
            $division_name  => $this->division_name ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'division_url'  => $this->division_url ?? 'https://' . $lead->domain,
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() &&
            (data_get($this->response->json(), 'id') !== null || data_get($this->response->json(), 'uuid') !== null);
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
        if (data_get($this->response->json(), 'uuid') !== null) {
            return data_get($this->response->json(), 'uuid');
        }

        return data_get($this->response->json(), 'id');
    }
}
