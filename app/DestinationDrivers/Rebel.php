<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Rebel implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url = 'https://rebelcrypt.com';
    protected $token;
    protected $language;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'] ?? $this->url;
        $this->token    = $configuration['token'];
        $this->language = $configuration['lang'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'API-KEY' => $this->token
        ])->post(sprintf('%s/api/partner/add_lead', $this->url), $payload = [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'      => $lead->getOrGenerateEmail(),
            'phonecc'    => $lead->lookup->country,
            'password'   => 'ChangeMe123!',
            'phone'      => '+'.$lead->phone,
            'country'    => $lead->ipAddress->country_code,
            'language'   => $this->language,
            'user_ip'    => $lead->ip,
            'os'         => Str::before($lead->offer->getOriginalCopy()->name, '_') ?? $lead->domain.'?utm_source=t',
            'campaign'   => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'coment'     => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() .PHP_EOL . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '',
        ]);

        $lead->addEvent('payload', $payload);
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
        return data_get($this->response->json(), 'login_link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $response = Http::withHeaders([
            'API-KEY' => $this->token
        ])->post(sprintf('%s/api/partner/get_info', $this->url), [
            'type'  => 'list',
            'from'  => $since->startOfDay()->toDateTimeString(),
            'to'    => now()->toDateTimeString(),
            'limit' => 1000,
            'page'  => $page
        ])->offsetGet('data');

        return collect($response)->map(fn ($item) => new CallResult([
            'id'     => $item['id'],
            'status' => $item['status']
        ]));
    }
}
