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

class SkynetLeads implements DeliversLeadToDestination, CollectsCallResults
{
    protected $hash;
    protected $email;
    protected $uuid;
    protected $response;

    public function __construct($configuration = null)
    {
        $this->hash = $configuration['hash'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post('https://api.skynetleads.online/create_user_post.php', [
            'aff_hash'    => $this->hash,
            'login'       => $this->email = $lead->getOrGenerateEmail(),
            'password'    => 'ChangeMe123!',
            'email'       => $this->email,
            'first_name'  => $lead->firstname,
            'second_name' => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'       => $lead->phone,
            'country'     => optional($lead->ipAddress)->country_code ?? $lead->lookup->country,
            'tag_1'       => $this->uuid = $lead->uuid,
            'description' => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'campaign_id' => $lead->domain.'?utm_source=t',
        ]);

        $lead->addEvent('responded', $this->response->json());
    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'result') === 'success';
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return data_get(Http::asForm()->post('https://api.skynetleads.online/autologin.php', [
            'user_email' => $this->email,
            'password'   => 'ChangeMe123!',
            'language'   => 'ru',
        ])->json(), 'url');
    }

    public function getExternalId(): ?string
    {
        return $this->uuid;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        $response = Http::get('https://api.skynetleads.online/getLeads.php', [
            'aff_hash' => $this->hash,
            'min_date' => $since->addWeeks($page - 1)->startOfDay()->timestamp,
            'max_date' => $since->addWeeks($page)->endOfDay()->timestamp,
        ])->json();

        // Log::info($response, ['skynet']);

        return collect($response)->map(fn ($item) => new CallResult([
            'id'         => $item['tag_1'],
            'status'     => $item['status'],
            'isDeposit'  => filter_var($item['ftd_date'], FILTER_VALIDATE_BOOLEAN),
            'depositSum' => $item['ftd_ammount'],
        ]));
    }
}
