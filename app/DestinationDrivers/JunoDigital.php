<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class JunoDigital implements DeliversLeadToDestination
{
    protected $username;
    protected $password;
    protected $source;
    protected $response;


    public function __construct($configuration = null)
    {
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
        $this->source   = $configuration['source'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withToken($this->getToken())
            ->post('https://api.junodigital.xyz/api/lead', [
                'first_name'   => $lead->firstname,
                'last_name'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'phone'        => $lead->phone,
                'email'        => $lead->getOrGenerateEmail(),
                'country'      => $this->getCountry($lead),
                'password'     => Str::random(10).rand(10, 99),
                'ip'           => $lead->ip,
                'funnel_name'  => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'funnel_url'   => $lead->domain.'?utm_source=1',
                'source'       => $this->source,
                'click_id'     => $lead->uuid,
            ]);
    }

    protected function getToken()
    {
        return Http::get('https://api.junodigital.xyz/api/authenticate', [
            'email'    => $this->username,
            'password' => $this->password
        ])->offsetGet('token');
    }

    public function getCountry(Lead $lead)
    {
        return optional($lead->ipAddress)->country_code ?? optional($lead->lookup)->country ?? substr($lead->offer->name, -2);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->response->offsetExists('lead_id');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        if ($this->isDelivered() && $this->response->offsetExists('redirect_url')) {
            return $this->response->offsetGet('redirect_url');
        }

        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->isDelivered() && $this->response->offsetExists('lead_id')) {
            return $this->response->offsetGet('lead_id');
        }

        return null;
    }

    /**
     * @param \Carbon\Carbon $startDate
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectFtdSinceDate(Carbon $startDate)
    {
        return Http::withToken($this->getToken())
            ->get('https://api.junodigital.xyz/api/deposits', [
                'perPage'  => 1000,
            ])->throw()->json();
    }
}
