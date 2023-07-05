<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Someones implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url = $configuration['url'];
    }

    /**
     * Collect statuses from the API
     *
     * @param \Carbon\Carbon $since
     * @param int            $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect();

        $response =  collect(Http::withHeaders([
            'api-key' => '78434dda-66ff-467e-b9a2-789ae19b61df',
        ])->asForm()->post(str_replace('/add-leads/', '/status/lending/', $this->url), [
            'from' => $since->toDateString(),
            'to'   => now()->toDateString(),
            'page' => $page,
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url, [
            'firstname'     => $lead->firstname,
            'password'      => Str::random(6).rand(10, 99),
            'phone_code'    => $lead->lookup->prefix,
            'phone_no_code' => substr($lead->phone, strlen($lead->lookup->prefix)),
            'lastname'      => $lead->lastname,
            'email'         => $lead->getOrGenerateEmail(),
            'phone'         => $lead->phone,
            'ip'            => $lead->ip,
            'country_name'  => optional($lead->ipAddress)->country_name,
            'country_code'  => optional($lead->ipAddress)->country_code,
            'field_5'       => $lead->hasPoll() ? $lead->getPollAsUrl() : '',
            'description'   => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().'-> '.$question->getAnswer())->implode('|') : ''
        ]);
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
        return data_get($this->response->json(), 'redirect', null);
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id', null);
    }
}
