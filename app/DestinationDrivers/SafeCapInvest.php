<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SafeCapInvest implements DeliversLeadToDestination, CollectsCallResults
{
    protected const SC_TOKEN_CACHE_KEY = 'SAFECAPINVEST_';
    protected string $url              = 'https://admin.safecapinvest.online';
    protected ?string $apiKey;
    protected $deskId      = 13;
    protected $field20     = 'Office2';
    protected $affiliateId = 17;
    protected $lang        = 2;
    protected $affFunnel;
    protected $login;
    protected $password;
    protected bool $sendquiz25 = false;
    protected $autologinUrl    = null;

    protected $response;

    protected const STATUS_NEW                = 1;
    protected const STATUS_COLD               = 2;
    protected const STATUS_IN_PROCESS         = 5;
    protected const STATUS_NO_ANSWER_1        = 6;
    protected const STATUS_NO_ANSWER_2        = 7;
    protected const STATUS_NO_ANSWER_3        = 8;
    protected const STATUS_NEVER_ANSWER       = 9;
    protected const STATUS_CALL_BACK          = 10;
    protected const STATUS_POTENTIAL          = 11;
    protected const STATUS_WRONG_NUMBER       = 12;
    protected const STATUS_NOT_INTEREST       = 13;
    protected const STATUS_TRANZIT            = 14;
    protected const STATUS_DEPOSIT            = 15;
    protected const STATUS_COLD_PLUS          = 16;
    protected const STATUS_RET_ACTIVE         = 25;
    protected const STATUS_RET_POTENTIAL_HIGH = 26;
    protected const STATUS_RET_POTENTIAL_LOW  = 27;
    protected const STATUS_RET_NO_ANSWER      = 28;
    protected const STATUS_RET_NEVER_ANSWER   = 29;
    protected const STATUS_RET_DO_NOT_CALL    = 30;
    protected const STATUS_RET_NO_POTENTIAL   = 31;
    protected const STATUS_RET_REASSIGN       = 32;
    protected const STATUS_RET_NEW            = 33;

    protected const STATUSES = [
        self::STATUS_NEW                => 'New',
        self::STATUS_COLD               => 'Cold',
        self::STATUS_IN_PROCESS         => 'In process',
        self::STATUS_NO_ANSWER_1        => 'No Answer 1',
        self::STATUS_NO_ANSWER_2        => 'No Answer 2',
        self::STATUS_NO_ANSWER_3        => 'No Answer 3',
        self::STATUS_NEVER_ANSWER       => 'Never Answer',
        self::STATUS_CALL_BACK          => 'Call Back',
        self::STATUS_POTENTIAL          => 'Potential',
        self::STATUS_WRONG_NUMBER       => 'Wrong Number',
        self::STATUS_NOT_INTEREST       => 'Not Interest',
        self::STATUS_TRANZIT            => 'Tranzit',
        self::STATUS_DEPOSIT            => 'Deposit',
        self::STATUS_COLD_PLUS          => 'Cold +',
        self::STATUS_RET_ACTIVE         => 'RET_Active',
        self::STATUS_RET_POTENTIAL_HIGH => 'RET_Potential_high',
        self::STATUS_RET_POTENTIAL_LOW  => 'RET_Potential_low',
        self::STATUS_RET_NO_ANSWER      => 'RET_No_answer',
        self::STATUS_RET_NEVER_ANSWER   => 'RET_Never_answer',
        self::STATUS_RET_DO_NOT_CALL    => 'RET_Do_not_call',
        self::STATUS_RET_NO_POTENTIAL   => 'RET_No_potential',
        self::STATUS_RET_REASSIGN       => 'RET_Reassign',
        self::STATUS_RET_NEW            => 'RET_NEW'
    ];

    protected const STATUSES_DEPOSIT = [
        self::STATUS_RET_ACTIVE,
        self::STATUS_RET_POTENTIAL_HIGH,
        self::STATUS_RET_POTENTIAL_LOW,
        self::STATUS_RET_NO_ANSWER,
        self::STATUS_RET_NEVER_ANSWER,
        self::STATUS_RET_DO_NOT_CALL,
        self::STATUS_RET_NO_POTENTIAL,
        self::STATUS_RET_REASSIGN,
        self::STATUS_RET_NEW,
    ];

    /**
     * AffBoat constructor.
     *
     * @param null $configuration
     *
     * @return void
     */
    public function __construct($configuration = null)
    {
        $this->url          = $configuration['url'] ?? $this->url;
        $this->apiKey       = $configuration['api_key'] ?? null;
        $this->deskId       = $configuration['desk_id'] ?? $this->deskId;
        $this->field20      = $configuration['field_20'] ?? $this->field20;
        $this->affiliateId  = $configuration['affiliate_id'] ?? $this->affiliateId;
        $this->lang         = $configuration['lang'] ?? $this->lang;
        $this->login        = $configuration['login'] ?? null;
        $this->password     = $configuration['password'] ?? null;
        $this->sendquiz25   = $configuration['sendquiz_25'] ?? $this->sendquiz25;
        $this->autologinUrl = $configuration['autologin_url'] ?? $this->autologinUrl;
        $this->affFunnel    = $configuration['aff_funnel'] ?? null;
    }

    public function pullResults(Carbon $since, int $page = 1): Collection
    {
        if (!$this->login || !$this->password) {
            return collect();
        }

        $pattern = json_encode([
            'name'    => '',
            'fields'  => ["id", "status_id", "affiliate_id", "user_id"],
            'filters' => [
                [
                    ["field_name" => "affiliate_id", "default_value" => $this->affiliateId, "filter_type" => "="],
                    ["field_name" => "creation_date", "default_value" => $since->unix(), "filter_type" => ">"],
                    'operation'   => 'AND', 'operationBlock' => 'AND'
                ]
            ],
            'sorting' => []], JSON_FORCE_OBJECT);

        $limit     = 300;
        $randParam = rand(1000000, 99999999);
        $key       = md5($this->apiKey . $randParam);

        return collect(json_decode(data_get(Http::get($this->url . '/api/v_2/crm/GetClientsList'
            . '?key=' . $key
            . '&rand_param=' . $randParam
            . '&auth_token=' . $this->retrieveToken($key, $randParam)
            . '&pattern_check=' . $pattern
            . '&use_pattern=true'
            . '&also_total=0'
            . '&limit=' . $limit
            . '&offset=' . $limit * ($page - 1))
            ->json(), 'values'), true))
            ->map(fn ($item) => new CallResult([
                'id'        => $item['user_id'],
                'status'    => array_key_exists($item['status_id'], self::STATUSES)
                    ? self::STATUSES[$item['status_id']]
                    : 'NONE',
                'isDeposit' => in_array($item['status_id'], self::STATUSES_DEPOSIT),
            ]));
    }

    /**
     * @param \App\Lead $lead
     *
     * @throws \Throwable
     */
    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post(sprintf('%s/api/v_2/page/RegisterUser', $this->url), $payload = $this->payload($lead));

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    protected function payload(Lead $lead): array
    {
        $randParam = rand(1000000, 99999999);

        $payload = [
            'rand_param'        => $randParam,
            'key'               => md5($this->apiKey . $randParam),
            'login'             => $lead->getOrGenerateEmail(),
            'password'          => 'Sa010406',
            'password_repeat'   => 'Sa010406',
            'second_name'       => $lead->lastname,
            'first_name'        => $lead->firstname,
            'email'             => $lead->getOrGenerateEmail(),
            'desk_id'           => $this->deskId,
            'is_active'         => 1,
            'additionalField20' => $this->field20,
            'additionalField23' => Str::before($lead->offer->getOriginalCopy()->name, '_'),
            'affiliate_id'      => $this->affiliateId,
            'phone'             => sprintf("+%s", $lead->formatted_phone),
            'country'           => $lead->ipAddress->country_code ?? 'RU',
            'origin'            => $lead->domain,
            'default_language'  => $this->lang,
        ];

        if ($this->sendquiz25) {
            $payload['additionalField25'] = $lead->getPollAsText();
        }

        if ($this->affFunnel) {
            $payload['aff_funnel'] = $this->affFunnel;
        }

        return $payload;
    }

    /**
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->response->successful() && $this->getExternalId() !== null;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->response->body();
    }

    /**
     * Get redirect url
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->autologinUrl ?? null;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        // there is also 'values.client_id'
        return data_get($this->response->json(), 'values.user_id');
    }

    private function retrieveToken(string $key, string $randParam)
    {
        if (app('cache')->has(self::SC_TOKEN_CACHE_KEY . $this->login)) {
            return app('cache')->get(self::SC_TOKEN_CACHE_KEY . $this->login);
        }

        $token =  Http::post(
            $this->url . '/api/v_2/page/Login',
            [
                'key'        => $key,
                'rand_param' => $randParam,
                'user_email' => $this->login,
                'password'   => $this->password,
            ]
        )->offsetGet('values')['auth_token'];

        return app('cache')
            ->remember(
                self::SC_TOKEN_CACHE_KEY . $this->login,
                now()->addMinutes(30),
                fn () => $token
            );
    }
}
