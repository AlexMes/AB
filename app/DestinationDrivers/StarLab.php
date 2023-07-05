<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StarLab implements DeliversLeadToDestination
{
    protected string $baseUrl = 'https://starlab-app.io/api';
    protected int $userId;
    protected string $hash;
    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $externalId = null;

    /**
     * UnitedMarkets constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->userId = $configuration['user_id'];
        $this->hash   = $configuration['hash'];
    }

    /**
     * Deliver lead to united markets
     *
     * @param \App\Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Throwable
     */
    public function send(Lead $lead): void
    {
        $response = Http::asForm()->post(sprintf("%s/v1/lead/create", $this->baseUrl), [
            'user_id'    => $this->userId,
            'hash'       => $this->hash,
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'None',
            'email'      => $lead->getOrGenerateEmail(),
            'phone'      => $lead->formatted_phone,
            'language'   => 'RU',
            'target_geo' => 'RU',
            'offer'      => Str::before(optional(optional($lead->offer)->getOriginalCopy())->name, '_'),
            'sub'        => $lead->domain,
        ])->throw();

        if ($response->ok()) {
            if (
                $response->offsetExists('errors')
                || ($response->offsetExists('success') && $response->offsetGet('success') === false)
            ) {
                $this->error = $response->getBody();

                return;
            }
        }

        if ($response->offsetGet('success') == true) {
            $this->isSuccessful = true;
            $this->externalId   = $response->json()['credentials']['id'];
        }
    }

    /**
     * Determines is lead delivered to destination
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
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
