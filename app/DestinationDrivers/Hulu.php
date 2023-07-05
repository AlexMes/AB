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

class Hulu implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $username;
    protected $password;
    protected $language;
    protected $desk;

    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'];
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
        $this->language = $configuration['language'];
        $this->desk     = $configuration['desk'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post(sprintf('%s/api/crm/create_lead', $this->url), $payload = [
            'token'       => $this->getToken(),
            'first_name'  => $lead->firstname,
            'last_name'   => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'language_id' => $this->language,
            'ip'          => $lead->ip,
            'desk_id'     => $this->desk,
            'phone'       => $lead->phone,
            'email'       => $lead->getOrGenerateEmail(),
            'password'    => 'ChangeMe123!',
            'sub_id'      => $lead->uuid,
            'offer_name'  => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'offer_url'   => $lead->domain.'?utm_source=t',
            'dob'         => '2020-01-01'
        ]);


        $lead->addEvent('payload', $payload);
        $lead->addEvent('crm-reponse', $this->response->json());
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

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        $deps = collect(Http::get($this->url . '/api/crm/check_deposits_period', [
            'start_date' => $since->addDays($page - 1)->toDateTimeString(),
            'end_date'   => $since->addDays($page)->toDateTimeString(),
            'token'      => $this->getToken()
        ])->throw()->offsetGet('lead_deposits'))->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['status'] ? 'FTD' : 'Unknown',
            'isDeposit'   => $item['status'],
            'depositDate' => $item['status'] ? Carbon::parse($item['time'])->toDateString() : null,
            'depositSum'  => '150'
        ]));

        return collect(Http::get($this->url . '/api/crm/get_lead_statuses', [
            'start_date' => $since->format('Y-m-d H:i'),
            'end_date'   => now()->format('Y-m-d H:i'),
            'token'      => $this->getToken()
        ])->throw()->offsetGet('lead_statuses'))->map(function ($item) use ($deps) {
            return new CallResult([
                'id'     => $item['id'],
                'status' => $item['status'],
            ]);
        })->merge($deps);
    }

    protected function getToken()
    {
        $response = Http::post($this->url . '/api/crm/auth/generate_token', [
            'login'    => $this->username,
            'password' => $this->password,
        ]);

        try {
            return $response->offsetGet('token');
        } catch (\Throwable $th) {
            AdsBoard::report('adsdb Token cant be fetched. '. $response->body());
        }
    }
}
