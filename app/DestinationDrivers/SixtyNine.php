<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SixtyNine implements DeliversLeadToDestination, CollectsCallResults
{
    protected $response;

    public function __construct($configuration = null)
    {
        //
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withToken($this->getToken())->post('https://api-v2.laravelcrm.fun/api/v2/lead', [
            'affiliate_id'    => '112422',
            'first_name'      => $lead->firstname,
            'last_name'       => $lead->lastname,
            'mail'            => $lead->getOrGenerateEmail(),
            'phone'           => $lead->phone,
            'sale_status_id'  => '55',
            'customer_status' => '10',
            'experience'      => $lead->hasPoll() ? $lead->pollResults()->map(fn ($question) => $question->getQuestion().' -> '.$question->getAnswer())->implode(PHP_EOL) : '',
            'department_id'   => '7',
        ]);
    }

    protected function getToken()
    {
        return Http::post('https://api-v2.laravelcrm.fun/api/login', [
            'mail'     => 'aff_gazprom@gmail.com',
            'password' => 'KibNgC78E1t1iX6Bidny',
        ])->offsetGet('token');
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
        return data_get($this->response->json(), 'data.id');
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect();
        $response = Http::withToken($this->getToken())->get('https://api-v2.laravelcrm.fun/api/sale_status', [
            ''
        ]);
    }
}
