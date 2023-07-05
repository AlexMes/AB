<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EuroHot implements DeliversLeadToDestination
{
    protected string $baseUrl = 'https://api-partners.eurostandarte.com/v1';
    protected int $affiliateId;
    protected string $partnerId;

    protected bool $isSuccessful = false;

    protected ?string $error = null;

    protected ?string $externalId = null;

    /**
     * Olympus constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        $this->affiliateId = $configuration['affiliateId'];
        $this->partnerId   = $configuration['partnerId'];
    }

    /**
     * Send lead to destination
     *
     * @param \App\Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $attributes = [
            'affiliateID' => $this->affiliateId,
            'email'       => $lead->getOrGenerateEmail(),
            'firstName'   => $lead->firstname ?? 'Unknown',
            'lastName'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'       => $lead->formatted_phone,
            'language'    => 'RU',
            'country'     => 'RU',
            'ip'          => $lead->ip,
            'domain'      => $lead->domain,
            'campaign'    => Str::before(optional(optional($lead->offer)->getOriginalCopy())->name, '_'),
        ];
        $response = Http::asForm()
            ->post(
                sprintf("%s/lead/create", $this->baseUrl),
                array_merge($attributes, ['checksum' => $this->checksum($attributes)])
            )->throw();

        if ($response->successful() && $response->offsetGet('returnCode') === 1) {
            $this->isSuccessful = true;

            $this->externalId = $response->offsetGet('data')['leadID'];
        } else {
            $this->error = $response->body();
        }
    }

    /**
     * Determine is lead delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @param array $attributes
     *
     * @return array|mixed
     */
    public function checksum(array $attributes)
    {
        $str = $this->partnerId . http_build_query($attributes);

        return Str::upper(md5($str));
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
        return $this->externalId;
    }
}
