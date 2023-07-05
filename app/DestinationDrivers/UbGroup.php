<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Str;

class UbGroup implements DeliversLeadToDestination, CollectsCallResults
{
    protected ?string $url = 'https://admin.swib.company';
    protected ?string $apiKey;
    protected ?string $deskId;
    protected ?string $affiliateId;
    protected ?string $tag1;
    protected bool $useRegisterUser = false;
    protected $campaignId;
    protected ?string $login;
    protected ?string $password;
    protected ?string $statusApiKey;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $link       = null;
    protected ?string $externalId = null;
    protected $response;

    /**
     * Undocumented function
     *
     * @param [type] $configuration
     */
    public function __construct($configuration = null)
    {
        $this->url             = $configuration['url'] ?? $this->url;
        $this->apiKey          = $configuration['api_key'] ?? null;
        $this->deskId          = $configuration['desk_id'] ?? null;
        $this->affiliateId     = $configuration['affiliate_id'] ?? null;
        $this->tag1            = $configuration['tag_1'] ?? null;
        $this->useRegisterUser = $configuration['use_register_user'] ?? false;
        $this->campaignId      = $configuration['campaign_id'] ?? null;
        $this->login           = $configuration['login'] ?? null;
        $this->password        = $configuration['password'] ?? null;
        $this->statusApiKey    = $configuration['status_api_key'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        $since = $since->toImmutable();

        if (empty($this->login) || empty($this->password) || empty($this->statusApiKey)) {
            return collect();
        }

        $statuses  = $this->statuses();
        $randParam = Str::random();

        return collect(data_get(Http::get(sprintf('%s/api/v_2/leadgen/client/getClients', $this->url), [
            'rand_param'     => $randParam,
            'key'            => md5($this->statusApiKey . $randParam),
            'affiliate_name' => $this->affiliateId,
            'limit'          => 1000,
            'offset'         => ($page - 1) * 1000,
            // filters don't work
            /*'filters'        => sprintf('[{"field":"creation_date","type":">=","value":"%s"}', $since->getTimestamp()),*/
        ])->throw()->json(), 'values'))->map(function ($item) use ($statuses) {
            return new CallResult([
                'id'        => $item['id'],
                'status'    => $statuses[$item['status_id']] ?? 'Unknown',
                'isDeposit' => in_array($statuses[$item['status_id']] ?? 'Unknown', ['Depositor']),
            ]);
        });
    }

    public function send(Lead $lead): void
    {
        if (!$this->useRegisterUser) {
            $this->response = Http::get(sprintf('%s/api/v_2/crm/CreateLead', $this->url), $payload = $this->payload($lead));
        } else {
            $this->response = Http::post(sprintf('%s/api/v_2/page/RegisterUser', $this->url), $payload = $this->payload($lead));
        }

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', $this->response->json());
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function payload(Lead $lead): array
    {
        $randParam = Str::random();

        $result = [
            'rand_param'   => $randParam,
            'key'          => md5($this->apiKey . $randParam),
            'first_name'   => $lead->firstname,
            'second_name'  => $lead->lastname ?? $lead->middlename ?? 'Unknown',
            'email'        => $lead->getOrGenerateEmail(),
            'phone'        => $lead->formatted_phone,
            'desk_id'      => $this->deskId,
            'affiliate_id' => $this->affiliateId,
            'description'  => Str::contains($this->url, 'ultratrderingpro')
                ? ''
                : ($lead->hasPoll() ? $lead->getPollAsUrl() : ''),
            'campaign_id' => $this->campaignId ?? Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'country'     => optional($lead->ipAddress)->country_code,
            'city'        => optional($lead->ipAddress)->city,
        ];

        if ($this->tag1 !== null) {
            $result['tag_1'] = $this->tag1;
        }

        if ($this->useRegisterUser) {
            $result['login'] = $lead->getOrGenerateEmail();
        }

        return $result;
    }

    public function isDelivered(): bool
    {
        return data_get($this->response->json(), 'result') === 'success';
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->useRegisterUser) {
            return data_get($this->response->json(), 'values.client_id');
        }

        return data_get($this->response->json(), 'values.leads_and_clients_id');
    }

    public function statuses(): ?array
    {
        return Cache::remember(
            sprintf("ubgroup-%s-%s-statuses", basename($this->url), $this->login),
            Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY,
            function () {
                $randParam = Str::random();

                return collect(json_decode(data_get(Http::get(sprintf('%s/api/v_2/crm/GetAllClientStatuses', $this->url), [
                    'rand_param'   => $randParam,
                    'key'          => md5($this->apiKey . $randParam),
                    'auth_token'   => $this->token(),
                ])->throw()->json(), 'values', []), true))->mapWithKeys(function ($item) {
                    return [$item['id'] => $item['name']];
                })->toArray();
            }
        );
    }

    protected function token(): ?string
    {
        return Cache::remember(
            sprintf("ubgroup-%s-%s-token", basename($this->url), $this->login),
            29 * Carbon::SECONDS_PER_MINUTE,
            function () {
                $randParam = Str::random();

                $response = Http::post(sprintf("%s/api/v_2/page/Login", $this->url), [
                    'rand_param' => $randParam,
                    'key'        => md5($this->apiKey . $randParam),
                    'user_email' => $this->login,
                    'password'   => $this->password,
                ])->throw();

                return data_get($response->json(), 'values.auth_token');
            }
        );
    }
}
