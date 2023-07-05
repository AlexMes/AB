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
use Log;

class TrackBox implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://platform.clicksclub.network';
    protected string $ai  = '2958590';
    protected string $gi  = '38';
    protected $ci;
    protected string $key;
    protected ?string $user;
    protected ?string $password;
    protected ?string $mpc2          = null;
    protected ?string $mpc3          = null;
    protected ?string $responsibleId = null;
    protected ?string $offer_name    = null;

    protected $dontSendAnswers;
    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $link       = null;
    protected ?string $externalId = null;
    public bool $nullInterval;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url             = $configuration['url'] ?? $this->url;
        $this->ai              = $configuration['ai'] ?? $this->ai;
        $this->gi              = $configuration['gi'] ?? $this->gi;
        $this->ci              = $configuration['ci'] ?? 1;
        $this->user            = $configuration['user'] ?? null;
        $this->password        = $configuration['password'] ?? null;
        $this->mpc2            = $configuration['MPC_2'] ?? null;
        $this->mpc3            = $configuration['MPC_3'] ?? null;
        $this->responsibleId   = $configuration['responsible_id'] ?? null;
        $this->offer_name      = $configuration['offer_name'] ?? null;
        $this->dontSendAnswers = $configuration['dont_send_answers'] ?? false;
    }

    /**
     * Collect sales results from the API
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since    = $since->toImmutable()->subWeek();
        $response = Http::asJson()->withHeaders([
            'x-trackbox-username' => $this->user,
            'x-trackbox-password' => $this->password,
            'x-api-key'           => '264388973aaa9b2f9eb2aa84a9c7382e',
        ])->post(sprintf('%s/api/pull/customers', $this->url), [
            'from' => $since->addWeeks($page - 1)->startOfDay()->toDateTimeString(),
            'to'   => $since->addWeeks($page)->endOfDay()->toDateTimeString(),
            'type' => "3"
        ])->json();

        if (!array_key_exists('data', $response)) {
            Log::info($response, ['trackbox']);
        }
        Log::debug(sprintf(
            "Since: %s, page: %s, from: %s, to: %s, url: %s, user: %s",
            $since->toDateTimeString(),
            $page,
            $since->addWeeks($page - 1)->startOfDay()->toDateTimeString(),
            $since->addWeeks($page)->endOfDay()->toDateTimeString(),
            $this->url,
            $this->user
        ), ['trackbox']);

        $this->nullInterval = !count($response['data']) && $since->addWeeks($page)->lessThanOrEqualTo(now());

        return collect($response['data'])
            ->map(fn ($item) => $item['customerData'])
            ->map(fn ($item) => new CallResult([
                'id'          => $item['uniqueid'],
                'status'      => $item['call_status'],
                'isDeposit'   => (bool) $item['depositor'],
                'depositDate' => Carbon::parse($item['first_depositDate'] ?? now())->toDateString(),
            ]));
    }

    public function send(Lead $lead): void
    {
        $response = Http::asJson()->withHeaders([
            'x-trackbox-username' => $this->user,
            'x-trackbox-password' => $this->password,
            'x-api-key'           => '2643889w34df345676ssdas323tgc738',
        ])->post(sprintf('%s/api/signup/procform', $this->url), $payload = $this->getPayload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('response', $response->json());

        if ($response->offsetExists('status') && $response->offsetGet('status')) {
            $this->isSuccessful = true;
            $this->externalId   = $response->offsetGet('addonData')['data']['uniqueid'];
            $this->link         = $response->offsetGet('data') ?? null;
        } else {
            $this->isSuccessful = false;
            $this->error        = $response->body();
        }
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function getPayload(Lead $lead): array
    {
        $payload = [
            'ai'          => $this->ai,
            'ci'          => $this->ci,
            'gi'          => $this->gi,
            'firstname'   => $lead->firstname,
            'lastname'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'       => $lead->getOrGenerateEmail(),
            'password'    => ucfirst(Str::random(5)) . lcfirst(Str::random(5)) . rand(10, 99),
            'phone'       => $lead->formatted_phone,
            'userip'      => $lead->ip,
            'so'          => $this->offer_name ?? $this->source($lead),
            'MPC_1'       => $this->mpc1($lead),
            'MPC_2'       => $this->mpc2,
            'MPC_3'       => $this->mpc3,
            'MPC_5'       => Str::contains($this->url, 'wemedia21') ? $lead->utm_source : '',
            'campaign'    => Str::contains($this->url, 'wemedia21') ? $lead->utm_source : '',
            'MPC_10'      => Str::contains($this->url, 's2snow') ? $lead->domain . '?utm_source=t' : null,
        ];

        if (!$this->dontSendAnswers) {
            $payload['description'] = $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion() . '-> ' . $question->getAnswer())->implode(PHP_EOL) : '';
            $payload['MPC_4']       = $lead->hasPoll() ? $this->answers($lead) : '';
        }

        if ($this->responsibleId !== null) {
            $payload['responsible_id'] = $this->responsibleId;
        }

        return $payload;
    }

    protected function mpc1(Lead $lead)
    {
        if (Str::contains($this->url, 'myadsworld')) {
            return 'ru';
        }

        if (Str::contains($this->url, 'ftdep') && $lead->hasPoll() && in_array($lead->offer_id, [1151,1117])) {
            return $lead->pollResults()->map(fn (PoolAnswer $a, $key) => [('step' . ++$key) => Str::before($a->getAnswer(), ')')]);
        }

        return Str::contains($this->url, 'theworldbest') ? ($lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion() . '-> ' . $question->getAnswer())->implode(PHP_EOL) : $lead->uuid) : $lead->uuid;
    }

    protected function source(Lead $lead)
    {
        if (Str::contains($this->url, 'mediacraft') && Str::contains($lead->offer->name, 'GAZPRO')) {
            return 'GazPromRu';
        }

        if (Str::contains($this->url, 'mediacraft') && Str::contains($lead->offer->name, 'TNK_BEST')) {
            return 'Tinkoff';
        }

        if (Str::startsWith($this->url, 'https://pa.punchaffiliates.net')) {
            return Str::before($lead->offer->getOriginalCopy()->name, '_');
        }

        if (Str::contains($this->url, 'tracndoa') && $lead->offer_id === 1826) {
            return 'GazPromRu';
        }

        if (Str::contains($this->url, 'jbomarketing') && Str::contains($lead->offer->name, 'GAZPROMMILLER')) {
            return 'Gazprom';
        }

        if (Str::contains($this->url, ['s2snow','jbomarketing','punchaffiliates'])) {
            return $lead->domain . '?utm_source=t';
        }

        if (Str::contains($this->url, 'trafagon') && Str::contains($lead->offer->name, 'GAZ')) {
            return 'Gazprom';
        }

        if (Str::contains($this->url, 'trafagon') && Str::contains($lead->offer->name, 'BB_RO')) {
            return 'Bitcoin Bank';
        }

        if (Str::contains($this->url, ['theworldbest']) && $lead->offer_id === 1607) {
            return $lead->domain . '?utm_source=cc';
        }

        if (Str::contains($this->url, ['theworldbest']) && $lead->offer_id === 1201) {
            return 'GazPromRu';
        }


        if (Str::contains($this->url, ['ftdep','fwi'])) {
            return $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_');
        }

        if (Str::contains($this->url, ['theworldbest','fwi'])) {
            return str_replace(['_SHM','_JRD'], '', $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_'));
        }


        if (Str::contains($this->url, 'foxoffers')) {
            return $lead->domain . '?utm_source=t';
        }

        if (Str::contains($this->url, ['theworldbest','ftdep'])) {
            return $lead->hasPoll() ? $lead->getPollAsUrl() : Str::before(str_replace(['_SHM','_JRD','FB_'], '', $lead->offer->getOriginalCopy()->name), '_');
        }

        if (Str::contains($this->url, ['pmatracker','fwi'])) {
            return $lead->lookup->country . ' ' . $lead->domain . '?utm_source=t';
        }

        if ($lead->offer_id === 1211 && Str::contains($this->url, 'trafa')) {
            return 'APPLE';
        }

        if ($lead->offer_id === 1295 && Str::contains($this->url, 'trafa')) {
            return 'TESLA';
        }

        if ($lead->offer_id === 1276 && Str::contains($this->url, 'trafa')) {
            return 'ENERGY';
        }

        if (in_array($lead->offer_id, [1225,1230]) && Str::contains($this->url, 'trafa')) {
            return 'FB Invest';
        }
        if ($lead->offer_id === 1178 && Str::contains($this->url, 'trafa')) {
            return 'SAXO';
        }

        if ($lead->offer_id === 539 && Str::contains($lead->offer->name, 'GAZ')) {
            return 'GazPromRu';
        }

        if ($lead->offer_id === 540) {
            return 'Quantum System';
        }

        if ($lead->offer_id === 484) {
            return $lead->domain;
        }

        return Str::before(str_replace(['_SHM','_JRD','FB_'], '', $lead->offer->getOriginalCopy()->name), '_');
    }

    protected function offername(Lead $lead)
    {
        if ($lead->user->branch_id === 19 && Str::contains($this->url, ['trafagon'])) {
            return 'Gazprom';
        }

        return Str::contains($this->url, ['pmatracker','fwi']) ? $lead->domain . '?utm_source=t' : Str::before(str_replace(['_SHM','_JRD','FB_'], '', $lead->offer->getOriginalCopy()->name), '_');
    }

    /**
     * @param Lead $lead
     *
     * @return string
     */
    protected function answers(Lead $lead)
    {
        if (Str::contains($this->url, 'ftdep')) {
            return $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion() . '-> ' . $question->getAnswer())->implode(PHP_EOL);
        }

        if ($this->ai == 2958051 && $this->gi == 41 && Str::contains($this->url, 'trfalliance')) {
            return $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion() . '-> ' . $question->getAnswer())->implode(PHP_EOL);
        }

        return $lead->getPollAsUrl();
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
        return Http::asJson()->withHeaders([
            'x-trackbox-username' => $this->user,
            'x-trackbox-password' => $this->password,
            'x-api-key'           => '264388973aaa9b2f9eb2aa84a9c7382e',
        ])->post(sprintf('%s/api/pull/customers', $this->url), [
            'from' => $startDate->format('Y-m-d 00:00:00'),
            'to'   => now()->format('Y-m-d 23:59:59'),
            /*type 2 = Only Leads
              type 3 = Leads + Deposits
              type 4= Only Deposits
              */
            'type' => 4,
        ])->throw()->json();
    }

    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->link;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
