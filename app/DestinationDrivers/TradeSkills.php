<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class TradeSkills implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://crm.thetradeskills.com';
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
        $since = $since->toImmutable();

        return collect(Http::get($this->url . '/crm/api/api_get_leads_status', [
            'token'      => $this->token,
            'start_date' => $since->addWeeks($page - 1)->toDateString(),
            'end_date'   => $since->addWeeks($page)->toDateString(),
        ])->throw()->offsetGet('leads'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['acc_id'],
                'status'      => $item['sales_status'],
                'isDeposit'   => $item['ftd'],
                'depositDate' => null,
                'depositSum'  => $item['ftd'] ? '151' : null,
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()
            ->post(sprintf('%s/crm/api/api_add_lead', $this->url), $payload = [
                'token'      => $this->token,
                'first_name' => $lead->firstname,
                'surname'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'      => $lead->getOrGenerateEmail(),
                'phone'      => '+' . $lead->phone,
                'campaign'   => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'geo'        => optional($lead->ipAddress)->country_code ?? 'RU',
                /*'cid'        => '',*/
                'extra' => $lead->getPollAsText(),
            ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'lead_id') !== null;
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
        return data_get($this->response->json(), 'lead_id');
    }
}
