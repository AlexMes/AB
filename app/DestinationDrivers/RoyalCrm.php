<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Services\MessageBird\MessageBird;
use Illuminate\Support\Facades\Http;
use Str;
use Throwable;

class RoyalCrm implements DeliversLeadToDestination
{
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $code = $this->getPhoneCode($lead->formatted_phone);

        if ($code === null) {
            return;
        }

        $this->response = Http::asForm()->post('https://crmroyal.com/api/addLead', [
            'token'       => $this->token,
            'firstName'   => $lead->firstname,
            'lastName'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'ip'          => $lead->ip,
            'phoneprefix' => sprintf("+%s", $code),
            'phonenumber' => substr($lead->formatted_phone, strlen($code)),
            'email'       => $lead->getOrGenerateEmail(),
            'password'    => Str::random(10).rand(10, 99),
            'from'        => $this->getDomain($lead),
        ]);
    }

    protected function getDomain(Lead $lead)
    {
        if ($lead->offer_id === 447) {
            return 'https://fashionfriends.dk/';
        }

        if ($lead->offer_id === 471) {
            return 'https://syst.autoprogram-trade.site/';
        }

        return $lead->domain;
    }

    /**
     * Get country code from the phone number
     *
     * @param string $phone
     *
     * @return mixed
     */
    protected function getPhoneCode(string $phone)
    {
        $service = new MessageBird(config('services.messagebird.key'));

        try {
            $result = $service->lookup($phone);

            return $result['countryPrefix'];
        } catch (Throwable $exception) {
            $this->error        = $exception->getMessage();
            $this->isSuccessful = false;

            return null;
        }

        return null;
    }

    /**
     * Determine is lead delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        if ($this->response === null) {
            return false;
        }

        return $this->response->successful() && $this->response->offsetGet('status') === 'success';
    }

    /**
     * Get response error
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        if ($this->response === null) {
            return null;
        }

        return $this->response->body();
    }

    /**
     * Autologin url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        if ($this->response === null) {
            return null;
        }

        return $this->response->offsetExists('link') ? $this->response->offsetGet('link') : null;
    }

    /**
     * Get external id for the lead
     *
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        if ($this->response === null) {
            return null;
        }

        return $this->response->offsetExists('leadId') ? $this->response->offsetGet('leadId') : null;
    }
}
