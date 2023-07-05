<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AtsInvest implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://api.ats-invest.com';
    protected $response;
    protected string $username;
    protected string $password;
    protected $affiliateId;
    protected $affiliateUrl;
    protected $affiliateCompany;
    protected $officeId;

    public function __construct($configuration = null)
    {
        $this->username         = $configuration['username'];
        $this->password         = $configuration['password'];
        $this->affiliateId      = $configuration['affiliate_id'];
        $this->affiliateCompany = $configuration['affiliate_company'];
        $this->affiliateUrl     = $configuration['affiliate_url'] ?? null;
        $this->officeId         = $configuration['office_id'] ?? null;
        $this->url              = $configuration['url'] ?? $this->url;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token(),
        ])
            ->get(
                $this->url . '/users?',
                [
                    'affiliate'        => $this->affiliateId,
                    'page'             => $page,
                    'perPage'          => 50,
                    'affiliateCompany' => $this->affiliateCompany,
                ]
            )->offsetGet('entities'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['id'],
                    'status'      => $item['status'],
                    'isDeposit'   => $item['status'] === 'deposit',
                    'depositDate' => null,
                    'depositSum'  => '151',
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token(),
        ])
            ->post($this->url . '/users', $payload = [
                'firstName'        => $lead->firstname,
                'lastName'         => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'            => $lead->getOrGenerateEmail(),
                'password'         => 'ChangeMe1234',
                'phoneNumber'      => $lead->formatted_phone,
                'affiliate'        => $this->affiliateId,
                'affiliateUrl'     => $this->affiliateUrl,
                'affiliateCompany' => $this->affiliateCompany,
                'officeId'         => $this->officeId,
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
        return data_get($this->response->json(), 'id');
    }

    private function token()
    {
        return Http::post(
            $this->url . '/manager-sessions',
            [
                'login'    => $this->username,
                'password' => $this->password,
            ]
        )->offsetGet('token');
    }
}
