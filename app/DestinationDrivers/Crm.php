<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Trail;
use Illuminate\Support\Facades\Http;

class Crm implements DeliversLeadToDestination
{
    protected $response;
    protected $passthrough = false;
    /**
     * Crm constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        // here goes nothing
    }

    /**
     * Dispatch lead to destination
     *
     * @param \App\Lead $lead
     */
    public function send(Lead $lead): void
    {
        $manager = $lead->current_assignment->route->manager;

        if (optional($lead->user)->branch_id === 19) {
            app(Trail::class)->add('Pass through delivery, JRD desk');
            $this->passthrough = true;

            return;
        }

        if ($manager === null || $manager->frx_access_token === null) {
            app(Trail::class)->add('Pass through delivery, no token is present on manager');
            $this->passthrough = true;

            return;
        }

        $this->response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => sprintf('Bearer %s', $manager->frx_access_token),
        ])->post(sprintf('%s/api/leads/check', $manager->tenant->url), [
            'number' => $lead->formatted_phone,
        ]);

        if ($this->response->successful() === false) {
            app(Trail::class)->add('Pass through delivery, CRM responded with error');
            app(Trail::class)->add($this->response->body());


            $this->passthrough = true;

            return;
        }
    }


    public function isDelivered(): bool
    {
        return $this->passthrough
            || (data_get(optional($this->response)->json(), 'exists', false) === false)
            || (data_get(optional($this->response)->json(), 'manager_can_view', false) === true);
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        if (data_get(optional($this->response)->json(), 'manager_can_view', false) === true) {
            return 'Lead is locked in CRM';
        }

        return 'Lead exists in CRM';
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return data_get(optional($this->response)->json(), 'id', null);
    }
}
