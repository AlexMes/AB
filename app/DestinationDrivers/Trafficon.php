<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Services\MessageBird\MessageBird;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Trafficon implements DeliversLeadToDestination
{
    /**
     * @var mixed
     */
    protected $offerId = 285;

    /**
     * @var mixed
     */
    protected $affiliateId = 2257;

    protected ?string $error      = null;
    protected ?string $externalId = null;

    /**
     * @var array|mixed
     */
    protected array $response = [];

    /**
     * Crm constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        $this->affiliateId = $configuration['aff_id'];
        $this->offerId     = $configuration['offer_id'];
    }

    /**
     * Dispatch lead to destination
     *
     * @param \App\Lead $lead
     */
    public function send(Lead $lead): void
    {
        $code = $this->getPhoneCode($lead->formatted_phone);

        $payload = [
            'offer_id'                   => $this->offerId,
            'aff_id'                     => $this->affiliateId,
            'first_name'                 => $lead->firstname,
            'last_name'                  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'                      => $lead->getOrGenerateEmail(),
            'password'                   => sprintf("%s%s", Str::random(10), rand(10, 99)),
            'area_code'                  => sprintf("+%s", $code),
            'ip'                         => $lead->ip ?? '127.0.0.1',
            'phone'                      => substr($lead->formatted_phone, strlen($code)),
            'country'                    => optional($lead->ipAddress)->country ?? 'Russia',
            'iso'                        => optional($lead->ipAddress)->country_code_iso3 ?? optional($lead->lookup)->country ?? 'RU',
            'aff_sub1'                   => $lead->uuid,
            'aff_sub2'                   => ($lead->hasPoll() && in_array($lead->offer_id, [817])) ? $this->answers($lead) : '',
            'offer_name'                 => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'offer_url'                  => $lead->domain,
            'offer_description'          => 'Bicoin Code',
            'offer_description_override' => $lead->hasPoll() ? $this->answers($lead) : $lead->offer->description,
        ];

        $response = Http::post('http://trafficon-api.com/secured-registration', $payload);

        if ($response->status() !== 200) {
            $this->error = $response->body();

            return;
        }

        if ($response->offsetExists('status') && $response->offsetGet('status') === 'error') {
            $this->error = $response->body();

            return;
        }

        $this->response = $response->json();
    }

    protected function answers(Lead $lead)
    {
        if ($lead->offer_id === 817) {
            return sprintf('Воронка Тинькофф обучение трейдингу, ответы на опрос:  %s', $lead->getPollAsUrl());
        }

        return $lead->getPollAsUrl();
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

        $result = $service->lookup($phone);

        return $result['countryPrefix'];
    }

    public function isDelivered(): bool
    {
        return $this->response !== [];
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
        return $this->isDelivered()
            ? sprintf("http://somedomain.com/api/v1/secured-auto-login/%s", $this->response['token'] ?? 'w')
            : null;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->isDelivered()
            ? $this->response['tid']
            : null;
    }
}
