<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class VallettaTrade implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://crm.vallettatrade.com/api';
    protected ?string $token;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $link       = null;
    protected ?string $externalId = null;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    /**
     * Collect results from the API
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @throws RequestException
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 0): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::post(sprintf('%s/check', $this->url), [
            'token'  => $this->token,
            'from'   => $since->addWeeks($page)->format('d-m-Y'),
            'to'     => $since->addWeeks($page + 1)->format('d-m-Y'),
            'page'   => $page,
        ])
            ->throw()->json())->map(fn ($item) => new CallResult([
                'id'          => $item['id'],
                'status'      => $item['callstatus'],
            ]));
    }

    /**
     * @param Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $response = Http::post(sprintf('%s/lead', $this->url), [
            'token'          => $this->token,
            'first_name'     => $lead->firstname,
            'last_name'      => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'          => $lead->getOrGenerateEmail(),
            'phone'          => substr($lead->formatted_phone, strlen($lead->lookup->prefix)),
            'utm_source'     => $lead->utm_source,
            'utm_medium'     => $lead->utm_medium,
            'utm_campaing'   => $lead->utm_campaign,
            'utm_content'    => $lead->utm_content,
            'country'        => $lead->ipAddress->country_code,
        ]);

        if ($response->successful()) {
            $this->isSuccessful = true;
            $this->externalId   = $response['id'];
        } else {
            $this->isSuccessful = false;
            $this->error        = $response->body();
        }
    }

    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->link;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
