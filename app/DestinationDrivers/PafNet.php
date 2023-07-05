<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PafNet implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url      = 'https://profit-affiliate.net';
    protected string $source   = 'Office2';
    protected string $campaign = 'RU';
    protected ?string $page;
    protected ?string $landing;
    protected ?string $webTeam;
    protected ?string $login;
    protected ?string $password;
    protected $shortAnswer;

    protected $response;
    protected $getExternalId = null;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url         = $configuration['url'] ?? $this->url;
        $this->source      = $configuration['source'] ?? $this->source;
        $this->campaign    = $configuration['campaign'] ?? $this->campaign;
        $this->webTeam     = $configuration['web_team'] ?? null;
        $this->page        = $configuration['page'] ?? null;
        $this->landing     = $configuration['landing'] ?? null;
        $this->login       = $configuration['login'] ?? null;
        $this->password    = $configuration['password'] ?? null;
        $this->shortAnswer = $configuration['short_answer'] ?? false;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withToken($this->token())
            ->get($this->url . '/api/web-master/leads/statuses-ftd', [
                'date_from' => $since->toDateString(),
                'date_to'   => now()->toDateString(),
                'per_page'  => 500,
                'page'      => $page,
            ])->throw()->offsetGet('data'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['id'] ?? Carbon::parse($item['created_at'])->toDateString() . $item['email'],
                    'status'      => $item['status'],
                    'isDeposit'   => (bool)$item['is_ftd'],
                    'depositDate' => $item['ftd_date'],
                    'depositSum'  => '151',
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->asForm()->post(
            sprintf('%s/api/lead', $this->url),
            $payload = $this->payload($lead)
        );

        $this->getExternalId = now()->toDateString() . $payload['email'];

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function payload(Lead $lead): array
    {
        $payload = [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'campaign'   => $this->campaign,
            'email'      => $lead->getOrGenerateEmail(),
            'phone'      => $lead->phone,
            'landing'    => $this->landing ?? ('https://' . $lead->domain),
            'country'    => optional($lead->ipAddress)->country_code ?? 'RU',
            'vertical'   => 'Forex',
            'page'       => $this->page ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'source'     => $this->source,
            'ip'         => $lead->ip,
        ];

        if ($this->webTeam !== null) {
            $payload['web_team'] = $this->webTeam;
        }

        if ($this->shortAnswer) {
            $payload['description'] = $lead->hasPoll() ? $lead->getPollAsUrl() : '';
        } else {
            $payload['description'] = $lead->getPollAsText();
        }

        return $payload;
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() &&
            (data_get($this->response->json(), 'Status') === 'Success' || $this->forceDelivered());
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'Link');
    }

    public function getExternalId(): ?string
    {
        return $this->isDelivered() ? data_get($this->response->json(), 'lead_id', $this->getExternalId) : null;
    }

    protected function forceDelivered(): bool
    {
        return data_get($this->response->json(), 'Error') === 'Offer not selected';
    }

    /**
     * @throws \Exception
     *
     * @return string|null
     */
    protected function token(): ?string
    {
        return cache()->remember(sprintf('paf-net-%s-%s', $this->url, $this->login), now()->addHour(), function () {
            $response = Http::asJson()
                ->post(sprintf('%s/api/login', $this->url), [
                    'email'    => $this->login,
                    'password' => $this->password,
                ])->throw();

            return data_get($response->json(), 'token.access_token');
        });
    }
}
