<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use URL;

class Odixen implements DeliversLeadToDestination
{
    protected string $registrationUrl;
    protected string $activationUrl;
    protected ?string $apiKey;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $link       = null;
    protected ?string $externalId = null;
    protected $deskId;
    protected $statusId;
    protected $responsibleId;


    /**
     * AffBoat constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->apiKey          = $configuration['api_key'] ?? null;
        $this->registrationUrl = $configuration['url'].'/api/v_2/page/RegisterUser';
        $this->activationUrl   = $configuration['url'].'/api/v_2/page/Activation';
        $this->deskId          = $configuration['desk_id'] ?? null;
        $this->statusId        = $configuration['status_id'] ?? null;
        $this->responsibleId   = $configuration['responsible'] ?? null;
    }

    /**
     * @param \App\Lead $lead
     *
     * @throws \Throwable
     */
    public function send(Lead $lead): void
    {
        $registrationResponse = $this->registerUser($lead);

        if (! $registrationResponse->successful() && $registrationResponse->offsetExists('values')) {
            $this->error = $registrationResponse->body();

            return;
        }

        $activationResponse = $this->activateUser($registrationResponse->offsetGet('values'));

        if ($registrationResponse->successful() && $activationResponse->offsetGet('result') === 'success') {
            $this->isSuccessful = true;
        } else {
            $this->error = $activationResponse->body();

            return;
        }
    }

    /**
     * 1st stage of registration
     *
     * @param Lead $lead
     *
     * @return \Illuminate\Http\Client\Response
     */
    protected function registerUser(Lead $lead)
    {
        $randParam = Str::random();

        return Http::asForm()->post($this->registrationUrl, [
            'rand_param'      => $randParam,
            'key'             => md5($this->apiKey . $randParam),
            'login'           => $lead->getOrGenerateEmail(),
            'password'        => 'As@132123',
            'password_repeat' => 'As@132123',
            'email'           => $lead->getOrGenerateEmail(),
            'first_name'      => $lead->firstname,
            'second_name'     => $lead->lastname ,
            'phone'           => sprintf("+%s", $lead->formatted_phone),
            'send_email'      => 0,
            'campaign_id'     => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'desk_id'         => $this->deskId,
            'status_id'       => $this->statusId,
            'responsible'     => $this->responsibleId
        ]);
    }

    /**
     * 2nd stage of registration
     *
     * @param array|mixed $data
     *
     * @return \Illuminate\Http\Client\Response
     */
    protected function activateUser($data)
    {
        $randParam = Str::random();

        return Http::get($this->activationUrl, [
            'rand_param'      => $randParam,
            'key'             => md5($this->apiKey . $randParam),
            'activation_key'  => $data['activation_key'],
            'activation_type' => $data['activation_type'],
        ]);
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
     * Get redirect url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
        // return $this->link;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
