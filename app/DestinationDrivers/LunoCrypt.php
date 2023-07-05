<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class LunoCrypt implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://api.lunocrypt.com';
    protected ?string $token;

    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'] ?? $this->url;
        $this->token = $configuration['token'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(data_get(Http::asJson()
            ->withHeaders(['Authorization' => $this->token])
            ->get($this->url . '/affiliates/leads', [
                'from' => $since->addWeeks($page - 1)->getTimestamp(),
                'to'   => $since->addWeeks($page)->getTimestamp(),
                /*'deposited' => 1,*/
            ])->throw(), 'data'))->map(function ($item) {
                return new CallResult([
                    'id'          => $item['id'],
                    'status'      => $item['status'],
                    'isDeposit'   => $item['deposited'],
                    'depositDate' => null,
                    'depositSum'  => $item['deposited'] ? '151' : null,
                ]);
            });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()
            ->withHeaders(['Authorization' => $this->token])
            ->post(sprintf('%s/affiliates/leads', $this->url), $payload = [
                'email'        => $lead->getOrGenerateEmail(),
                'phone'        => '+' . $lead->phone,
                'firstname'    => $lead->firstname,
                'lastname'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'fullname'     => $lead->fullname,
                'country'      => optional($lead->ipAddress)->country_name ?? 'Russia',
                'utm_campaign' => $lead->domain,
                /*'comment'      => '',
                'info'         => '',*/
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
        return data_get($this->response->json(), 'data.valid.0.login_url');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'data.valid.0.id');
    }
}
