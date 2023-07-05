<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Scaleo implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url = 'https://crypto.scaletrk.com';
    protected $token;
    protected $goal;
    protected $offer;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url       = $configuration['url'] ?? $this->url;
        $this->token     = $configuration['token'];
        $this->goal      = $configuration['goal'];
        $this->offer     = $configuration['offer'];
        $this->affiliate = $configuration['affiliate'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(data_get(Http::post($this->url . '/api/v2/affiliate/reports/conversions?'.http_build_query([
            'api-key' => $this->token,
            'page'    => $page,
            'perPage' => 500,
        ]), [
            'rangeFrom' => $since->toDateString(),
            'rangeTo'   => now()->toDateString(),
            'columns'   => 'transaction_id','conversion_status, payout',
        ])->json(), 'info.transactions', []))->map(fn ($conversion) => new CallResult([
            'id'         => $conversion['transaction_id'],
            'status'     => $conversion['conversion_status'],
            'isDeposit'  => true,
            'depositSum' => 150
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url . '/api/v2/affiliate/leads?api-key=' . $this->token, $payload = [
            'goal_id'      => $this->goal,
            'offer_id'     => $this->offer,
            'aff_click_id' => $lead->uuid,
            'ip'           => $lead->ip,
            'email'        => $lead->email,
            'firstname'    => $lead->firstname,
            'lastname'     => $lead->lastname ?? 'Unknown',
            'phone'        => $lead->phone,
            'affiliate_id' => $this->affiliate,
        ]);

        $lead->addEvent('payload', $payload);
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
        return data_get($this->response->json(), 'info.lead_id');
    }
}
