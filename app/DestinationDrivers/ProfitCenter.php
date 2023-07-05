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

class ProfitCenter implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $username;
    protected $password;
    protected $response;
    protected $promo;
    protected $platform;
    protected $funnel;
    protected $group;
    protected $team;
    protected $office;
    protected $department;


    public function __construct($configuration = null)
    {
        $this->url         = $configuration['url'];
        $this->username    = $configuration['username'];
        $this->password    = $configuration['password'];
        $this->orderid     = $configuration['orderid'] ?? null;
        $this->promo       = $configuration['promo'] ?? null;
        $this->platform    = $configuration['platform'] ?? null;
        $this->group       = $configuration['group'] ?? null;
        $this->funnel      = $configuration['funnel'] ?? null;
        $this->responsible = $configuration['responsible'] ?? null;
        $this->team        = $configuration['team'] ?? null;
        $this->department  = $configuration['department'] ?? null;
        $this->office      = $configuration['office'] ?? null;
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
            Http::timeout(5)->get($this->url . '/crm/get_leads_statuses', [
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

    /**
     * Send lead to the CRM instance
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $email = $lead->getOrGenerateEmail();

        $this->response = Http::post($this->url.'/user/register_user?token='.$this->getToken(), [
            'login'          => $email,
            'password'       => 'StronkPassword123@',
            'name'           => $lead->fullname,
            'phone'          => $lead->phone,
            'email'          => $email,
            'leadorder_id'   => $this->orderid,
            'promo_code'     => $this->promo,
            'country_code'   => $this->getCountryCode($lead),
            'funnel_id'      => $this->funnel,
            'funnel'         => $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'group_id'       => $this->group,
            'responsible_id' => $this->responsible,
            'office_id'      => $this->office,
            'team_id'        => $this->team,
            'department_id'  => $this->department,
        ]);
    }

    protected function getCountryCode(Lead $lead)
    {
        $code = optional($lead->ipAddress)->country_code ?? optional($lead->lookup)->country;

        if ($code === 'DE') {
            return 'Germany';
        }

        return $code;
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
            AdsBoard::report('profitc Token cant be fetched. '. $response->body());
        }
    }

    public function collectFtdSinceDate($since)
    {
        return  Http::timeout(5)->get($this->url . '/crm/check_deposits', [
            'start_date' => $since->startOfDay()->toDateTimeString(),
            'end_date'   => now()->addDay()->endOfDay()->toDateTimeString(),
            'token'      => $this->getToken()
        ])->throw()->json();
    }
}
