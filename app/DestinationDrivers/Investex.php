<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Investex implements DeliversLeadToDestination
{
    protected $url;
    protected $login;
    protected $password;
    protected $leadorderid;
    protected $response;
    protected $funnel;
    protected $office;
    protected $group;
    protected $department;

    public function __construct($configuration = null)
    {
        $this->leadorderid = $configuration['leadorderid'];
        $this->url         = $configuration['url'];
        $this->login       = $configuration['login'];
        $this->password    = $configuration['password'];
        $this->platform    = $configuration['platform'];
        $this->funnel      = $configuration['funnel'] ?? 1;
        $this->office      = $configuration['office'] ?? 1;
        $this->group       = $configuration['group'] ?? 19;
        $this->department  = $configuration['department'] ?? null;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getToken()
    {
        return cache()->remember($this->login.'-inv-auth-token'.$this->login, now()->addHours(12), function () {
            return Http::get($this->url.'/auth/generate_token', [
                'login'    => $this->login,
                'password' => $this->password,
                'platform' => $this->platform,
            ])->throw()->offsetGet('token');
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::timeout(10)
            ->post($this->url.'/user/register_user?token='.$this->getToken(), [
                'login'         => $lead->getOrGenerateEmail(),
                'password'      => Str::random(10).rand(10, 99),
                'name'          => $lead->fullname,
                'phone'         => $lead->formatted_phone,
                'email'         => $lead->getOrGenerateEmail(),
                'country'       => optional($lead->ipAddress)->country_code ?? 'NL',
                'leadorder_id'  => $this->leadorderid,
                'funnel_id'     => $this->funnel ?? 1,
                'group_id'      => $this->group ?? 19,
                'office_id'     => $this->office ?? 1,
                'department_id' => $this->department
            ]);
    }

    public function isDelivered(): bool
    {
        // lol, fucking awful, doesn't it?
        return true;
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        if ($this->response->offsetExists('auto_login')) {
            return $this->response->offsetGet('auto_login');
        }

        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->response->offsetExists('lead_id')) {
            return $this->response->offsetGet('lead_id');
        }

        return null;
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
        return Http::get($this->url.'/crm/check_deposits', [
            'token'      => $this->getToken(),
            'start_date' => $startDate->subMonth()->toDateTimeString(),
            'end_date'   => now()->toDateTimeString(),
        ])->throw()->json();
    }
}
