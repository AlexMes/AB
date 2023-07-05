<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Platform500 implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $token;
    protected $goalType;
    protected $depFromConversion;
    protected $funnelUrl;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url                = $configuration['url'];
        $this->token              = $configuration['token'];
        $this->goalType           = $configuration['goal_type'] ?? 2;
        $this->depFromConversion  = $configuration['dep_from_conversion'] ?? false;
        $this->funnelUrl          = $configuration['funnel_url'] ?? null;
    }

    /**
     * Pull results from the destination
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $response = Http::get($this->url . '/api/v1/affiliates/leads', [
            'token'        => $this->token,
            'created_from' => $since->toDateTimeString(),
            'page'         => $page,
        ])->throw()->json();

        return collect($response['data'])->map(function ($item) {
            return new CallResult([
                'id'          => $item['id'],
                'status'      => $item['sale_status'],
                'isDeposit'   => $item['has_conversion'] && $this->depFromConversion,
                'depositDate' => null,
                'depositSum'  => $item['has_conversion'] && $this->depFromConversion ? '151' : null,
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url . '/api/v1/affiliates/leads', $payload = [
            'token'   => $this->token,
            'profile' => [
                'first_name' => $lead->firstname,
                'last_name'  => $lead->lastname,
                'email'      => $lead->getOrGenerateEmail(),
                'phone'      => $lead->phone,
                'password'   => 'Aa' . Str::random(6) . rand(10, 99),
            ],
            'ip'           => $lead->ip,
            'tp_aff_sub'   => $lead->uuid,
            'tp_aff_sub3'  => Str::contains($this->url, ['punk']) ? '' : Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'tp_aff_sub4'  => $this->funnelUrl ?? (str_contains($this->url, 'punk') ? $this->offerPunks($lead) : ''),
            'tp_aff_sub9'  => str_contains($this->url, 'trafficsquare') ? Str::before($lead->offer->getOriginalCopy()->name, '_') : '',
            'tp_aff_sub10' => str_contains($this->url, 'trafficsquare') ? $lead->domain.'?fbp=1' : '',
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    protected function offerPunks(Lead $lead)
    {
        if (Str::contains($lead->offer->name, 'MUSK')) {
            return 'Tesla Musk';
        }

        return Str::before($lead->offer->getOriginalCopy()->name, '_');
    }

    public function isDelivered(): bool
    {
        return $this->response->successful();
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'auto_login_url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
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
        $page   = 1;
        $limit  = 100;
        $result = collect();

        do {
            $response = Http::get($this->url . '/api/v1/affiliates/conversions', [
                'token'        => $this->token,
                'created_from' => $startDate->toDateTimeString(),
                'goal_type_id' => $this->goalType,
                'page'         => $page,
                'per_page'     => $limit,
            ])->throw()->json();

            $result->push(...$response['data']);
        } while ($response['meta']['pagination']['total'] > $limit * $page++);

        return $result->toArray();
    }
}
