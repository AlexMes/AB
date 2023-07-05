<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Str;

class AffLion implements DeliversLeadToDestination, CollectsCallResults
{
    protected ?string $url = 'https://aff-lead.com/lion';
    protected ?string $token;

    /**
     * Undocumented variable
     *
     * @var \Illuminate\Http\Client\Response
     */
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

    /**
     * @param Carbon $since
     * @param int    $page
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return Collection
     */
    public function pullResults(\Carbon\Carbon $since, int $page = 1): Collection
    {
        return collect(Http::post(sprintf('%s/get-leads', $this->url), [
            'Token'  => $this->token,
        ])->throw()->offsetGet('data'))->map(fn ($item) => new CallResult([
            'id'          => $item['id'],
            'status'      => $item['status'],
            'isDeposit'   => (bool)$item['ftd'],
            'depositDate' => Carbon::parse($item['ftdDate'])->toDateString(),
            'depositSum'  => '151',
        ]));
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()
            ->post(sprintf('%s/new-lead', $this->url), [
                'Token'    => $this->token,
                'Name'     => $lead->firstname,
                'LastName' => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'Email'    => $lead->getOrGenerateEmail(),
                'Phone'    => $lead->formatted_phone,
                'Password' => Str::random(10) . rand(10, 99),
                'Country'  => optional($lead->ipAddress)->country_code ?? 'NL',
                'Source'   => $lead->offer->name,
            ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful()
            && $this->response->offsetExists('clientID');
    }

    public function getError(): ?string
    {
        return Str::limit($this->response->body(), 250);
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->response->offsetExists('clientID')) {
            return $this->response->offsetGet('clientID');
        }

        return null;
    }

    /**
     * @param CarbonInterface      $startDate
     * @param CarbonInterface|null $endDate
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectFtdSinceDate(CarbonInterface $startDate, CarbonInterface $endDate = null)
    {
        return Http::asForm()->post(sprintf('%s/get-leads', $this->url), [
            'Token'    => $this->token,
            /*'created_from'   => $startDate->format('Y-m-d'),
            'created_to'     => ($endDate ?? now())->format('Y-m-d'),
            'has_conversion' => 1,*/
        ])->throw()->json();
    }
}
