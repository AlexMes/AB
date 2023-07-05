<?php


namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Finantick implements DeliversLeadToDestination
{
    protected string $url;
    protected string $grantType = 'http://finantick.com/grants/api_key';
    protected ?int $campaign;
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $username;
    protected ?string $password;

    protected bool $isSuccessful  = false;
    protected ?string $error      = null;
    protected ?string $externalId = null;

    /**
     * @inheritDoc
     */
    public function __construct($configuration = null)
    {
        $this->url          = $configuration['url'];
        $this->campaign     = $configuration['campaign'] ?? null;
        $this->clientId     = $configuration['client_id'] ?? null;
        $this->clientSecret = $configuration['client_secret'] ?? null;
        $this->username     = $configuration['username'] ?? null;
        $this->password     = $configuration['password'] ?? null;
    }

    /**
     * Sends lead to desired destination
     *
     * @param \App\Lead $lead
     *
     * @return void
     */
    public function send(Lead $lead): void
    {
        $response = Http::asForm()
            ->acceptJson()
            ->post(sprintf("%s/rest/users.json?access_token=%s", $this->url, $this->resolveToken()), [
                'firstName'       => $lead->firstname ?? 'Unknown',
                'lastName'        => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'email'           => $lead->getOrGenerateEmail(),
                'username'        => $lead->getOrGenerateEmail(),
                'country'         => optional($lead->lookup)->country ?? 'AE',
                //'prefixCountry'   => '',
                //'areaPhone'   => '',
                'phone'           => $lead->formatted_phone,
                'plainPassword'   => sprintf("%s%s", Str::random(10), rand(10, 99)),
                'currency'        => 'USD',
                'enabled'         => 1,
                'demo'            => 0,
                'campaign'        => $this->campaign,
                'param1'          => $lead->domain,
            ]);

        if ($response->successful()) {
            $this->isSuccessful = true;
            $this->externalId   = $response->offsetGet('id');
        } else {
            $this->error = json_encode($response->offsetGet('errors'));
        }
    }

    /**
     * Determines is delivery was successful
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * Gets delivery failed error
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Get link to redirect user to it
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return str_replace('backoffice', 'www', $this->url);
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function resolveToken(): ?string
    {
        return Cache::remember(sprintf("finantick-%s-token", basename($this->url)), 3500, function () {
            $response = Http::asForm()
                ->acceptJson()
                ->post(
                    sprintf("%s/oauth/v2/token", $this->url),
                    [
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'grant_type'    => $this->grantType,
                        'username'      => $this->username,
                        'password'      => $this->password,
                    ]
                )->throw();

            return $response->offsetGet('access_token');
        });
    }
}
