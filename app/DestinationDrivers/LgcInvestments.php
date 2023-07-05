<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class LgcInvestments implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://backoffice.lgc.investments';
    protected ?string $id;
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
        $this->id    = $configuration['id'] ?? null;
        $this->token = $configuration['token'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::asForm()
            ->post($this->url . '/api/Partner/GetLeads', [
                'ID'        => $this->id,
                'Token'     => $this->token,
                'DateStart' => $since->toDateTimeString(),
                /*'DateEnd'    => now()->toDateTimeString(),*/
                // not found max page size in docs...
                'PageSize'   => 500,
                'PageNumber' => $page,
            ])->throw()->offsetGet('data'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['leadId'],
                    'status'      => $item['status'],
                    'isDeposit'   => $item['ftdDate'] !== null,
                    'depositDate' => $item['ftdDate'],
                    'depositSum'  => $item['ftd'],
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()
            ->post(sprintf('%s/api/affilator', $this->url), $payload = [
                'AffilateID' => $this->id,
                'Token'      => $this->token,
                'Name'       => $lead->firstname,
                'LastName'   => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'      => $lead->getOrGenerateEmail(),
                'Phone'      => '+' . $lead->phone,
                'Campaign'   => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'Country'    => optional($lead->ipAddress)->country_code ?? 'RU',
                'Source'     => 'https://' . $lead->domain,
                'Password'   => 'ChangeMe123!'
            ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', [
            'status' => $this->response->status(),
            'body'   => $this->response->body()
        ]);
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
        // nothing appropriate in response...
        return data_get($this->response->json(), 'url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'clientID');
    }
}
