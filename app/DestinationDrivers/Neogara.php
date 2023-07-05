<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use URL;

class Neogara implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://admin.neogara.com';
    protected ?string $key;
    protected ?int $offerId;
    protected ?string $ref;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $link       = null;
    protected ?string $externalId = null;

    /**
     * AffBoat constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->key     = $configuration['key'] ?? null;
        $this->offerId = $configuration['offer_id'] ?? null;
        $this->ref     = $configuration['ref'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        if ($this->key === null) {
            return collect();
        }

        return collect(Http::get('https://admin.neogara.com/api/statuses', [
            'apiKey'      => $this->key,
            'page'        => $page,
            'limit'       => 100,
            'createdFrom' => $since->startOfDay()->toDateTimeString(),
        ])->offsetGet('data'))->map(fn ($item) => new CallResult([
            'id'     => $item['id'],
            'status' => $item['status']
        ]));
    }

    /**
     * @param \App\Lead $lead
     *
     * @throws \Throwable
     */
    public function send(Lead $lead): void
    {
        $response = Http::asJson()->acceptJson()->post(sprintf('%s/api/lid?apiKey=%s', $this->url, $this->key), [
            'offerId'   => $this->offerId,
            'firstname' => $lead->firstname ?? $lead->fullname,
            'lastname'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'     => $lead->getOrGenerateEmail(),
            'phone'     => sprintf("+%s", $lead->formatted_phone),
            'ref'       => $this->ref ?? sprintf('%s?utm_source=1', $lead->domain),
            'ip'        => $lead->ip ?? '127.0.0.1',
            'country'   => $this->getCode($lead),
            'city'      => optional($lead->ipAddress)->city,
        ]);

        if ($response->successful() && $response->offsetExists('result')) {
            $this->isSuccessful = true;
            $this->link         = $response->offsetExists('cabinetUrl')
                ? $response->offsetGet('cabinetUrl')
                : null;
            $this->externalId = $response->offsetGet('lidId');
        } else {
            \Log::error('Lead assignment delivery failed', $response->json());
            $this->error = is_array($response->offsetGet('message'))
                ? $response->offsetGet('message')[0]
                : $response->offsetGet('message');
        }
    }

    protected function getCode(Lead $lead)
    {
        if (Str::contains($lead->offer->name, 'YUANPAYNL')) {
            return 'NL';
        }

        if (Str::contains($lead->offer->name, 'ORLEN')) {
            return 'PL';
        }

        return optional($lead->lookup)->country ?? substr($lead->offer->name, -2);
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
        return $this->link;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
