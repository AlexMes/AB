<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AmoCrm implements DeliversLeadToDestination
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected ?string $code;
    protected ?string $redirectUri;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $externalId = null;

    /**
     * Olympus constructor.
     *
     * @param null $configuration
     */
    public function __construct($configuration = null)
    {
        $this->baseUrl      = $configuration['url'];
        $this->clientId     = $configuration['client_id'];
        $this->clientSecret = $configuration['client_secret'];
        $this->code         = $configuration['code'] ?? null;
        $this->redirectUri  = $configuration['redirect_uri'] ?? null;
    }

    /**
     * Send lead to destination
     *
     * @param \App\Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $contactId = $this->createContact($lead);
        if ($this->error !== null) {
            return;
        }

        $response = Http::withToken($this->resolveAccessToken())
            ->post(sprintf("%s/api/v4/leads", $this->baseUrl), [
                'add' => [
                    'name'       => $lead->fullname,
                    'created_at' => $lead->created_at->getTimestamp(),
                    'updated_at' => $lead->updated_at->getTimestamp(),
                    '_embedded'  => [
                        'contacts' => [
                            ['id' => $contactId],
                        ],
                    ],
                    "custom_fields_values" => [
                        [
                            /*'field_id' => 937561,*/
                            'field_code' => 'FROM',
                            'values'     => [
                                [
                                    'value' => optional($lead->offer)->name,
                                ],
                            ],
                        ],
                        [
                            'field_code' => 'REFERRER',
                            'values'     => [
                                [
                                    'value' => $lead->domain,
                                ],
                            ],
                        ],
                        [
                            'field_code' => 'FBCLID',
                            'values'     => [
                                [
                                    'value' => $lead->clickid,
                                ],
                            ],
                        ],
                    ],
                ]
            ]);

        if (! $response->ok()) {
            $this->error = $response->body();

            return;
        }

        if ($response->successful() && isset($response['_embedded']['leads'][0]['id']) !== null) {
            $this->isSuccessful = true;
            $this->externalId   = $response['_embedded']['leads'][0]['id'];
        }
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return array|null
     */
    public function getFieldsList()
    {
        $response = Http::withToken($this->resolveAccessToken())
            ->get(sprintf("%s/api/v4/leads/custom_fields", $this->baseUrl), []);

        if (!$response->successful() || !isset($response['_embedded']['custom_fields'])) {
            $this->error = $response->body();

            return null;
        }

        return $response->json();
    }

    /**
     * @param Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return integer|string|null
     */
    public function createContact(Lead $lead)
    {
        $response = Http::withToken($this->resolveAccessToken())
            ->post(sprintf("%s/api/v4/contacts", $this->baseUrl), [
                [
                    'first_name'           => $lead->firstname ?? 'Unknown',
                    'last_name'            => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                    'created_at'           => $lead->created_at->getTimestamp(),
                    'updated_at'           => $lead->updated_at->getTimestamp(),
                    "custom_fields_values" => [
                        [
                            /*'field_id' => 350321,*/
                            'field_code' => 'PHONE',
                            'values'     => [
                                [
                                    'enum_code' => 'MOB',
                                    'value'     => $lead->formatted_phone,
                                ],
                            ],
                        ],
                        [
                            /*'field_id' => 350323,*/
                            'field_code' => 'EMAIL',
                            'values'     => [
                                [
                                    'enum_code' => 'PRIV',
                                    'value'     => $lead->getOrGenerateEmail(),
                                ],
                            ],
                        ],
                    ],
                ]
            ]);

        if (! $response->ok() || !isset($response->json()['_embedded']['contacts'][0]['id'])) {
            $this->error = $response->body();

            return null;
        }

        return $response->json()['_embedded']['contacts'][0]['id'];
    }

    /**
     * Determine is lead delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @param \Carbon\Carbon $startDate
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    public function collectFtdSinceDate(Carbon $startDate)
    {
        /*return Http::get(sprintf('%s/api/users/campaigns/clients-registrations', $this->baseUrl), [
            'api_key'     => $this->apiKey,
            'campaign_id' => $this->campaignId,
            'startDate'   => $startDate->toIso8601String(),
            'has_ftd'     => true,
        ])->throw()->json();*/
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

    /**
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function freshTokensByCode()
    {
        $response = Http::post(sprintf("%s/oauth2/access_token", $this->baseUrl), [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'authorization_code',
            'code'          => $this->code,
            'redirect_uri'  => $this->redirectUri,
        ])->throw();

        if ($response->offsetExists('access_token') && $response->offsetExists('refresh_token')) {
            $this->cacheTokens($response->json());
        }
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return string|null
     */
    public function resolveAccessToken(): ?string
    {
        $token = $this->getAccessToken();

        if ($token !== null) {
            return $token;
        }

        $response = Http::asForm()
            ->acceptJson()
            ->post(
                sprintf("%s/oauth2/access_token", $this->baseUrl),
                [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $this->getRefreshToken(),
                    'redirect_uri'  => $this->redirectUri,
                ]
            )->throw();

        $this->cacheTokens($response->json());

        return $response->offsetGet('access_token');
    }

    /**
     * @param array $tokens
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cacheTokens(array $tokens)
    {
        Cache::set(sprintf('amocrm-%s-access-token', $this->baseUrl), $tokens['access_token'], 86300);
        Cache::set(sprintf('amocrm-%s-refresh-token', $this->baseUrl), $tokens['refresh_token'], 86400 * 30);
    }

    /**
     * @return string|null|mixed
     */
    public function getAccessToken()
    {
        return Cache::get(sprintf('amocrm-%s-access-token', $this->baseUrl));
    }

    /**
     * @return string|null|mixed
     */
    public function getRefreshToken()
    {
        return Cache::get(sprintf('amocrm-%s-refresh-token', $this->baseUrl));
    }
}
