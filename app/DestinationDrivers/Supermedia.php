<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Supermedia implements DeliversLeadToDestination
{
    /**
     * Auth token
     *
     * @var string|mixed
     */
    protected string $token;
    protected bool $isDelivered = false;
    protected ?string $error    = null;
    protected array $response   = [];

    /**
     * Supermedia constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $code = $lead->lookup->prefix;

        $response = Http::asForm()
            ->withHeaders([
                'Token' => $this->token,
            ])->post('https://ss2701api.com/v3/affiliates/lead/create', [
                'firstname'    => Str::limit($lead->firstname, 40, null),
                'lastname'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'        => $lead->getOrGenerateEmail(),
                'password'     => sprintf("Sa%s%s", Str::random(6), rand(10, 99)),
                'phone'        => substr($lead->formatted_phone, strlen($code)),
                'ip'           => $lead->ip ?? '127.0.0.1',
                'area_code'    => $code,
                'country_code' => optional($lead->ipAddress)->country_code_iso3 ?? optional($lead->lookup)->country ?? 'CL',
                'source'       => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'aff_sub'      => $lead->uuid,
                'referrer_url' => $lead->domain,
            ]);

        if (! $response->ok()) {
            $this->error = $response->body();

            return;
        }

        if ($response->offsetExists('status') && $response->offsetGet('status') == true) {
            $this->isDelivered = true;
            $this->response    = $response->json();

            return;
        }

        $this->error = $response->body();
    }

    public function isDelivered(): bool
    {
        return $this->isDelivered;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->response['result']['url'] ?? null;
    }

    public function getExternalId(): ?string
    {
        return $this->response['result']['lead_id'] ?? null;
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
            'Token' => $this->token,
        ])->get('https://api.sdkapilead.com/v3/affiliates/leads', [
            'created_from'   => $startDate->toIso8601String(),
            'created_to'     => now()->toIso8601String(),
            'has_conversion' => true
        ])->throw()->json();
    }
}
