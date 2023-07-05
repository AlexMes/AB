<?php

namespace App\DestinationDrivers;

use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class LeadPanda implements Contracts\DeliversLeadToDestination, Contracts\CollectsCallResults
{
    protected $response;
    protected string $url = 'https://aff.lead-panda.com';
    protected $token;
    protected $brandUuid;
    protected $referrer;
    protected $affiliateUrl;
    protected $offerId;

    public function __construct($configuration = null)
    {
        $this->url          = $configuration['url'] ?? $this->url;
        $this->token        = $configuration['token'];
        $this->brandUuid    = $configuration['brand_uuid'];
        $this->affiliateUrl = $configuration['affiliate_url'];
        $this->offerId      = $configuration['offer_id'];
        $this->referrer     = $configuration['referrer'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::asForm()->post($this->url . '/api/', [
            'method'     => 'management',
            'token'      => $this->token,
            'daterange'  => '{' . $since->addWeeks($page - 1)->toDateString() . ','
                . $since->addWeeks($page)->toDateString() . '}',
        ])->json())->map(function ($item, $id) {
            return new CallResult([
                'id'          => $id,
                'status'      => $item['status'],
                'isDeposit'   => (bool)$item['deposit'],
            ]);
        });
    }

    protected function payload(Lead $lead): array
    {
        $payload = [
            'method'       => 'integrate',
            'token'        => $this->token,
            'brand_uuid'   => $this->brandUuid,
            'email'        => $lead->getOrGenerateEmail(),
            'password'     => 'ChangeMe123!',
            'full_phone'   => '+' . $lead->phone,
            'firstname'    => $lead->firstname,
            'lastname'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'referrer'     => $this->referrer ?? $lead->domain,
            'country'      => optional($lead->ipAddress)->country_code ?? 'RU',
            'affiliateurl' => $this->affiliateUrl,
            'terms'        => true,
            'offer_id'     => $this->offerId,
            'ip_address'   => $lead->ip ?? '2.60.255.255',
        ];

        return $payload;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post(
            $this->url . '/api/',
            $payload = $this->payload($lead)
        );

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'error') === false;
    }

    public function getError(): ?string
    {
        return data_get($this->response->json(), 'status', $this->response->body());
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id', null);
    }
}
