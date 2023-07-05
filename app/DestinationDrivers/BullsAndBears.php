<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Offer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class BullsAndBears implements DeliversLeadToDestination, CollectsCallResults
{
    /**
     * Undocumented variable
     *
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;
    protected $login;
    protected $password;
    protected $url;

    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'];
        $this->login    = $configuration['login'];
        $this->password = $configuration['password'];
    }

    /**
     * Collect postback results from the API
     *
     * @param \Carbon\Carbon $since
     * @param int            $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withHeaders([
            'login'    => $this->login,
            'password' => $this->password,
        ])->get($this->url, [
            'registrationDateFrom' => $since->format('Y-m-d'),
            'registrationDateTo'   => now()->format('Y-m-d'),
            'page'                 => $page
        ])->offsetGet('content'))->map(fn ($item) => new CallResult([
            'id'          => $item['clientUUID'],
            'status'      => $item['salesStatus'] ?? 'Unknown',
            'isDeposit'   => array_key_exists('firstDeposit', $item) ? $item['firstDeposit'] : false,
            'depositDate' => $item['firstDepositDate'] ?? null,
            'depositSum'  => '151',
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'login'        => $this->login,
            'password'     => $this->password,
        ])
            ->post($this->url, [
                'firstName' => $lead->firstname,
                'lastName'  => $lead->lastname ?? 'Unknown',
                'country'   => optional($lead->ipAddress)->country_code ?? 'CL',
                'email'     => $lead->getOrGenerateEmail(),
                'password'  => Str::random(12).rand(10, 99),
                'language'  => $this->getLanguage($lead->offer),
                'source'    => $this->source($lead),
                'phone'     => $lead->formatted_phone,
                'currency'  => 'USD',
                'ip'        => $lead->ip,
                'referral'  => $lead->hasPoll() ? $lead->getPollAsUrl() : '',

            ]);
    }

    /**
     * Get source for the lead
     *
     * @param \App\Lead $lead
     *
     * @return void
     */
    protected function source(Lead $lead)
    {
        if (optional($lead->user)->branch_id === 19) {
            return Str::before($lead->offer->name, '_');
        }

        return $lead->domain.'?utm_source=t';
    }

    protected function getLanguage(Offer $offer)
    {
        if (in_array($offer->id, [406,398])) {
            return 'ar';
        }
        if (Str::endsWith(Str::before($offer->name, '_'), 'CL') || in_array($offer->name, ['DGTLPESOCL_QUIZ','YUANPAYCL_QUIZ_SBK'])) {
            return 'es';
        }
        if ((Str::endsWith($offer->name, 'ZA'))) {
            return 'en';
        }

        if ((Str::contains($offer->name, 'PL'))) {
            return 'pl';
        }

        if (in_array($offer->name, ['BITCOINITL'])) {
            return 'it';
        }

        if ($offer->name === 'BTCERAIN') {
            return 'en';
        }

        return 'ru';
    }

    public function isDelivered(): bool
    {
        return $this->response->ok() && $this->response->offsetExists('profileUUID');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return  $this->response->offsetExists('redirectUrl') ? $this->response->offsetGet('redirectUrl') : null;
    }

    public function getExternalId(): ?string
    {
        return  $this->response->offsetExists('profileUUID') ? $this->response->offsetGet('profileUUID') : null;
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
            'login'    => $this->login,
            'password' => $this->password,
        ])->get($this->url, [
            'registrationDateFrom' => $startDate->format('Y-m-d'),
            'registrationDateTo'   => now()->format('Y-m-d'),
            'firstDeposit'         => true,
        ])->offsetGet('content');
    }

    /**
     * @param \Carbon\Carbon $startDate
     * @param mixed          $page
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectStatusesSince(Carbon $startDate, $page = 1)
    {
        return Http::withHeaders([
            'login'    => $this->login,
            'password' => $this->password,
        ])->get($this->url, [
            'registrationDateFrom' => $startDate->format('Y-m-d'),
            'registrationDateTo'   => now()->format('Y-m-d'),
            'page'                 => $page
        ])->offsetGet('content');
    }
}
