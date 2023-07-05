<?php


namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Illuminate\Support\Facades\Http;

class Afftek implements DeliversLeadToDestination
{
    protected $key;
    protected $response;
    protected $lang = 'RU';
    protected $url  = 'https://afftek.club';
    protected $funnel;

    public function __construct($configuration = null)
    {
        $this->key    = $configuration['key'];
        $this->url    = $configuration['url'] ?? $this->url;
        $this->lang   = $configuration['lang'] ?? 'RU';
        $this->funnel = $configuration['funnel'] ?? null;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url . '/api/get-form', [
            'key'       => $this->key,
            'firstname' => $lead->firstname,
            'lastname'  => $lead->lastname ?? 'Unknown',
            'email'     => $lead->getOrGenerateEmail(),
            'phone'     => $lead->phone,
            'geo'       => optional($lead->lookup)->country ?? optional($lead->ipAddress)->country_code,
            'funnel'    => $this->funnel ?? $this->funnel($lead),
            'lang'      => $this->lang,
            'ip2'       => $lead->ip,
            'cid'       => $lead->uuid,
            'comment'   => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().'-> '.$question->getAnswer())->implode(PHP_EOL) : '',
        ]);
    }

    protected function funnel(Lead $lead)
    {
        if ($this->key === 'RHtYTOOeHP4NnlgH') {
            return $lead->offer->name;
        }

        return $lead->hasPoll() ? $lead->getPollAsUrl() : $lead->domain;
    }
    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') === 1;
    }

    public function getError(): ?string
    {
        return data_get($this->response->json(), 'errors');
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'autologin_link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id');
    }
}
