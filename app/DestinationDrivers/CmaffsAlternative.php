<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Offer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CmaffsAlternative implements DeliversLeadToDestination
{
    /**
     * API url
     *
     * @var string
     */
    protected $url = 'https://nextera.site/api/v2/push/';
    /**
     * API login
     *
     * @var string
     */
    protected $login;

    /**
     * API password
     *
     * @var string
     */
    protected $token;

    /**
     * Raw response from the API
     *
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;

    /**
     * Construct Cmaffs driver
     *
     * @param array $configuration
     */
    public function __construct($configuration = null)
    {
        $this->login   = $configuration['login'];
        $this->token   = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url, [
            'login'       => $this->login,
            'token'       => $this->token,
            'offer_id'    => $this->getOfferId($lead->offer),
            'firstname'   => $lead->firstname,
            'lastname'    => $lead->lastname ?? $lead->middlename ?? 'unknown',
            'email'       => $lead->getOrGenerateEmail(),
            'phone'       => $lead->formatted_phone,
            'password'    => Str::random(12),
            'iso'         => $this->getCode($lead->offer),
            'ip'          => $lead->ip,
            'from_url'    => $lead->domain,
            'subcampaign' => $this->getSub($lead->offer),
            'sub2'        => $lead->uuid,
        ])->throw();
    }

    protected function getOfferId(Offer $offer)
    {
        // if(Str::endsWith('CL', $offer->name)){
        // return 490;
        // }

        return 609;
    }

    /**
     * Get country code
     *
     * @param Offer $offer
     *
     * @return string
     */
    protected function getCode(Offer $offer)
    {
        return substr($offer->name, -2);
    }

    /**
     * Get offer description
     *
     * @param int $offer
     *
     * @return string
     */
    protected function getSub(Offer $offer)
    {
        return 'Bitcoin Era '.substr($offer->name, -2);
    }

    /**
     * Determine is lead delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        if ($this->response->ok()) {
            return $this->response->offsetExists('statusCode') && $this->response->offsetGet('statusCode') !== 400;
        }

        return false;
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->response->body();
    }

    /**
     * Get lead redirect URl
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        if ($this->response->offsetExists('data')) {
            return $this->response->offsetGet('data')['redirect']['url'] ?? null;
        }

        return null;
    }

    /**
     * Get external lead ID
     *
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        if ($this->response->offsetExists('data')) {
            return $this->response->offsetGet('data')['customerID'] ?? null;
        }

        return null;
    }
}
