<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class GasTrade implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://api.gastrade.company';
    protected ?string $login;
    protected ?string $password;
    protected ?string $deskId  = '1';
    protected ?string $offerId = '10';

    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'] ?? $this->url;
        $this->login    = $configuration['login'] ?? null;
        $this->password = $configuration['password'] ?? null;
        $this->deskId   = $configuration['desk_id'] ?? $this->deskId;
        $this->offerId  = $configuration['offer_id'] ?? $this->offerId;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(data_get(Http::withToken($this->getToken())->post($this->url . '/api/crm/get_lead_statuses', [
            'start_date' => $since->addWeeks($page - 1)->toDateString(),
            'end_date'   => $since->addWeeks($page)->toDateString(),
        ])->throw(), 'data.response.leads'))->map(function ($item) {
            return new CallResult([
                'id'     => $item['sub_id'],
                'status' => $item['department_status'],
                // TODO dont know how to identify dep, may be 'department_status' OR 'client_status' can be like 'DEPOSIT'...
                'isDeposit'   => in_array($item['department_status'], ['Deposit','FTD']) ,
                'depositDate' => null,
                // TODO dont know how to identify dep, may be 'department_status' OR 'client_status' can be like 'DEPOSIT'...
                'depositSum' => $item['department_status'] === 'Deposit' ? '151' : null,
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withToken($this->getToken())->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
        ])
            ->post(sprintf('%s/api/crm/create_lead', $this->url), $payload = [
                'first_name'  => $lead->firstname,
                'last_name'   => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'       => $lead->getOrGenerateEmail(),
                'password'    => ucfirst(Str::random(10)) . rand(10, 99),
                'phone'       => '+' . $lead->phone,
                'language_id' => 1,
                'desk_id'     => $this->deskId,
                'offer_id'    => $this->offerId,
                'dob'         => $lead->created_at->toDateString(),
                'ip'          => $lead->ip,
                'offer_name'  => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'offer_url'   => $lead->domain,
                'sub_id'      => $lead->uuid,
                /*'description' => $lead->hasPoll()
                    ? (Str::length($lead->getPollAsText()) < 1990
                        ? $lead->getPollAsText()
                        : $lead->getPollAsUrl())
                    : '',*/
                'offer_description' => $lead->hasPoll()
                    ? (Str::length($lead->getPollAsText()) < 1990
                        ? $lead->getPollAsText()
                        : $lead->getPollAsUrl())
                    : '',
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
        return data_get($this->response->json(), 'autologin');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.request.sub_id');
    }

    protected function getToken()
    {
        return cache()->remember('gas-trade-' . $this->login, now()->addHour(), function () {
            $response = Http::post($this->url . '/api/crm/auth/generate_token', [
                'email'    => $this->login,
                'password' => $this->password,
            ])->throw();

            return data_get($response->json(), 'data.response.token');
        });
    }
}
