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

class Franklin implements DeliversLeadToDestination, CollectsCallResults
{
    protected $token;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Api-Key' => $this->token
        ])->post('https://frnkaffs-api.com/api/v2/leads', $payload = [
            'firstName'    => $lead->firstname,
            'lastName'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'        => $lead->getOrGenerateEmail(),
            'password'     => 'ChangeMe123',
            'phone'        => $lead->phone,
            'ip'           => $lead->ip,
            'comment'      => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $a) => $a->getQuestion().' => '.$a->getAnswer())->implode(PHP_EOL) : null,
            'offerName'    => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'offerWebsite' => $lead->domain.'?utm_source=t',
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', $this->response->json());
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
        return 'https://autologinlink.com/api/v1/brokers/login/redirect.php?signupID='.$this->getExternalId();
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'details.leadRequest.ID');
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $response = collect(Http::withHeaders(['Api-Key' => $this->token])->get('https://frnkaffs-api.com/api/v2/leads', [
            'fromDate' => $since->startOfDay()->toDateTimeString(),
            'toDate'   => now()->toDateTimeString(),
            'page'     => $page
        ])->offsetGet('items'))->map(fn ($item) => new CallResult([
            'id'          => $item['leadRequestIDEncoded'],
            'status'      => $item['saleStatus'],
            'isDeposit'   => $item['hasFTD'],
            'depositDate' => $item['FTDdate'] ?? now()->toDateString(),
            'depositSum'  => $item['depositAmount'] ?? null
        ]));

        logger($response, ['integration' => 'franklin']);

        return $response;
    }
}
