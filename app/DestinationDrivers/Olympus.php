<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Olympus implements DeliversLeadToDestination
{
    protected string $baseUrl = 'https://crm.olympusholding.co';
    protected string $apiKey;
    protected string $campaignId;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $externalId = null;

    /**
     * Olympus constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        $this->apiKey     = $configuration['apiKey'];
        $this->campaignId = $configuration['campaignId'];
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
        $response = Http::asForm()
            ->post(sprintf("%s/api/users/trading-platform/create-client?api_key=%s", $this->baseUrl, $this->apiKey), [
                'country'       => $lead->ipAddress->country_name ?? 'Russia',
                'currency_code' => 'USD',
                'email'         => $lead->getOrGenerateEmail(),
                'first_name'    => $lead->firstname ?? $lead->fullname,
                'last_name'     => $lead->lastname ?? $lead->middlename ?? 'none',
                'phone'         => $lead->formatted_phone,
                'gmt_timezone'  => '03:00',
                'password'      => Str::random(8),
                'lang'          => 'RU',
                'campaign_id'   => $this->campaignId,
                'free_text'     => $lead->domain
            ]);

        if (! $response->ok()) {
            $this->error = $response->getBody();

            return;
        }

        if ($response->successful() && $response['client_id'] !== null) {
            $this->isSuccessful = true;
            $this->externalId   = $response['client_id'];
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
     * @param \Carbon\Carbon $startDate
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectFtdSinceDate(Carbon $startDate)
    {
        return Http::get(sprintf('%s/api/users/campaigns/clients-registrations', $this->baseUrl), [
            'api_key'     => $this->apiKey,
            'campaign_id' => $this->campaignId,
            'startDate'   => $startDate->toIso8601String(),
            'has_ftd'     => true,
        ])->throw()->json();
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
