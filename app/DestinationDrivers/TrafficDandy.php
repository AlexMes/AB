<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class TrafficDandy implements DeliversLeadToDestination, CollectsCallResults
{
    protected $url;
    protected $token;
    protected $response;
    protected $signupId;

    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'];
        $this->token = $configuration['token'];
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $startDate = $since;
        $minDate   = now()->subDays(85);
        if ($since < $minDate) {
            $startDate = $minDate;
        }

        return collect(Http::withHeaders([
            'Api-Key' => $this->token
        ])->get($this->url.'/api/v2/leads', [
            'fromDate' => $startDate->toDateTimeString(),
            'toDate'   => now()->addDay()->toDateTimeString(),
            'page'     => $page
        ])->offsetGet('items'))->map(function ($item) {
            return new CallResult([
                'id'          => $item['leadRequestIDEncoded'],
                'status'      => $item['saleStatus'],
                'isDeposit'   => $item['hasFTD'],
                'depositDate' => $item['hasFTD'] ? now()->toDateString() : null
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Api-Key' => $this->token
        ])
            ->post($this->url.'/api/v1/signups/add.php', $payload = [
                'firstName'    => $lead->firstname,
                'lastName'     => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'        => $lead->getOrGenerateEmail(),
                'password'     => Str::random(8).rand(10, 99).'Af',
                'phone'        => $lead->formatted_phone,
                'ip'           => $lead->ip,
                'offerName'    => Str::before($lead->offer->getOriginalCopy()->name, '_'),
                'offerWebsite' => $lead->domain.'?utm_source=1',
                'comment'      => $this->poll($lead),
            ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', [
            'status' => $this->response->status(),
            'body'   => $this->response->body(),
        ]);
    }

    protected function poll(Lead $lead)
    {
        if ($lead->offer_id === 1784) {
            return $lead->hasPoll() ? $lead->pollResults()
                ->filter(fn ($block) => in_array($block->getQuestion(), [
                    "Your age",
                    "Your current monthly income",
                    "I hear about the value of investing...",
                    "The quickest way for me to start earning more is...",
                    "On these days how can you find cryptocurrency trends that will help you make big margin throughout the years?",
                    "Where is the best place you can invest and have guidance at the same time?",
                    "How much % of your income you are willing to start investing?\ud83e\udd14"]))
                ->map(fn ($answer) => $answer->getQuestion() . PHP_EOL . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '';
        }

        return $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() . PHP_EOL . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '';
    }

    public function isDelivered(): bool
    {
        return $this->response->status() === 200 && $this->response->offsetExists('data');
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    /**
     * Get autologin url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        if ($this->getExternalId()) {
            $response = Http::post($this->url.'/api/v1/brokers/login/details.php?signupID='.$this->getExternalId());

            if ($response->ok()) {
                $params = http_build_query($response->json()['data']['parameters']);

                if (strlen($params) > 0) {
                    return sprintf('%s/?%s', $response->json()['data']['url'], $params);
                }

                return $response->json()['data']['url'];
            }

            return null;
        }

        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->response->offsetExists('data')) {
            return $this->response->offsetGet('data')['signupRequestID'] ?? null;
        }

        return null;
    }

    public function collectFtdSinceDate(Carbon $startDate)
    {
        $params = http_build_query([
            'dateFrom'    => $startDate->subDay()->startOfDay()->toDateTimeString(),
            'dateTo'      => now()->addDay()->endOfDay()->toDateTimeString(),
            'itemPerPage' => 1000,
        ]);

        return Http::withHeaders([
            'Api-Key' => $this->token,
        ])->get($this->url.'/api/v1/brokers/accounts/apis/deposits.php?'.$params)->json();
    }
}
