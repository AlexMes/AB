<?php

namespace App\DestinationDrivers;

use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Vexxsel implements Contracts\DeliversLeadToDestination, Contracts\CollectsCallResults
{
    protected string $url = 'https://api.vexxsel.tech';
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token  = $configuration['api_key'];
        $this->url    = $configuration['url'] ?? $this->url;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        if ($page) {
            $page--;
        }

        return collect(Http::withHeaders([
            'secretKey' => $this->token,
        ])
            ->get(
                sprintf('%s/v1/leads/leads', $this->url),
                [
                    'limit'                  => 200,
                    'offset'                 => $page,
                    'registration_date_from' => $since->format('d-m-Y'),
                    'registration_date_to'   => now()->addDays(1)->format('d-m-Y'),
                ]
            )->throw()->json())
            ->map(function ($item) {
                return new CallResult([
                    'id'          => $item['user_id'],
                    'status'      => $item['last_call_status_name'],
                    'isDeposit'   => $item['is_ftd'] === 'Yes',
                    'depositDate' => $item['ftd_date'],
                    'depositSum'  => null,
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'API-Key' => $this->token,
        ])
            ->post(sprintf('%s/v1/affiliate/users/register', $this->url), $payload = [
                'email'              => $lead->getOrGenerateEmail(),
                'firstName'          => $lead->firstname,
                'lastName'           => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'password'           => 'ChangeMe1234',
                'retypePassword'     => 'ChangeMe1234',
                'phone'              => $lead->phone,
                'countryName'        => $lead->ipAddress->country_name,
                'affiliateSecretKey' => $this->token,
                'referralInfo'       => $lead->domain
            ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->getExternalId() !== null;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'attributes.user_id');
    }
}
