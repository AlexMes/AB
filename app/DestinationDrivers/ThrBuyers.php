<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ThrBuyers implements DeliversLeadToDestination
{
    protected $user;
    protected $source;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->user   = $configuration['user'];
        $this->source = $configuration['source'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()->post('https://13buyers.com/api/leads', $payload = [
            'full_name'    => $lead->fullname,
            'country'      => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            'email'        => $lead->getOrGenerateEmail(),
            'landing'      => $lead->domain,
            'description'  => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().'-> '.$question->getAnswer())->implode(PHP_EOL) : '',
            'phone'        => $lead->phone,
            'user_id'      => $this->user,
            'ip'           => $lead->ip,
            'source'       => $this->source,
            'landing_name' => $this->offer($lead),
        ]);

        $lead->addEvent('payload', $payload);
    }

    protected function offer(Lead $lead)
    {
        return str_replace(['_SHM','_JRD'], '', $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_'));
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'link_auto_login');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }
}
