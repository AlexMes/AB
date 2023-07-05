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

class HugeOffers implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $baseUrl = 'https://track.uclicknow.com/lds/affiliate/registration';
    protected string $token;
    protected ?string $bearerToken;
    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $externalId = null;
    protected $redirect           = null;
    protected $cc;

    public function __construct($configuration = null)
    {
        $this->baseUrl     = $configuration['url'] ?? 'https://track.uclicknow.com/lds/affiliate/registration';
        $this->token       = $configuration['token'];
        $this->bearerToken = $configuration['bearer_token'] ?? null;
        $this->cc          = $configuration['cc'] ?? null;
    }

    /**
     * Collect statuses from the API
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withToken($this->bearerToken)->get('https://api.hugeoffers.com/rest/affiliate/leads', [
            'date_from' => $since->toDateString(),
            'date_to'   => now()->toDateString(),
            'per-page'  => 1000,
            'page'      => $page
        ])->throw()->offsetGet('data'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['click_id'],
                'status'      => $item['lead_status'],
                'isDeposit'   => in_array($item['lead_status'], ['Depositor']),
            ]);
        });
    }

    /**
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $code = $lead->lookup->prefix;

        if ($code === null) {
            return;
        }

        $response = Http::post(sprintf("%s?lds-token=%s", $this->baseUrl, $this->token), $payload = [
            'firstname'       => Str::limit($lead->firstname, 30, null) ?? 'Unknown',
            'lastname'        => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'           => $lead->getOrGenerateEmail(),
            'password'        => sprintf("%s%s", Str::random(10), rand(10, 99)),
            'phone_code'      => sprintf("+%s", $code),
            'phone_number'    => substr($lead->formatted_phone, strlen($code)),
            'registration_ip' => $lead->ip ?? '127.0.0.1',
            'p4'              => Str::before(optional(optional($lead->offer)->getOriginalCopy())->name, '_'),
            'tc'              => 'FB',
            'sub5'            => sprintf('%s/%s', Str::before($lead->offer->getOriginalCopy()->name, '_'), $lead->domain),
        ]);

        $lead->addEvent('payload', $payload);
        if ($response->status() !== 200) {
            $this->error = $response->body();

            return;
        }

        try {
            $this->externalId = $response->offsetGet('click_id');
        } catch (Throwable $exception) {
            $this->error = 'Unable to extract click id';

            return;
        }

        if ($response->offsetGet('status') === 3) {
            $this->isSuccessful = true;
            $this->redirect     = $response->offsetGet('redirect_url');
        } else {
            $this->error = $response->body();
        }
    }

    /**
     * Get handling call-center language
     *
     * @param \App\Offer $offer
     *
     * @return void
     */
    protected function getCcLang(Offer $offer)
    {
        if ($offer->vertical === Offer::VERTICAL_CRYPT) {
            return 'RU';
        }

        if (in_array($offer->id, [406,398])) {
            return 'AR';
        }
        if (Str::endsWith($offer->name, 'CL', 'MX', 'PE', 'SL', 'ES', 'BR')) {
            return 'ES';
        }
        if (Str::endsWith($offer->name, 'NL') || Str::contains($offer->name, 'YUANPAYNL')) {
            return 'NL';
        }

        return 'EN';
    }

    public function isDelivered(): bool
    {
        return $this->isSuccessful;
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
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
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
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->get('https://api.hugeoffers.com/rest/affiliate/ftds', [
            'click_date_from' => $startDate->subMonth()->toIso8601String(),
            'click_date_to'   => now()->toIso8601String(),
        ])->throw()->json();
    }

    /**
     * Collect leads statuses
     *
     * @param \Carbon\Carbon $startDate
     * @param mixed          $page
     *
     * @return void
     */
    public function collectStatusesSince(Carbon $startDate, $page = 1)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->get('https://api.hugeoffers.com/rest/affiliate/leads', [
            'date_from' => $startDate->toDateString(),
            'date_to'   => now()->toDateString(),
            'per-page'  => 500,
            'page'      => $page
        ])->throw()->offsetGet('data');
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirect;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
