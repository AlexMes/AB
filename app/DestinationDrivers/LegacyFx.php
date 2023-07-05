<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Trail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class LegacyFx implements DeliversLeadToDestination, CollectsCallResults
{
    protected $response;
    protected $username;
    protected $password;

    public function __construct($configuration = null)
    {
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
    }

    /**
     * Send lead to destination
     *
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $token = $this->obtainToken();

        app(Trail::class)->add('Obtained token '.$token);

        $this->response = Http::withHeaders([
            'AuthToken' => $token,
        ])
            ->post('https://affiliate.legacyfx.com/api/aff/leads', [
                'FirstName' => $lead->firstname,
                'LastName'  => $lead->lastname ?? 'Unknown',
                'Email'     => $lead->getOrGenerateEmail(),
                'Phone'     => $lead->phone,
                'tag1'      => 'musk',
                'Country'   => 'ZA',
                // 'UserName'  => $this->username,
                // 'Password'  => $this->password,
            ]);

        // app(Trail::class)->add($this->response->body());
    }

    public function isDelivered(): bool
    {
        return $this->response !== null && $this->response->ok();
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
        return data_get($this->response->json(), 'Id', null);
    }

    /**
     * Collect call results
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect();
    }

    /**
     * Obtain aff auth token
     *
     * @return string
     */
    protected function obtainToken()
    {
        return Http::post('https://affiliate.legacyfx.com/api/affiliate/generateauthtoken', [
            'UserName' => $this->username,
            'password' => $this->password,
        ])->offsetGet('token');
    }
}
