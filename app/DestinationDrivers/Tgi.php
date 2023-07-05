<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Tgi implements DeliversLeadToDestination
{
    protected $url;
    protected $affiliate;
    protected $campaign;
    protected $desk;
    protected $response;

    protected $email;
    protected $password;

    public function __construct($configuration = null)
    {
        $this->url       = $configuration['url'];
        $this->affiliate = $configuration['aff'];
        $this->campaign  = $configuration['campaign'];
        $this->desk      = $configuration['desk'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url.'/api/peoples', [
            'fname'            => $lead->firstname,
            'lname'            => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'            => $this->email = $lead->getOrGenerateEmail(),
            'country'          => optional($lead->ipAddress)->country_name,
            'language'         => 'Russian',
            'currency'         => 'USD',
            'phone'            => $lead->phone,
            'password'         => $this->password = 'Sd4'.Str::random(5),
            'campaign_id'      => $this->campaign,
            'affiliate_id'     => $this->affiliate,
            'desk_id'          => $this->desk,
            'terms'            => 1,
            'a_aid'            => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'description'      => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().'-> '.$question->getAnswer())->implode(PHP_EOL) : ''
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful()
        && data_get($this->response->json(), 'data.id') !== null
        && data_get($this->response->json(), 'data.id') !== 0;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return sprintf('%s/api/autologin?email=%s&password=%s', $this->url, $this->email, $this->password);
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.id');
    }
}
