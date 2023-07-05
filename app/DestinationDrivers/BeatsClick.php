<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BeatsClick implements DeliversLeadToDestination, CollectsCallResults
{
    protected $token;
    protected $map;
    protected $response;
    protected $language;

    public function __construct($configuration = null)
    {
        $this->token    = $configuration['token'];
        $this->map      = $configuration['map'];
        $this->language = $configuration['lang'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::get('https://bestcliq.tech/api/v1/GetLeads', [
            'api_key'    => $this->token,
            'date_start' => $since->addWeeks($page - 1)->timestamp,
            'date_end'   => $since->addWeeks($page)->timestamp
        ])->offsetGet('data'))->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['status'],
            'isDeposit'   => (bool)data_get($item, 'ftd_info.ftd', false),
            'depositDate' => !empty($item['ftd_info']['ftd_date'])
                ? Carbon::parse($item['ftd_info']['ftd_date'])
                : null,
            'depositSum'  => data_get($item, 'ftd_info.ftd', false)
                ? data_get($item, 'ftd_info.ftd_amount', 150)
                : null,
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://bestcliq.tech/api/v1/AddLead', $payload = [
            'api_key'     => $this->token,
            'map_id'      => $this->map,
            'email'       => $lead->getOrGenerateEmail(),
            'first_name'  => $lead->firstname,
            'second_name' => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'       => $lead->phone,
            'country'     => $lead->ipAddress->country_code,
            'language'    => $this->language,
            'campaign'    => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            // 'description' => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() . ' => ' . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '',
            'password' => 'ChangeMe123!',
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
        return data_get($this->response->json(), 'autologin_link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'values.id');
    }
}
