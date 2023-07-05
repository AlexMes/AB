<?php

namespace App\DestinationDrivers;

use App\AdsBoard;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Str;

class Balojah implements DeliversLeadToDestination
{
    protected ?string $url = 'https://my.lernerandmoriscapitalgroup.com';
    protected ?string $apiKey;
    protected ?string $deskId;

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
        $this->url    = $configuration['url'] ?? $this->url;
        $this->apiKey = $configuration['api_key'] ?? null;
        $this->deskId = $configuration['desk_id'] ?? null;
    }

    public function send(Lead $lead): void
    {
        $randParam = Str::random();

        $response = Http::get(sprintf('%s/api/v_2/crm/CreateLead', $this->url), [
            'rand_param'   => $randParam,
            'key'          => md5($this->apiKey . $randParam),

            'first_name'   => $lead->firstname,
            /*'status'       => 1,//hot*/
            'email'        => $lead->getOrGenerateEmail(),
            'phone'        => $lead->formatted_phone,
            /*'desk_id'      => $this->deskId,*/
            'description'  => $lead->fullname,
            'campaign_id'  => 'https://startuppers.biz/b/gazprom/',
            'second_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'country'      => optional($lead->ipAddress)->country_code,
            'city'         => optional($lead->ipAddress)->city,
        ]);

        AdsBoard::report('#delivery #balojah ' . $response->status());
        AdsBoard::report('Response ' . PHP_EOL . $response->body());

        if ($response->offsetGet('result') === 'success') {
            $this->isSuccessful = true;
            $this->externalId   = $response->offsetGet('values')['leads_and_clients_id'];
        } else {
            $this->isSuccessful = false;
            $this->error        = $response->body();
        }
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
