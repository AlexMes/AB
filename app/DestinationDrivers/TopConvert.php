<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Jobs\Leads\FetchIpAddressData;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TopConvert implements DeliversLeadToDestination
{
    protected $token;
    protected string $url;
    protected $response;
    protected $error = null;

    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'];
        $this->token = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        if ($lead->ipAddress === null) {
            FetchIpAddressData::dispatchNow($lead);
            $lead->fresh('ipAddress');
        }
        $this->response = Http::post(sprintf("%s?%s", $this->url, http_build_query(['click_id' => $lead->uuid, 'aff_id' => 'A-JK16', 'subcampaign_id' => 'A-JK16'])), [
            'name'          => $lead->firstname,
            'lastname'      => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'         => $lead->getOrGenerateEmail(),
            'password'      => Str::random(8) . rand(10, 99),
            'phone'         => $lead->formatted_phone,
            'country_id'    => optional($lead->ipAddress)->country_code ?? 'CL',
            'specific_data' => json_encode(['ip' => $lead->ip ,'form_url' => $lead->domain,'subcampaign_id' => Str::before($lead->offer->getOriginalCopy()->name, '_')]),
            'language'      => 'EN',
        ]);

        if (! $this->response->ok()) {
            $this->error = $this->response->body();

            return;
        }
    }

    public function isDelivered(): bool
    {
        return $this->error === null
            && $this->response !== null
            && $this->response->offsetExists('data')
            && $this->response->json()['data']['redirect_url'] !== null;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->response->json()['data']['redirect_url'] ?? null;
    }

    public function getExternalId(): ?string
    {
        return $this->response->json()['data']['lead']['id'] ?? null;
    }

    /**
     * Determine lead language
     *
     * @param \App\Lead $lead
     *
     * @return string
     */
    protected function getLeadLanguage(Lead $lead): string
    {
        $lang = null;

        $languages = explode(',', optional(optional($lead)->ipAddress)->languages);

        if ($languages[0] !== "") {
            $lang = $languages[0];
        }

        if (in_array($lead->offer_id, [191,204,209])) {
            return 'EN';
        }

        if (in_array($lead->offer_id, [229,216])) {
            return 'ES';
        }

        return $lang ?? 'RU';
    }
}
