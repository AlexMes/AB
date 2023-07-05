<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Yoose implements DeliversLeadToDestination
{
    protected $token;
    protected $affId;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
        $this->affId = $configuration['affId'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://buyleads.cloud/api/CreateLead', [
            'ApiKey'      => $this->token,
            'FirstName'   => $lead->firstname,
            'LastName'    => $lead->lastname,
            'Email'       => $lead->getOrGenerateEmail(),
            'Phone'       => $lead->phone,
            'AffiliateID' => $this->affId,
            'Funnel'      => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'Source'      => 'FACEBOOK',
            'Geo'         => optional($lead->ipAddress)->country_code,
            'Lang'        => $this->getLanguage($lead),
            'IP'          => $lead->ip,
            'URL'         => $lead->domain
        ]);
    }

    protected function getLanguage(Lead $lead)
    {
        if (Str::endsWith($lead->offer->name, 'PL')
            || in_array($lead->offer->name, ['ORLENPL_QUIZ'])
            || Str::contains($lead->offer->name, 'ORLEN')
            || Str::contains($lead->offer->name, 'TSLPL_LONG')
            || Str::contains($lead->offer->name, 'PGNG_MRQ')
            || Str::contains($lead->offer->name, 'PGEPL')
            ) {
            return 'PL';
        }

        if (Str::endsWith($lead->offer->name, 'CL', 'MX', 'PE', 'SL', 'BR')) {
            return 'ES';
        }

        if (in_array($lead->offer->getOriginalCopy()->name, ['MASTERCASHEU'])) {
            return 'RU';
        }

        return 'EN';
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'Status', null) === 'successful';
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'LeadID', null);
    }
}
