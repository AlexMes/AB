<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\DestinationDrivers\Contracts\GetsInfoFromDestination;
use App\DestinationDrivers\Contracts\MappedResponse;
use App\Lead;
use App\LeadDestination;
use App\LeadOrderAssignment;

class Bitrix24 implements DeliversLeadToDestination, GetsInfoFromDestination, MappedResponse
{

    /**
     * Configuration array
     *
     * @var array|null
     */
    protected $bitrix;

    /**
     * Statuses have to be an array of key => value and stored into database table
     *
     * @see LeadDestination
     * An example of stored value:
     * <code>
     * [
     *      1 => NEW
     * ]
     * </code>
     *
     * @var array
     */
    protected $statuses;

    /**
     * @var bool
     */
    protected bool $isDelivered = false;

    /**
     * @var string|null
     */
    protected ?string $error = null;

    /**
     * @var string|null
     */
    protected ?string $externalId = null;

    /**
     * Bitrix24 constructor.
     *
     * @param array|null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->bitrix   = new \App\Services\Bitrix24\Bitrix24($configuration['url']);
        $this->statuses = $configuration['statuses'] ?? [];
    }

    /**
     * Dispatch lead to destination
     *
     * @param \App\Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return void
     *
     */
    public function send(Lead $lead): void
    {
        $result = $this->bitrix->addLead($lead);

        $this->externalId = $result;

        if ($result !== null) {
            $this->isDelivered = true;
        } else {
            $this->error = 'Bitrix24 failed.';
        }
    }

    /**
     * Get list of leads in bitrix24
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function getLeads()
    {
        return collect($this->bitrix->getLeads())->map(fn ($lead) => $this->mapResponse($lead));
    }

    /**
     * Get lead from bitrix24
     *
     * @param LeadOrderAssignment $assignment
     *
     * @throws \Throwable
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|null
     */
    public function getLead(LeadOrderAssignment $assignment)
    {
        return $this->mapResponse($this->bitrix->getLead($assignment));
    }

    /**
     * @inheritDoc
     */
    public function mapResponse(?array $response = null)
    {
        return is_null($response) ? $response : array_merge($response, [
            'STATUS_ID' => $this->statuses[$response['STATUS_ID']] ?? $response['STATUS_ID'],
        ]);
    }

    /**
     * @inheritDoc
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getLeadComments(LeadOrderAssignment $assignment)
    {
        return collect($this->bitrix->getLeadComments($assignment));
    }

    /**
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isDelivered;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
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
        return $this->externalId;
    }
}
