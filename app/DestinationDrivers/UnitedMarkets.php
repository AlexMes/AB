<?php


namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UnitedMarkets implements DeliversLeadToDestination
{
    protected string $baseUrl    = 'https://unitedmarkets.io/simple';
    protected string $sid        = 'CPA';
    protected bool $isSuccessful = false;
    protected ?string $error     = null;

    /**
     * UnitedMarkets constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        // Dont event bother with configurations
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
        $response = Http::get($this->baseUrl, [
            'f_name'       => $lead->firstname ?? 'Unknown',
            'l_name'       => $lead->lastname ?? $lead->middlename ?? 'None',
            'email'        => $lead->getOrGenerateEmail(),
            'phone'        => $lead->formatted_phone,
            'lang'         => 'RU',
            'lp'           => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'lp_url'       => $lead->domain,
            'country'      => optional($lead->ipAddress)->country_code ?? 'RU',
            't_geo'        => 'RU',
            'ip'           => $lead->ip ?? '127.0.0.1',
            'unique_token' => $lead->uuid,
            'sid'          => $this->sid
        ])->throw();

        if (array_key_exists('error', $response->json())) {
            $this->error = $response->offsetGet('error');

            return;
        }

        if (array_key_exists('success', $response->json())) {
            $this->isSuccessful = filter_var($response->json()['success'], FILTER_VALIDATE_BOOL);
        }
    }

    /**
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
        return null;
    }
}
