<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\IpAddress;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ClickMate implements DeliversLeadToDestination
{
    protected string $baseUrl = 'https://bitcoin-code.link/users/ajax';
    protected string $hash;
    protected ?string $bearerToken;
    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $externalId = null;

    public function __construct($configuration = null)
    {
        $this->hash        = $configuration['hash'];
        $this->bearerToken = $configuration['bearer_token'] ?? null;
    }

    /**
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $response = Http::post(sprintf("%s/%s", $this->baseUrl, $this->hash), [
            'name'            => $lead->firstname ?? 'Unknown',
            'lastname'        => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'           => $lead->getOrGenerateEmail(),
            'phone'           => $lead->formatted_phone,
            'country_id'      => optional(IpAddress::whereIp($lead->ip)->first())->country_code ?? 'RU',
            'password'        => sprintf("%s%s", Str::random(10), rand(10, 99)),
            'language'        => 'RU',
            'specific_data'   => [
                'registration_ip' => $lead->ip ?? '127.0.0.1',
                'p4'              => optional($lead->offer)->name,
            ],
            'aff_id'   => 870,
            'click_id' => $lead->uuid
        ]);

        if ($response->status() !== 200) {
            $this->error = $response->body();

            return;
        }

        try {
            // according to doc lead_id can be the same for different brand
            $this->externalId = sprintf(
                '%s-%s',
                $response->json()['data']['lead']['lead_id'],
                $response->json()['data']['brand']['brand_id']
            );
        } catch (Throwable $exception) {
            $this->error = $response->body() . PHP_EOL . $exception->getMessage();

            return;
        }

        if ($response->offsetGet('status') === 200) {
            $this->isSuccessful = true;
        } else {
            $this->error = $response->body();
        }
    }

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
        ])->get('https://api.clickmate.io/v2/download', [
            'limit'   => 1000,
            'status'  => 5,
            'created' => [
                '>' => $startDate->toIso8601String(),
                '<' => now()->toIso8601String(),
            ],
        ])->throw()->json();
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
