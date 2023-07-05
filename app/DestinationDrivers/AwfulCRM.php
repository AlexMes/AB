<?php

namespace App\DestinationDrivers;

use App\AdsBoard;
use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AwfulCRM implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $username;
    protected $password;
    protected $response;
    protected $funnel;
    protected $office;
    protected $group;
    protected $department;
    protected $team;
    protected $platform;
    protected $creativeId;
    protected $sendApiDate;
    protected $authMultData;

    public function __construct($configuration = null)
    {
        $this->url          = $configuration['url'];
        $this->username     = $configuration['username'];
        $this->password     = $configuration['password'];
        $this->funnel       = $configuration['funnel'] ?? null;
        $this->office       = $configuration['office'] ?? null;
        $this->group        = $configuration['group'] ?? null;
        $this->department   = $configuration['department'] ?? null;
        $this->orderid      = $configuration['orderid'] ?? null;
        $this->team         = $configuration['team'] ?? null;
        $this->platform     = $configuration['platform'] ?? null;
        $this->creativeId   = $configuration['creative_id'] ?? null;
        $this->sendApiDate  = $configuration['send_api_date'] ?? false;
        $this->authMultData = $configuration['auth_mult_data'] ?? [['username' => null, 'password' => null, 'platform' => null]];
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
        $this->authMultData = array_unique(array_merge([[
            'username' => $this->username,
            'password' => $this->password,
            'platform' => $this->platform
        ]], $this->authMultData), SORT_REGULAR);

        $results['lead_deposits'] = [];

        foreach ($this->authMultData as $credential) {
            if (
                is_array($credential)
                && array_key_exists('username', $credential)
                && array_key_exists('password', $credential)
                && array_key_exists('platform', $credential)
                && $credential['username']
                && $credential['password']
                && $credential['platform']
            ) {
                try {
                    $part = Http::timeout(5)->get($this->url . '/crm/check_deposits', [
                        'start_date' => $since->startOfDay()->toDateTimeString(),
                        'end_date'   => now()->addDay()->endOfDay()->toDateTimeString(),
                        'token'      => $this->getToken($credential['username'], $credential['password'], $credential['platform'])
                    ])->json();
                } catch (\Throwable $exception) {
                    \Log::warning(sprintf(
                        'Collect FTD failed. Url:%s, user:%s, platform:%s. Error: %s',
                        $this->url,
                        $credential['username'],
                        $credential['platform'],
                        $exception->getMessage()
                    ), ['awfulcrm']);

                    continue;
                }

                $results['lead_deposits'] = array_merge(
                    $results['lead_deposits'],
                    array_filter(data_get($part, 'lead_deposits', []), function ($item) use ($results) {
                        return (bool)$item['status'];
                    })
                );
            }
        }

        return $results;
    }

    /**
     * Send lead to the CRM instance
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url . '/crm/create_lead?token=' . $this->getToken(), $payload = $this->payload($lead));

        $lead->addEvent('payload', array_merge(['token' => $this->getToken()], $payload));
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function payload(Lead $lead)
    {
        $result = [
            'name'          => $lead->fullname,
            'phone'         => $lead->phone,
            'email'         => $lead->getOrGenerateEmail(),
            'funnel_id'     => $this->funnel,
            'group_id'      => $this->group,
            'office_id'     => $this->office,
            'team_id'       => $this->team,
            'department_id' => $this->department,
            'country_code'  => optional($lead->ipAddress)->country_code ?? optional($lead->lookup)->country,
        ];

        if (!Str::contains($this->url, 'viefinanc')) {
            $result['answer_info'] = $lead->getPollAsText();
        }

        if ($this->creativeId !== null) {
            $result['creative_id'] = $this->creativeId;
        }

        if ($this->orderid !== null) {
            $result['leadorder_id'] = $this->orderid;
        }

        if ($this->sendApiDate) {
            $date               = $lead->assignments()->latest()->first()->registered_at ?? now();
            $result['api_date'] = $date->toDateTimeString();
        }

        return $result;
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
     * @param null|mixed $username
     * @param null|mixed $password
     * @param null|mixed $platform
     *
     * @return void
     */
    protected function getToken($username = null, $password = null, $platform = null)
    {
        $response = Http::timeout(5)->get($this->url . '/auth/generate_token', [
            'login'    => $username ?? $this->username,
            'password' => $password ?? $this->password,
            'platform' => $platform ?? $this->platform,
        ]);

        try {
            return $response->offsetGet('token');
        } catch (\Throwable $th) {
            AdsBoard::report('awfucrm Token cant be fetched. ' . $response->body());
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
