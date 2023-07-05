<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NormalTrafficNew implements DeliversLeadToDestination, CollectsCallResults
{
    protected string $url = 'https://normal-traffic.club';
    protected $user_id;
    protected $source;
    protected $response;
    protected $offer_name;

    public function __construct($configuration = null)
    {
        $this->url         = $configuration['url'] ?? $this->url;
        $this->user_id     = $configuration['user_id'] ?? null;
        $this->source      = $configuration['source'] ?? null;
        $this->offer_name  = $configuration['offer_name'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        return collect(data_get(Http::acceptJson()->get($this->url . '/api/web-master/leads', [
            'date_start' => $since->addWeeks($page - 1)->toDateString(),
            'date_end'   => $since->addWeeks($page)->toDateString(),
        ])->throw()->json(), 'data.data'))->map(function ($item) {
            return new CallResult([
                'id'     => $item['id'],
                'status' => data_get($item, 'status.name'),
                // 'isDeposit'   => $item['status'] === 'Deposit',
                // 'depositDate' => null,
                // 'depositSum'  => $item['status'] === 'Deposit' ? '151' : null,
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::acceptJson()->asForm()->post($this->url . '/api/leads', $payload = [
            'full_name' => $lead->fullname,
            'phone'     => $lead->phone,
            'email'     => $lead->getOrGenerateEmail(),
            'ip'        => $lead->ip,
            'country'   => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            // 'comment'   => $lead->hasPoll() ? $lead->pollResults()->map(fn ($answer) => $answer->getQuestion() . PHP_EOL . $answer->getAnswer())->implode(' / ' . PHP_EOL . PHP_EOL) : '',
            'user_id'      => $this->user_id,
            'source'       => $this->source,
            'landing'      => $lead->domain.'?utm_source=t',
            'landing_name' => $this->offer_name ?? $this->getLandingName($lead),
            'description'  => $lead->hasPoll() ? Str::limit($this->answers($lead), 250, ' ...') : '',
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    protected function getLandingName(Lead $lead): string
    {
        if (Str::contains($this->url, 'normal-traffic')
            && (
                Str::contains($lead->offer->name, 'GAZPROMMILLER2')
                || Str::contains($lead->offer->name, 'GAZPROMMILLER')
                || Str::contains($lead->offer->name, 'GAZB')
            )
        ) {
            return 'gazprom';
        }

        return $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_');
    }

    /**
     * @param Lead $lead
     *
     * @return string
     */
    protected function answers(Lead $lead)
    {
        return $lead->getPollAsUrl();
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
        return data_get($this->response->json(), 'link_auto_login');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id');
    }
}
