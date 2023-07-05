<?php

namespace App\DestinationDrivers;

use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TrustLeads implements Contracts\DeliversLeadToDestination, Contracts\CollectsCallResults
{
    protected $response;
    protected const TL_TOKEN_CACHE_KEY = 'TRUSTLEADS_';
    protected string $url              = 'https://trustleads.co';
    protected string $username;
    protected string $password;
    protected $userId;
    protected $source;
    protected $landingName;
    protected $landing;

    public function __construct($configuration = null)
    {
        $this->url         = $configuration['url'] ?? $this->url;
        $this->username    = $configuration['username'];
        $this->password    = $configuration['password'];
        $this->userId      = $configuration['user_id'];
        $this->source      = $configuration['source'] ?? null;
        $this->landingName = $configuration['landing_name'] ?? null;
        $this->landing     = $configuration['landing'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(data_get(Http::acceptJson()->withHeaders([
            'Authorization' => 'Bearer ' . $this->retrieveToken(),
        ])
            ->get($this->url . '/api/web-master/leads', [
                'date_start_registration' => $since->toDateString(),
                'date_end_registration'   => now()->toDateString(),
                'page'                    => $page,
            ])->throw()->json(), 'data.data'))->map(function ($item) {
                return new CallResult([
                    'id'     => $item['id'],
                    'status' => data_get($item, 'status.name') ?? $item['status'],
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->asForm()->post($this->url . '/api/leads', $payload = [
            'email'        => $lead->getOrGenerateEmail(),
            'phone'        => $lead->phone,
            'full_name'    => $lead->fullname,
            'ip'           => $lead->ip,
            'country'      => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            'landing_name' => $this->landingName ?? $this->getLandingName($lead),
            'source'       => $this->source ?? $lead->domain,
            'landing'      => $this->landing ?? ('https://' . $lead->domain),
            'user_id'      => $this->userId,
            'description'  => $lead->hasPoll() ? Str::limit($this->answers($lead), 250, ' ...') : '',
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * @param Lead $lead
     *
     * @return string
     */
    protected function answers(Lead $lead)
    {
        return $lead->getPollAsUrl();
    }

    protected function getLandingName(Lead $lead): string
    {
        return $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_');
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
        return data_get($this->response->json(), 'lead_id');
    }

    private function retrieveToken()
    {
        if (app('cache')->has(self::TL_TOKEN_CACHE_KEY . $this->username)) {
            return app('cache')->get(self::TL_TOKEN_CACHE_KEY . $this->username);
        }

        $token =  Http::asForm()->post(
            $this->url . '/api/login',
            [
                'email'    => $this->username,
                'password' => $this->password,
            ]
        )->offsetGet('data')['token'];

        return app('cache')
            ->remember(
                self::TL_TOKEN_CACHE_KEY . $this->username,
                now()->addMinutes(30),
                fn () => $token
            );
    }
}
