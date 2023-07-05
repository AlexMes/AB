<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Trail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class Aivix implements DeliversLeadToDestination, CollectsCallResults
{
    public bool $nullInterval = false;
    protected $url;
    protected $urlGet = "http://tracker.aivix.com";
    protected $id;
    protected $sub;
    protected $response;
    protected $token;

    public function __construct($configuration = null)
    {
        $this->url    = $configuration['url'];
        $this->urlGet = $configuration['url_get'] ?? $this->urlGet;
        $this->id     = $configuration['id'];
        $this->token  = $configuration['token'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        if (!$this->token) {
            return collect();
        }

        $since = $since->max(now()->subMonths(2)->toDateString())->toImmutable();

        $response = data_get(Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post($this->urlGet . '/api/get_client_conversions', [
            "date" => $since->addDays($page - 1)->toDateString()
        ])->json(), 'data');

        $this->nullInterval = empty($response) && $since->addDays($page - 1)->lessThanOrEqualTo(now());

        return collect($response)->map(function ($item) {
            return new CallResult([
                'id'          => $item['id'],
                'status'      => $item['lead_status'],
                'isDeposit'   => $item['conversion_type'] === 'Conversion',
                'depositDate' => Carbon::parse($item['ts'])->toDateString(),
                'depositSum'  => '151',
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withOptions([
            'verify'          => false,
            'allow_redirects' => [
                'on_redirect' => fn ($r, $s, $uri) => app(Trail::class)->add('Got redirect to ' . $uri),
            ],
        ])->get(sprintf('%s/tracker', $this->url), $payload = [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'      => $lead->getOrGenerateEmail(),
            'password'   => Str::random(9) . rand(10, 99),
            'aff_id'     => $this->id,
            'aff_sub3'   => $lead->offer->description ?? $lead->domain . '?utm_source=1',
            'offer_id'   => 1737,
            'phonecc'    => sprintf("+%s", $lead->lookup->prefix),
            'phone'      => substr($lead->formatted_phone, strlen($lead->lookup->prefix)),
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * Determine is lead was delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->response->ok() && data_get($this->response->json(), 'success');
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->response->body();
    }

    /**
     * Get redirect url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'redirect');
    }

    /**
     * Get external id
     *
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }
}
