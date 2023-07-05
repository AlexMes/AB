<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Offer;
use App\Services\MessageBird\MessageBird;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ConvertingTeam implements DeliversLeadToDestination, CollectsCallResults
{
    protected $offerId;
    protected $affiliateId;
    protected $language;

    /**
     * Undocumented variable
     *
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;

    public function __construct($configuration = null)
    {
        $this->offerId     = $configuration['offer'];
        $this->affiliateId = $configuration['affiilate'];
        $this->language    = $configuration['lang'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $response = Http::get('https://api-brokers.com/lead', [
            'page' => $page,
        ])->json();

        return collect($response['leads'])->map(fn ($item) => new CallResult([
            'id'        => $item['regId'],
            'status'    => $item['status'],
            'isDeposit' => $item['deposit'],
        ]));
    }

    public function send(Lead $lead): void
    {
        $code = $this->getPhoneCode($lead->formatted_phone);

        if ($code === null) {
            return;
        }

        $this->response = Http::withHeaders([
            'X-Forwarded-For' => $lead->ip,
            'Content-Type'    => 'application/json'
        ])
            ->post('https://api-brokers.com/registration', [
                'firstName'       => Str::limit($lead->firstname, 45, null),
                'lastName'        => Str::limit($lead->lastname ?? 'Unknown', 45, null),
                'email'           => $lead->getOrGenerateEmail(),
                'password'        => sprintf("%s%s", Str::random(10), rand(10, 99)),
                'phonecc'         => sprintf("+%s", $code),
                'phone'           => substr($lead->formatted_phone, strlen($code)),
                'project'         => 'apicrypto',
                'lang'            => $this->language ?? 'RU',
                'a'               => $this->affiliateId,
                'o'               => $this->offerId,
                'agreementBroker' => true,
                'offerName'       => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'offerUrl'        => $lead->domain.'?utm_source=1',
            ]);
    }

    /**
     * get lead language
     *
     * @param \App\Offer $offer
     *
     * @return string
     */
    protected function getLang(Offer $offer)
    {
        if (in_array($offer->id, [406,398])) {
            return 'ar';
        }
        if (Str::endsWith($offer->name, 'CL', 'MX', 'PE', 'SL', 'ES', 'BR')) {
            return 'es';
        }
        if (Str::endsWith($offer->name, 'NL') || Str::contains($offer->name, 'YUANPAYNL')) {
            return 'nl';
        }

        return 'en';
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

            return $result['countryPrefix'] ?? null;
        } catch (Throwable $exception) {
            return null;
        }

        return null;
    }

    /**
     * determine is lead delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        if ($this->response === null) {
            return false;
        }

        return $this->response->status() === 201;
    }

    /**
     * get request error
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->response === null ? null : Str::limit($this->response->body(), 250);
    }

    /**
     * get autologin url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'target.url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'regId');
    }
}
