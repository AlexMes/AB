<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use URL;

class AffBoat implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://marketing.affboat.com/api/v3';
    protected ?int $linkId;
    protected ?int $send_offer;
    protected string $token;
    protected bool $isSuccessful = false;
    protected ?string $source;
    protected ?string $error      = null;
    protected ?string $link       = null;
    protected ?string $externalId = null;
    protected $bid                = null;
    protected $empId;
    protected $domain;
    protected ?string $message = null;
    protected $utmCampaign;

    /**
     * AffBoat constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->url         = $configuration['url'] ?? $this->url;
        $this->linkId      = $configuration['link_id'] ?? null;
        $this->token       = $configuration['token'];
        $this->source      = $configuration['source'] ?? null;
        $this->bid         = $configuration['bid'] ?? null;
        $this->empId       = $configuration['broker_employee_id'] ?? null;
        $this->send_offer  = $configuration['send_offer'] ?? null;
        $this->domain      = $configuration['domain'] ?? null;
        $this->message     = $configuration['message'] ?? $this->message;
        $this->utmCampaign = $configuration['utm_campaign'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        return collect(Http::get(sprintf('%s/get-leads', $this->url), [
            'api_token' => $this->token,
            'link_id'   => $this->linkId,
            'from'      => $since->toDateTimeString(),
            'limit'     => 500,
            'offset'    => --$page * 500,
        ])->throw()->offsetGet('data'))->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['status'],
            'isDeposit'   => (bool)$item['ftd'],
            'depositDate' => Carbon::parse($item['ftd_date'])->toDateString(),
            'depositSum'  => '151',
        ]));
    }

    /**
     * @param \App\Lead $lead
     *
     * @throws \Throwable
     */
    public function send(Lead $lead): void
    {
        $response = Http::post(sprintf('%s/integration?api_token=%s', $this->url, $this->token), $this->payload($lead));

        if (in_array($this->linkId, [855, 1048])) {
            Log::debug('Response from the affboat.');
            Log::debug($response->body());
        }

        if ($response->successful() && $response['success']) {
            $this->isSuccessful = true;
            $this->link         = $response->offsetGet('autologin');
            $this->externalId   = $response['id'];
        } else {
            \Log::error('Lead assignment delivery failed', $response->json());
            $this->error = $response['message'];
        }
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function payload(Lead $lead): array
    {
        $payload = [
            'fname'              => $lead->firstname ?? $lead->fullname,
            'lname'              => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'              => $lead->getOrGenerateEmail(),
            'fullphone'          => sprintf("+%s", $lead->formatted_phone),
            'source'             => $this->source($lead) ?? $this->getLandingUrl($lead),
            'link_id'            => $this->determineLinkId($lead->offer_id),
            'domain'             => $this->domain ?? $this->getLandingUrl($lead),
            'ip'                 => $lead->ip ?? '127.0.0.1',
            'description'        => $lead->hasPoll() ? $this->formattedPoll($lead) : '',
            'b_bid'              => $this->bid ?? null,
            'broker_employee_id' => $this->empId,
        ];

        if ($this->message !== null) {
            $payload['message'] = $this->message;
        }

        if ($this->utmCampaign !== null) {
            $payload['utm_campaign'] = $this->utmCampaign;
        }

        return $payload;
    }

    protected function source(Lead $lead)
    {
        if ($this->source) {
            return $this->source;
        }

        if (in_array($this->send_offer, [1117])) {
            return Str::before($lead->offer->getOriginalCopy()->name, '_') . '/' . $this->url;
        }

        // BNP request. EAS-114
        if (in_array($this->linkId, [3172])) {
            return Str::before($lead->offer->getOriginalCopy()->name, '_');
        }

        if (in_array($lead->affiliate_id, [241])) {
            return Str::before($lead->offer->getOriginalCopy()->name, '_');
        }

        return null;
    }

    /**
     * Get landing url
     *
     * @param mixed $lead
     *
     * @return void
     */
    public function getLandingUrl($lead)
    {
        if (in_array($lead->offer_id, [450])) {
            return 'https://gazplatfrom.com/';
        }

        if (in_array($lead->offer_id, [48])) {
            return 'https://funequa.info';
        }

        return sprintf('%s?utm_source=1', $lead->domain);
    }

    /**
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param \Carbon\Carbon $startDate
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectFtdSinceDate(Carbon $startDate)
    {
        return Http::get(sprintf('%s/get-leads', $this->url), [
            'api_token' => $this->token,
            'link_id'   => $this->linkId,
            'from'      => $startDate->toDateTimeString(),
            'filters'   => [
                'ftd' => 1,
            ],
        ])->throw()->json();
    }

    public function collectStatusesSince(Carbon $startDate, $page = 1)
    {
        return Http::get(sprintf('%s/get-leads', $this->url), [
            'api_token' => $this->token,
            'link_id'   => $this->linkId,
            'from'      => $startDate->toDateTimeString(),
            'limit'     => 500,
            'offset'    => --$page * 500,
        ])->throw()->offsetGet('data');
    }

    /**
     * Get redirect url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->link;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * Format and send poll results for customer
     *
     * @param \App\Lead $lead
     *
     * @return string
     */
    protected function formattedPoll(Lead $lead): string
    {
        return $lead->pollResults()
            ->map(fn ($answer) => $answer->getQuestion() . PHP_EOL . $answer->getAnswer())
            ->implode(' / ' . PHP_EOL . PHP_EOL);
    }

    /**
     * Determine link id for fxg24
     *
     * @param int $offer_id
     *
     * @return int
     */
    protected function determineLinkId(int $offer_id)
    {
        if ($this->linkId !== null) {
            return $this->linkId;
        }

        if (in_array($offer_id, [173,323])) {
            return 1042;
        }

        if (in_array($offer_id, [146,279,333])) {
            return 1043;
        }

        if ($offer_id === 215) {
            return 1044;
        }

        return null;
    }
}
