<?php

namespace App\DestinationDrivers;

use App\AdsBoard;
use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ScHrm implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $username;
    protected $password;
    protected $response;
    protected $platform;

    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'];
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
        $this->orderid  = $configuration['orderid'] ?? null;
        $this->platform = $configuration['platform'] ?? null;
    }

    /**
     * Collect call results
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(
            Http::get($this->url . '/crm/get_leads_statuses', [
                'start_date' => $since->addWeeks($page - 1)->toDateTimeString(),
                'end_date'   => $since->addWeeks($page)->toDateTimeString(),
                'token'      => $this->getToken()
            ])->throw()->offsetGet('lead_statuses')
        )->map(function ($item) {
            return new CallResult([
                'id'     => $item['id'],
                'status' => $item['status'],
            ]);
        });
    }

    public function collectFtdSinceDate($since)
    {
        return  Http::timeout(5)->get($this->url . '/crm/check_deposits', [
            'start_date' => $since->startOfDay()->toDateTimeString(),
            'end_date'   => now()->addDay()->endOfDay()->toDateTimeString(),
            'token'      => $this->getToken()
        ])->throw()->json();
    }

    /**
     * Send lead to the CRM instance
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url . '/crm/create_lead?token=' . $this->getToken(), $payload = array_merge([], [
            'first_name'    => $lead->firstname,
            'last_name'     => $lead->lastname,
            'phone'         => $lead->phone,
            'email'         => $lead->getOrGenerateEmail(),
            'lead_order_id' => $this->orderid ?? null,
            'telegram'      => data_get($lead->requestData, 'telegram', $lead->lastname),
        ]));

        $lead->addEvent('payload', array_merge(['token' => $this->getToken()], $payload));
    }


    public function isDelivered(): bool
    {
        return $this->response->ok() && data_get($this->response->json(), 'status', false);
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
        return data_get($this->response->json(), 'lead_id', null);
    }

    /**
     * Get auth token
     *
     * @return void
     */
    protected function getToken()
    {
        $response = Http::timeout(5)->get($this->url . '/auth/generate_token', [
            'login'    => $this->username,
            'password' => $this->password,
            'platform' => $this->platform,
        ]);

        try {
            return $response->offsetGet('token');
        } catch (\Throwable $th) {
            AdsBoard::report('schrm Token cant be fetched. '. $response->body());
        }
    }

    /**
     * Get leads order
     *
     * @return int
     */
    protected function getOrderId()
    {
        $orders = Http::timeout(5)->get($this->url . '/crm/get_leadorders', [
            'token' => $this->getToken(),
        ])->offsetGet('leadorders');

        return $orders[0]['id'];
    }
}
