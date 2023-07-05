<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResultsByOne;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\LeadDestination;
use App\LeadOrderAssignment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AtoA implements DeliversLeadToDestination, CollectsCallResultsByOne
{
    protected $token;
    protected $campaignId;
    protected $currency;
    protected $language;
    protected $response;
    protected $url;
    protected $langOff;
    protected $sendDescription;

    public function __construct($configuration = null)
    {
        $this->token           = $configuration['token'];
        $this->campaignId      = $configuration['campaign'];
        $this->currency        = $configuration['currency'] ?? null;
        $this->language        = $configuration['lang'] ?? null;
        $this->url             = $configuration['url'] ?? 'https://a2a.tracktd.live';
        $this->langOff         = $configuration['lang_off'] ?? null;
        $this->sendDescription = $configuration['send_description'] ?? true;
    }

    /**
     * Pulls results by one from destination
     *
     * @param LeadDestination $destination
     *
     * @return Collection
     */
    public function pullResultsByOne(LeadDestination $destination, Carbon $since): Collection
    {
        $assignments = $destination->assignments()
            ->whereDate('created_at', '>=', $since->toDateString());

        $result = collect();

        /** @var LeadOrderAssignment $assignment */
        foreach ($assignments->cursor() as $assignment) {
            $response = Http::withHeaders([
                'Authorization' => 'AF/a2a/v1 ' . $this->token,
            ])->post($this->url.'/rpc', [
                'jsonrpc' => '2.0',
                'method'  => 'getRequestById',
                'params'  => [
                    'id' => $assignment->external_id,
                ]
            ]);

            $item = data_get($response->json(), 'result');
            if ($item !== null) {
                $result->push(new CallResult([
                    'id'          => $item['id'],
                    'status'      => $item['status'],
                    'isDeposit'   => $item['status'] === 'conversion_converted',
                    'depositDate' => null,
                    'depositSum'  => '151',
                ]));
            }
        }

        return $result;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::withHeaders([
            'Authorization' => 'AF/a2a/v1 ' . $this->token,
        ])->post($this->url.'/rpc', $payload = [
            'jsonrpc' => '2.0',
            'method'  => 'createRequest',
            'params'  => [
                'campaignId' => $this->campaignId,
                'data'       => $this->payload($lead),
            ]
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('integration-response', $this->response->json());
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function payload(Lead $lead)
    {
        $result = [
            'firstName'        => $lead->firstname,
            'lastName'         => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'phone'            => '+' . $lead->phone,
            'sub1'             => $lead->uuid,
            'sub2'             => sprintf('of-%s-%s', $lead->offer_id, optional($lead->offer)->branch_id),
            'country'          => optional($lead->lookup)->country ?? optional($lead->ipAddress)->country_code,
            'offerName'        => str_replace(['_SHM','_JRD'], '', $lead->offer->name),
            'offerDescription' => $lead->offer->description ?? 'None',
            'offerUrl'         => $lead->domain . '?utm_source=t',
            'password'         => 'ChangeMe1234',
            'email'            => $lead->getOrGenerateEmail(),
            'ip'               => $lead->ip,
        ];

        if ($this->currency) {
            $result['currency'] = $this->currency;
        }

        if (!$this->langOff) {
            $result['language'] = $this->language ?? $this->lang($lead);
        }

        if ($this->sendDescription) {
            $result['description'] = $lead->getPollAsText();
        }

        return $result;
    }

    public function lang(Lead $lead)
    {
        if (in_array($lead->offer_id, [1640,1645])) {
            return 'EN';
        }

        if (in_array($lead->offer_id, [1583,1694])) {
            return 'DE';
        }

        if ($lead->offer_id === 1261) {
            return 'ES';
        }

        if ($lead->offer_id === 1239) {
            return 'PL';
        }

        return 'RU';
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
        return data_get($this->response->json(), 'result.integrationData.redirectUrl');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'result.id');
    }
}
