<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Services\MessageBird\MessageBird;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;
use Throwable;

class Affiliates360 implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://api.wickedtrack.com';
    protected ?string $affId;
    protected ?string $token;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $link       = null;
    protected ?string $externalId = null;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'] ?? $this->url;
        $this->affId = $configuration['aff_id'];
        $this->token = $configuration['token'];
    }

    /**
     * Collect results from the API
     *
     * @param \Carbon\Carbon $since
     * @param integer        $page
     *
     * @return \Illuminate\Support\Collection
     */
    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(Http::get(sprintf('%s/affiliate_deposits/', $this->url), [
            'token'  => $this->token,
            'from'   => $since->addWeeks($page - 1)->toDateString(),
            'to'     => $since->addWeeks($page)->toDateString(),
            'limit'  => 2000,
        ])->throw()->json())->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['sale_status'],
            'isDeposit'   => $item['first_time_deposit'] !== null,
            'depositDate' => Carbon::parse($item['first_time_deposit'])->toDateString(),
            'depositSum'  => '151',
        ]));
    }

    public function send(Lead $lead): void
    {
        $code = $this->getPhoneCode($lead->formatted_phone);

        if ($code === null) {
            return;
        }

        $response = Http::asForm()->post(sprintf('%s/leads', $this->url), [
            'affid'        => $this->affId,
            'first_name'   => $lead->firstname,
            'last_name'    => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'password'     => ucfirst(Str::random(10)) . rand(10, 99),
            'email'        => $lead->getOrGenerateEmail(),
            'area_code'    => sprintf("+%s", $code),
            'phone'        => substr($lead->formatted_phone, strlen($code)),
            '_ip'          => $lead->ip,
            'hitid'        => $lead->clickid,
            'funnel'       => $lead->offer->name
        ]);

        if ($response->successful()) {
            $this->isSuccessful = true;
            $this->externalId   = $response->offsetGet('lead')['id'];
            $this->link         = $response->offsetGet('extras')['redirect']['url'] ?? null;
        } else {
            $this->isSuccessful = false;
            $this->error        = $response->body();
        }
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
        return Http::get(sprintf('%s/affiliate_deposits/', $this->url), [
            'token'  => $this->token,
            'from'   => $startDate->subWeek()->format('Y-m-d'),
            'to'     => now()->addDay()->format('Y-m-d'),
            'filter' => 'ftd',
            'limit'  => 1000,
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

    protected function getPhoneCode(string $phone)
    {
        $service = new MessageBird(config('services.messagebird.key'));

        try {
            $result = $service->lookup($phone);

            return $result['countryPrefix'] ?? null;
        } catch (Throwable $exception) {
            $this->error        = $exception->getMessage();
            $this->isSuccessful = false;

            return null;
        }
    }
}
