<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Offer;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class GlobalAlliance implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $baseUrl = 'https://tss.tdsys.info';
    protected string $token;
    protected ?Response $response = null;
    protected ?string $affId      = null;
    protected $offerId;
    protected $bBid;

    /**
     * GlobalAlliance constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->baseUrl = $configuration['url'] ?? 'https://tss.tdsys.info';
        $this->token   = $configuration['token'];
        $this->offerId = $configuration['offer'] ?? null;
        $this->bBid    = $configuration['b_bid'] ?? null;
    }

    /**
     * Send lead to the customer
     *
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $this->response = Http::asJson()->post(sprintf("%s/leads/create.php?api_key=%s", $this->baseUrl, $this->token), $payload = [
            'fname'       => $lead->firstname,
            'lname'       => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'       => $lead->getOrGenerateEmail(),
            'phone'       => sprintf("+%s", $lead->formatted_phone),
            'country'     => 'RU',
            'lang'        => 'ru',
            'campaing_id' => $this->offerId ?? $this->getCampaignId($lead->offer_id),
            'currency'    => 'usd',
            'pass'        => sprintf("%s%s", Str::random(8), rand(10, 99)),
            'ip'          => $lead->ip ?? '127.0.0.1',
            'domain'      => sprintf('%s?utm_source=1', $lead->domain),
            'a_aid'       => $lead->hasPoll() ? $lead->getPollAsText() : '',
            'b_bid'       => $this->bBid ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'c_cid'       => 0
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('response', ['body' => $this->response->body()]);
    }

    /**
     * Determines lead was delivered or not
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->response->ok()
            && $this->response->offsetExists('message')
            && ! Str::contains($this->response->offsetGet('message'), 'Unable to create');
    }

    /**
     * Error message
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return Str::limit($this->response->body(), 250);
    }

    /**
     * Autologin URL
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->response->offsetExists('autologine')
            ? $this->response->offsetGet('autologine')
            : null;
    }

    /**
     * External ID for sent leads
     *
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->response->offsetExists('id')
            ? $this->response->offsetGet('id')
            : null;
    }

    /**
     * Get campaign ID for external application
     *
     * @param int $offerId
     *
     * @return int
     */
    protected function getCampaignId(int $offerId): int
    {
        if ($offerId === 351) {
            return 111;
        }

        if ($offerId === 206) {
            return 38;
        }

        if ($offerId === 215) {
            return 59;
        }

        if ($offerId === 211) {
            return 109;
        }

        return 37;
    }

    /**
       * Determine lead language
       *
       * @param \App\Lead $lead
       *
       * @return string
       */
    protected function getLanguage(Lead $lead): string
    {
        $lang = null;

        $languages = explode(',', optional(optional($lead)->ipAddress)->languages);

        if ($languages[0] !== "") {
            $lang = $languages[0];
        }

        if (in_array($lead->offer_id, [262])) {
            return 'ru';
        }
        if (in_array($lead->offer_id, [191,204,209])) {
            return 'en-UK';
        }

        if (in_array($lead->offer_id, [229,216])) {
            return 'es';
        }

        return $lang ?? 'ru';
    }

    /**
     * Pass description of offer, if possible
     *
     * @param int|null $offer
     *
     * @return string
     */
    protected function describeOffer(?int $offer)
    {
        if ($offer === 215) {
            return "Лиды переходят по рекламе с Facebook/Instagram. Регистрация происходит через прохождение опроса от бренда ВТБ Инвестиции, указывают цели заработка, фин обязательства, мин сумму инвестиции";
        }

        return Str::before(Offer::find($offer)->getOriginalCopy()->name, '_');
    }

    /**
     * Collect statuses from the API
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $url = Str::contains($this->baseUrl, 'tdsgoeagles')
            ? 'https://get.tdsgoeagles.com'
            : $this->baseUrl;

        $response = Http::get(sprintf('%s/leads/search.php', $url), [
            'api_key'  => $this->token,
            'reg_from' => $since->toDateString(),
            'per_page' => 500,
            'p'        => $page
        ])->throw();


        return collect($response->offsetExists('records') ? $response->offsetGet('records') : [])
            ->map(fn ($item) => new CallResult([
                'id'          => $item['id'],
                'status'      => $item['crm_status'],
                'isDeposit'   => $item['FTD'] ?? false,
                'depositDate' => $item['FTD_date'],
                'depositSum'  => '151',
            ]));
    }
}
