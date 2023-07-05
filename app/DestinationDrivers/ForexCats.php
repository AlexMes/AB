<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ForexCats implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $token;
    protected $response;
    protected $welcomeLetter;
    protected $campaign;

    public function __construct($configuration = null)
    {
        $this->url            = $configuration['url'];
        $this->token          = $configuration['token'];
        $this->welcomeLetter  = $configuration['welcome_letter'] ?? 1;
        $this->campaign       = $configuration['campaign'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::post($this->url, [
            'token'    => $this->token,
            'method'   => 'get-leads',
            'filter'   => [
                'reg_from' => $since->toDateTimeString(),
                'take'     => 500,
                'skip'     => --$page * 500,
            ]
        ])->throw()->offsetGet('data'))->map(fn ($item) => new CallResult([
            'id'     => $item['lead_id'],
            'status' => $item['lead_status'],
            // status can be depositor but no firstDepositDate is set
            'isDeposit'   => $item['lead_status'] === 'Depositor',
            'depositDate' => $item['firstDepositDate'] !== null
                ? Carbon::parse($item['firstDepositDate'])->toDateString()
                : null,
            'depositSum' => $item['lead_status'] === 'Depositor' ? 150 : null,
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post($this->url, $payload = [
            'token'  => $this->token,
            'method' => 'registration',
            'user'   => [
                'first_name'     => $lead->firstname,
                'last_name'      => $lead->lastname ?? $lead->middlename ?? 'Unknkown',
                'email'          => $lead->getOrGenerateEmail(),
                'phone'          => $lead->formatted_phone,
                'country_iso'    => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
                'campaign_name'  => $this->campaign ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
                '_comment'       => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().' -> '.$question->getAnswer())->implode(' | ') : '',
                'welcome_letter' => $this->welcomeLetter,
            ],
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * Determine is lead delivered
     *
     * @return boolean
     */
    public function isDelivered(): bool
    {
        return $this->response->status() === 200 && $this->response->offsetGet('success');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        if ($this->response->offsetExists('autologin')) {
            return $this->response->offsetGet('autologin');
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
        return Http::post($this->url, [
            'token'         => $this->token,
            'method'        => 'get-deps',
            'dep_date_from' => $startDate->subMonth()->toDateTimeString(),
            'dep_date_to'   => now()->toDateTimeString(),
            'FTD'           => true,
        ])->throw()->json();
    }
}
