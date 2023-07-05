<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GoldenBit implements DeliversLeadToDestination, CollectsCallResults
{
    /**
     * Undocumented variable
     *
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;
    protected $login;
    protected $password;
    protected $url;
    protected $lang;

    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'];
        $this->login    = $configuration['login'];
        $this->password = $configuration['password'];
        $this->lang     = $configuration['lang'] ?? 'ru';
    }

    /**
     * Collect postback results from the API
     *
     * @param \Carbon\Carbon $since
     * @param int            $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(data_get(Http::get(sprintf("%s/api/leads", $this->url), [
            'login'    => $this->login,
            'password' => $this->password,
            'date'     => sprintf(
                '%s,%s',
                $since->addWeeks($page - 1)->toDateString(),
                $since->addWeeks($page)->toDateString()
            ),
        ])->throw(), 'list'))->map(function ($item) {
            return new CallResult([
                'id'        => $item['id'],
                'status'    => $item['status'],
                'isDeposit' => in_array($item['status'], ['Депозит', 'Deposit', 'FTD']),
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post(sprintf("%s/api/", $this->url), $payload = [
            'login'       => $this->login,
            'password'    => $this->password,
            'first_name'  => $lead->firstname,
            'last_name'   => $lead->lastname ?? 'Unknown',
            'phone'       => $lead->formatted_phone,
            'email'       => $lead->getOrGenerateEmail(),
            'country'     => optional($lead->ipAddress)->country_code ?? 'CL',
            'lang'        => $this->lang,
            'ip'          => $lead->ip,
            'lead_source' => $lead->domain,
            'description' => $lead->hasPoll() ? $lead->getPollAsUrl() : '',
            /*'test'  => 1,*/
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
        if ($this->response->offsetExists('duble')) {
            return null;
        }

        return data_get($this->response->json(), 'client_id');
    }
}
