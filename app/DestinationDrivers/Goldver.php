<?php

namespace App\DestinationDrivers;

use App\Exceptions\DeliveryFailed;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Goldver implements Contracts\DeliversLeadToDestination
{
    protected string $baseUrl = 'https://crm.capitalinfocus.com/api/users';
    protected string $campaignId;
    protected bool $isSuccessful = false;
    protected string $token;
    protected ?string $error      = null;
    protected ?string $externalId = null;

    /**
     * @inheritDoc
     */
    public function __construct($configuration = null)
    {
        $this->campaignId = $configuration['campaign'];
        $this->token      = $configuration['token'];
    }

    /**
     * @inheritDoc
     */
    public function send(Lead $lead): void
    {
        $response = Http::asForm()
            ->post(sprintf("%s/trading-platform/create-client?api_key=%s", $this->baseUrl, $this->token), [
                'first_name'    => $lead->firstname,
                'last_name'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'phone'         => $lead->formatted_phone,
                'email'         => $lead->getOrGenerateEmail(),
                'country'       => optional($lead->ipAddress)->country_name ?? 'Russia',
                'currency_code' => optional($lead->ipAddress)->currency ?? 'RUB',
                'gmt_timezone'  => '03:00',
                'campaign_id'   => $this->campaignId,
                'password'      => Str::random(12),
            ]);

        if (! $response->ok()) {
            $this->error = $response->body();

            throw new DeliveryFailed($response->body());
        }

        if ($response->ok()) {
            $this->isSuccessful = true;
            $this->externalId   = $response->offsetGet('client_id');
        }
    }

    /**
     * @inheritDoc
     */
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @inheritDoc
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
