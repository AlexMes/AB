<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Offer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Cmaffs implements DeliversLeadToDestination
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
            'password'    => Str::random(10).rand(10, 99),
            'iso'         => $this->getCode($lead->offer),
            'ip'          => $lead->ip,
            'from_url'    => $lead->domain,
            'subcampaign' => $this->getSub($lead->offer),
            'sub2'        => $lead->uuid,
        ])->throw();
    }

    protected function getOfferId(Offer $offer)
    {
        if (Str::endsWith($offer->name, 'PL') || Str::contains($offer->name, ['ORLENPL_QUIZ'])) {
            return 808;
        }

        if (Str::endsWith($offer->name, 'CL')) {
            return 490;
        }

        if (Str::endsWith($offer->name, 'ZA')) {
            return 487;
        }

        if (Str::endsWith($offer->name, 'MY')) {
            return 391;
        }

        if (Str::endsWith($offer->name, 'MX')) {
            return 522;
        }

        if (Str::endsWith($offer->name, ['NL', 'NL2','NLLOGIN'])) {
            return 285;
        }

        if (Str::endsWith($offer->name, 'ES')) {
            return 528;
        }

        return 487;
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
        if (Str::contains($offer->name, 'YUANPAYNL')) {
            return 'NL';
        }

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
        if (Str::contains($offer->name, 'NEURO')) {
            return  'Neuro-Autotrading Softw';
        }

        if (Str::endsWith($offer->name, 'ES')) {
            return 'Bitcoin Rev 11';
        }

        if (Str::contains($offer->name, 'YUAN')) {
            return 'Yuan Pay ';
        }

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
            return $this->response->offsetExists('lead_id');
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
        if ($this->response->offsetExists('auto_login_url')) {
            return $this->response->offsetGet('auto_login_url') ?? null;
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
        if ($this->response->offsetExists('lead_id')) {
            return $this->response->offsetGet('lead_id');
        }

        return null;
    }
}
