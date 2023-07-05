<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SwissRoi implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url_send = 'https://client.swissroi.com';
    protected string $url_get  = 'https://backoffice.swissroi.com';
    protected ?string $token;
    protected ?string $affiliateId = '29';

    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url_send    = $configuration['url'] ?? $this->url_send;
        $this->url_get     = $configuration['url_get'] ?? $this->url_get;
        $this->token       = $configuration['token'] ?? null;
        $this->affiliateId = $configuration['affiliate_id'] ?? $this->affiliateId;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(data_get(Http::asForm()
            ->post(sprintf('%s/api/Partner/GetClients', $this->url_get), [
                'ID'         => $this->affiliateId,
                'Token'      => $this->token,
                'DateStart'  => $since->toIso8601String(),
                'DateEnd'    => now()->toIso8601String(),
                'PageSize'   => 1000,
                'PageNumber' => $page,
            ])->throw()->json(), 'data'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['clientid'],
                    'status'      => $item['status'],
                    'isDeposit'   => $item['ftd'] !== null,
                    'depositDate' => $item['ftdDate'] ? Carbon::parse($item['ftdDate'])->toDateString() : null,
                    'depositSum'  => $item['ftd'] ? '151' : null,
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()
            ->post(sprintf('%s/api/affilator', $this->url_send), $payload = [
                'Token'      => $this->token,
                'AffilateID' => $this->affiliateId,
                'Name'       => $lead->firstname,
                'LastName'   => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'Email'      => $lead->getOrGenerateEmail(),
                'Phone'      => '+' . $lead->phone,
                'Password'   => ucfirst(Str::random(10)) . rand(10, 99),
                'Country'    => optional($lead->ipAddress)->country_code ?? 'RU',
                'Source'     => Str::before($lead->offer->getOriginalCopy()->name, '_'),
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
        return data_get($this->response->json(), 'clientID');
    }
}
