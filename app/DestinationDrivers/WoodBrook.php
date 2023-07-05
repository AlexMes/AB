<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WoodBrook implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://crm.wood-brook.com';
    protected ?string $token;

    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'] ?? $this->url;
        $this->token = $configuration['token'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        // TODO nothing returned, cant test it.
        return collect();

        return collect(data_get(Http::asJson()
            ->post($this->url . '/api/check', [
                'token' => $this->token,
                'from'  => $since->format('d-m-Y'),
                'to'    => now()->format('d-m-Y'),
                'page'  => $page,
            ])->throw(), 'data'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['id'],
                    'status'      => $item['status'],
                    'isDeposit'   => $item['deposited'],
                    'depositDate' => null,
                    'depositSum'  => $item['deposited'] ? '151' : null,
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()
            ->post(sprintf('%s/api/lead', $this->url), $payload = [
                'token'        => $this->token,
                'first_name'   => $lead->firstname,
                'last_name'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'        => $lead->getOrGenerateEmail(),
                'phone'        => '+' . $lead->phone,
                'utm_source'   => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'utm_medium'   => '',
                'utm_campaing' => $lead->domain,
                'utm_content'  => $this->crmQuiz($lead),
                'comment'      => '',
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
        return data_get($this->response->json(), 'id');
    }

    public function crmQuiz($lead)
    {
        if (Str::contains($this->url, 'polariscorporate')) {
            return $lead->getPollAsUrl();
        }

        return $lead->getPollAsText();
    }
}
