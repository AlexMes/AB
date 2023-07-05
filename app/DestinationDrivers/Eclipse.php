<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Eclipse implements DeliversLeadToDestination
{
    /**
     * URL to send leads
     *
     * @var string
     */
    protected $url = 'https://api.crm-eclipseaffiliate.com/api/lead';

    /**
     * Determine is lead delivered
     *
     * @var bool
     */
    protected bool $isSuccessful = false;

    /**
     * Error details
     *
     * @var string|null
     */
    protected ?string $error;

    /**
     * Eclipse constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        // nothing to add here
    }

    /**
     * Send lead to destination
     *
     * @param \App\Lead $lead
     */
    public function send(Lead $lead): void
    {
        $response = Http::post($this->url, [
            'firstName' => $lead->firstname,
            'lastName'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'     => $lead->formatted_phone,
            'email'     => $lead->getOrGenerateEmail(),
            'source'    => $lead->domain,
            'language'  => $this->getLanguage($lead),
            'country'   => $this->getCountry($lead),
            'sub1'      => 'RCT',
            'creo'      => 'xyz',
            'offer'     => $this->getOffer($lead)
        ]);

        if ($response->successful()) {
            $this->isSuccessful = true;

            return;
        }

        $this->isSuccessful = false;
        $this->error        = $response->body();
    }

    /**
     * Determine is lead was delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Get redirect url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * Get external id
     *
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return null;
    }

    /**
     * Get lead language
     *
     * @param \App\Lead $lead
     *
     * @return string
     */
    protected function getLanguage(Lead $lead)
    {
        return 'ru';
    }

    /**
     * Get lead country
     *
     * @param \App\Lead $lead
     *
     * @return string
     */
    protected function getCountry(Lead $lead)
    {
        return 'RU';
    }

    /**
     * Get formatted offer name
     *
     * @param \App\Lead $lead
     *
     * @return mixed
     */
    protected function getOffer(Lead $lead)
    {
        return Str::before($lead->offer->getOriginalCopy()->name, '_');
    }
}
