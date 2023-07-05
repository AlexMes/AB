<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Affclub implements DeliversLeadToDestination, CollectsCallResults
{
    protected $token;
    protected string $url     = "https://af1402api.com";
    public bool $nullInterval = false;
    /**
     * Undocumented variable
     *
     * @var \Illuminate\Http\Client\Response
     */

    protected $referrerUrl;
    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->token       = $configuration['token'];
        $this->referrerUrl = $configuration['referrer_url'] ?? null;
        $this->url         = $configuration['url'] ?? $this->url;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()
            ->withHeaders([
                'Token' => $this->token,
            ])->post($this->url . '/v3/affiliates/lead/create', [
                'firstname'    => $lead->firstname,
                'lastname'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'        => $lead->getOrGenerateEmail(),
                'password'     => Str::random(10).rand(10, 99),
                'country_code' => optional($lead->ipAddress)->country_code ?? 'NL',
                'area_code'    => sprintf("+%s", $lead->lookup->prefix),
                'phone'        => substr($lead->phone, strlen($lead->lookup->prefix)),
                'referrer_url' => $this->referrerUrl ?? $lead->domain,
                'ip'           => $lead->ip,
                'aff_sub'      => $lead->uuid
            ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->ok();
    }

    public function getError(): ?string
    {
        return Str::limit($this->response->body(), 250);
    }

    public function getRedirectUrl(): ?string
    {
        if ($this->response->offsetExists('result')) {
            return $this->response->offsetGet('result')['url'] ?? null;
        }

        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->response->offsetExists('result')) {
            return $this->response->offsetGet('result')['lead_id'] ?? null;
        }

        return null;
    }

    /**
     * @param CarbonInterface      $startDate
     * @param CarbonInterface|null $endDate
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectFtdSinceDate(CarbonInterface $startDate, CarbonInterface $endDate = null)
    {
        return Http::withHeaders([
            'Token' => $this->token,
        ])->get($this->url . '/v3/affiliates/leads', [
            'created_from'   => $startDate->format('Y-m-d'),
            'created_to'     => ($endDate ?? now())->format('Y-m-d'),
            'has_conversion' => 1,
        ])->throw()->json();
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        $response = data_get(Http::withHeaders([
            'Token' => $this->token,
        ])->get($this->url . '/v3/affiliates/leads', [
            'created_from'   => $since->addDays(28 * ($page - 1))->format('Y-m-d'),
            'created_to'     => $since->addDays(28 * $page)->format('Y-m-d'),
            'has_conversion' => 0,
        ])->json(), 'result');

        $this->nullInterval = empty($response) && $since->addDays(28 * $page)->endOfDay()->lessThanOrEqualTo(now());

        return collect($response)->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['sale_status'],
        ]));
    }
}
