<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Fxg24 implements DeliversLeadToDestination
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var bool
     */
    protected bool $isDelivered = false;

    /**
     * @var string|null
     */
    protected ?string $error = null;

    /**
     * Fxg24 constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'];
        $this->token = $configuration['token'];
    }

    /**
     * Dispatch lead to the destination
     *
     * @param \App\Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function send(Lead $lead): void
    {
        $response = Http::asJson()->post($this->url, [
            'token'          => $this->token,
            'method'         => 'registration',
            'user'           => [
                'first_name'     => $lead->firstname,
                'last_name'      => $lead->lastname ?? $lead->middlename,
                'email'          => $lead->email ?? Str::slug($lead->fullname, '') . rand(1, 99) . '@gmail.com',
                'phone'          => $lead->formatted_phone,
                'source_id'      => 1,
                'country_iso3'   => $lead->ipAddress->country_code_iso3 ?? 'RUS',
                'campaign_name'  => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'click_id'       => $lead->clickid,
            ],
        ])
            ->throw()
            ->json();

        if ($response['success'] !== true) {
            $this->error = json_encode($response['message']);
        } else {
            $this->isDelivered = true;
        }
    }


    /**
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isDelivered;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return null;
    }
}
