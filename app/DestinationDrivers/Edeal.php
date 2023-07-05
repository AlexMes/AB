<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Edeal implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://edjax.pro/api/v3/customers/gjAQeyVJhlQK';
    protected string $token;
    protected ?string $language;

    /**
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;


    /**
     * Edeal constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'];
        $this->token    = $configuration['token'];
        $this->language = $configuration['lang'] ?? null;
    }

    /**
     * Collect sales results from the API
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(data_get(Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Api-Auth'   => $this->token
        ])->get('https://fetch.edeal.io/api/v3/customers', [
            'date_from' => $since->format('Y-m-d'),
            'limit'     => 500,
            'skip'      => --$page * 500,
        ])->throw()->json(), 'data.items'))->map(fn ($item) => new CallResult([
            'id'        => $item['unique_id'],
            'status'    => $item['status_name'],
            'isDeposit' => false,
        ]));
    }

    /**
     * Send lead to destination
     *
     * @param \App\Lead $lead
     */
    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Api-Auth'   => $this->token
        ])->post($this->url, [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'      => $lead->getOrGenerateEmail(),
            'phone'      => $lead->formatted_phone,
            'country'    => optional($lead->ipAddress)->country_code ?? 'CL',
            'password'   => Str::random(5) . rand(10, 999),
            'language'   => $this->language ?? $this->getLanguage($lead),
            'ip'         => $lead->ip ?? '127.0.0.1',
            'utm_source' => $lead->domain.'?utm_source=1',
            'clickid'    => $lead->uuid,
            'quiz_info'  => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $s) => ['question' => $s->getQuestion(), 'answer' => $s->getAnswer(),])->toArray() : null,
        ]);
    }

    /**
     * Get lead language
     *
     * @param \App\Offer $offer
     *
     * @return string
     */
    public function getLanguage(Lead $lead)
    {
        if (in_array($lead->offer->id, [406,398])) {
            return 'AR';
        }

        if (in_array(optional($lead->lookup)->country, ['LT','LV','EE','RU'])) {
            return 'RU';
        }

        if (Str::contains($lead->offer->name, ['GAZ','SWEDBANK','MASTERCASHEU','MAGELAN','SAXO','TNK','ALFA','TON','RUEU'])) {
            return 'RU';
        }

        if (Str::endsWith($lead->offer->name, 'PL') || Str::contains($lead->offer->name, ['ORLEN','POLSKA'], )) {
            return 'PL';
        }

        if (Str::endsWith($lead->offer->name, 'CL', 'MX', 'PE', 'SL', 'BR')) {
            return 'ES';
        }

        return 'EN';
    }

    /**
     * Determine is delivery ok
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->response->offsetGet('status') === 200;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'data.autologin_url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.unique_id');
    }

    /**
     * @param CarbonInterface      $startDate
     * @param CarbonInterface|null $endDate
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectFtdSinceDate(CarbonInterface $startDate)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Api-Auth'   => $this->token
        ])->get('https://fetch.edeal.io/api/v3/customers', [
            'date_from' => $startDate->format('Y-m-d'),
            'ftd'       => 1,
        ])->throw()->json();
    }
}
