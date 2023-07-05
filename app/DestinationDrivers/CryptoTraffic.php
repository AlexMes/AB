<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CryptoTraffic implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url = 'https://crypto.traffic.partners';
    protected $token;
    protected $language;
    protected $response;
    public bool $nullInterval;

    public function __construct($configuration = null)
    {
        $this->url      = $configuration['url'] ?? $this->url;
        $this->token    = $configuration['token'];
        $this->language = $configuration['lang'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'API-KEY' => $this->token
        ])->post(sprintf('%s/api/partner/add_lead', $this->url), $payload = [
            'first_name' => $lead->firstname,
            'last_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'      => $lead->getOrGenerateEmail(),
            'language'   => $this->language,
            'password'   => 'ChangeMe123!',
            'phone'      => '+'.$lead->phone,
            'country'    => $lead->ipAddress->country_code,
            'user_ip'    => $lead->ip,
            'domain'     => $lead->domain,
            'comment'    => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() .PHP_EOL . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '',
        ]);

        $lead->addEvent('payload', $payload);
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
        return data_get($this->response->json(), 'login_link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since  = $since->toImmutable();
        $result = collect();

        $countPage = 1;

        do {
            $response = Http::withHeaders([
                'API-KEY' => $this->token
            ])->post(sprintf('%s/api/partner/get_info', $this->url), [
                'type'   => 'list',
                'from'   => $since->addDays($page - 1)->startOfDay()->toDateTimeString(),
                'to'     => $since->addDays($page)->startOfDay()->toDateTimeString(),
                'limit'  => 1000,
                'page'   => $countPage++
            ]);

            $items = data_get($response->json(), 'data', []);

            foreach ($items as $item) {
                $exists = $result->contains(function ($value) use ($item) {
                    return $value->getId() == $item['lead_id'];
                });

                if (!$exists) {
                    $result->push(new CallResult([
                        'id'        => $item['lead_id'],
                        'status'    => $item['status'],
                        'isDeposit' => in_array($item['status'], ['Deposit', 'Depozit']),
                    ]));
                }
            }
        } while ($items);

        $this->nullInterval = $result->isEmpty() && $since->addDays($page)->lessThanOrEqualTo(now());

        return $result;
    }
}
