<?php

namespace App\VK;

use App\VK\Exceptions\Unauthorized;

class VKApp
{
    /**
     * @var VKClient
     */
    protected VKClient $client;

    /**
     * @var string|null
     */
    protected ?string $appId;

    /**
     * @var string|null
     */
    protected ?string $appSecret;

    /**
     * @var string|null
     */
    protected ?string $accessToken;

    /**
     * @var string|null
     */
    protected ?string $apiVersion = null;

    /**
     * @param string|null $appId
     * @param string|null $appSecret
     * @param string|null $accessToken
     */
    public function __construct(
        ?string $appId,
        ?string $appSecret,
        ?string $accessToken,
        ?string $apiVersion = null
    ) {
        $this->appId       = $appId;
        $this->appSecret   = $appSecret;
        $this->accessToken = $accessToken;
        $this->apiVersion  = $apiVersion ?: '5.131';
    }

    /**
     * @return VKLoginHelper
     */
    public function getLoginHelper(): VKLoginHelper
    {
        return new VKLoginHelper($this->getClient());
    }

    /**
     * @return VKClient
     */
    public function getClient(): VKClient
    {
        if (!isset($this->client)) {
            $this->client = app(
                VKClient::class,
                [
                    'clientId'     => $this->appId,
                    'clientSecret' => $this->appSecret,
                    'apiVersion'   => $this->apiVersion,
                ]
            );
        }

        return $this->client;
    }

    /**
     * @param string|null $accessToken
     *
     * @throws \Throwable
     * @throws Unauthorized
     *
     * @return array|mixed
     */
    public function me(?string $accessToken = null)
    {
        $params = ['access_token' => $accessToken ?? $this->accessToken];

        $response = $this->getClient()->send('/method/users.get', $params);

        $result = $response->json();

        throw_if(isset($result['error']) || !isset($result['response'][0]['id']), new Unauthorized($response->body()));

        return $result['response'][0];
    }

    /**
     * @param array $params
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function fetchGroups(array $params = [])
    {
        $params = array_merge(['access_token' => $this->accessToken], $params);

        $response = $this->getClient()->send('/method/groups.get', $params);

        $result = $response->json();

        throw_if(isset($result['error']) || !isset($result['response']), new Unauthorized($response->body()));

        return $result['response'];
    }

    /**
     * @param array $params
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function fetchLeadForms(array $params = [])
    {
        $params = array_merge(['access_token' => $this->accessToken], $params);

        $response = $this->getClient()->send('/method/leadForms.list', $params);

        $result = $response->json();

        throw_if(isset($result['error']) || !isset($result['response']), new Unauthorized($response->body()));

        return $result['response'];
    }

    /**
     * @param array $params
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function fetchLeads(array $params = [])
    {
        $params = array_merge(['access_token' => $this->accessToken], $params);

        $response = $this->getClient()->send('/method/leadForms.getLeads', $params);

        $result = $response->json();

        throw_if(isset($result['error']) || !isset($result['response']), new Unauthorized($response->body()));

        return $result['response'];
    }

    /**
     * @param array $params
     *
     * @throws \Throwable
     * @throws Unauthorized
     *
     * @return array|mixed
     */
    public function fetchAdAccounts(array $params = [])
    {
        $params = array_merge(['access_token' => $this->accessToken], $params);

        $response = $this->getClient()->send('/method/ads.getAccounts', $params);

        $result = $response->json();

        throw_if(isset($result['error']) || !isset($result['response']), new Unauthorized($response->body()));

        return $result['response'];
    }

    /**
     * @param array $params
     *
     * @throws \Throwable
     * @throws Unauthorized
     *
     * @return array|mixed
     */
    public function fetchAdCampaigns(array $params = [])
    {
        $params = array_merge(['access_token' => $this->accessToken], $params);

        $response = $this->getClient()->send('/method/ads.getCampaigns', $params);

        $result = $response->json();

        throw_if(isset($result['error']) || !isset($result['response']), new Unauthorized($response->body()));

        return $result['response'];
    }

    /**
     * @param array $params
     *
     * @throws \Throwable
     * @throws Unauthorized
     *
     * @return array|mixed
     */
    public function fetchAds(array $params = [])
    {
        $params = array_merge(['access_token' => $this->accessToken], $params);

        $response = $this->getClient()->send('/method/ads.getAds', $params);

        $result = $response->json();

        throw_if(isset($result['error']) || !isset($result['response']), new Unauthorized($response->body()));

        return $result['response'];
    }

    /**
     * @param array $params
     *
     * @throws Unauthorized
     * @throws \Throwable
     *
     * @return array|mixed
     */
    public function fetchAdInsights(array $params = [])
    {
        $params = array_merge(['access_token' => $this->accessToken], $params);

        $response = $this->getClient()->send("/method/ads.getStatistics", $params);

        $result = $response->json();

        throw_if(isset($result['error']) || !isset($result['response']), new Unauthorized($response->body()));

        return $result['response'];
    }

    /**
     * @param string $accessToken
     *
     * @return $this
     */
    public function useToken(string $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @param string $apiVersion
     *
     * @return $this
     */
    public function useVersion(string $apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }
}
