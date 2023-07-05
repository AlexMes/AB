<?php

namespace App\Unity;

use App\Unity\Exceptions\Unauthorized;
use App\Unity\Exceptions\UnityException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UnityApp
{
    public const API_ADVERTISE_URL = 'https://services.api.unity.com';
    public const API_STATS_URL     = 'https://stats.unityads.unity3d.com';

    /**
     * @var string|null
     */
    protected ?string $apiKey;

    /**
     * @var string|null
     */
    protected ?string $keyId;

    /**
     * @var string|null
     */
    protected ?string $secretKey;

    /**
     * @var string|null
     */
    protected ?string $apiVersion = null;

    /**
     * @param string|null $keyId
     * @param string|null $secretKey
     * @param string|null $apiKey
     * @param string|null $apiVersion
     */
    public function __construct(?string $keyId, ?string $secretKey, ?string $apiKey, ?string $apiVersion = null)
    {
        $this->keyId       = $keyId;
        $this->secretKey   = $secretKey;
        $this->apiKey      = $apiKey;
        $this->apiVersion  = $apiVersion ?: 'v1';
    }

    /**
     * @param string $organizationCoreId
     * @param array  $params
     *
     *@throws \Throwable
     *
     * @return array|mixed
     *
     */
    public function fetchApps(string $organizationCoreId, array $params = [])
    {
        // offset, limit(max 1000) for pagination
        $response = $this->send("organizations/$organizationCoreId/apps", $params);

        throw_if($response->status() === 401, new Unauthorized($response->body()));
        throw_if($response->failed() || !$response->offsetExists('results'), new UnityException($response->body()));

        return $response->json();
    }

    /**
     * @param string $organizationCoreId
     * @param string $appId
     * @param array  $params
     *
     *@throws \Throwable
     *
     * @return array|mixed
     *
     */
    public function fetchCampaigns(string $organizationCoreId, string $appId, array $params = [])
    {
        $response = $this->send("organizations/$organizationCoreId/apps/$appId/campaigns", $params);

        throw_if($response->status() === 401, new Unauthorized($response->body()));
        throw_if($response->failed() || !$response->offsetExists('results'), new UnityException($response->body()));

        return $response->json();
    }

    /**
     * @param string $organizationId
     * @param array  $params
     *
     * @throws \Throwable
     *
     * @return array|mixed
     */
    public function fetchInsights(string $organizationId, array $params = [])
    {
        $response = $this->send(
            "organizations/$organizationId/reports/acquisitions",
            $params,
            ['Authorization' => 'Bearer ' . $this->apiKey]
        );

        throw_if($response->status() === 401, new Unauthorized($response->body()));
        throw_if($response->failed(), new UnityException($response->body()));

        return $response->body();
    }

    /**
     * @param string $apiKey
     *
     * @return $this
     */
    public function useApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @param string $keyId
     * @param string $secretKey
     *
     * @return $this
     */
    public function useCredentials(string $keyId, string $secretKey)
    {
        $this->keyId     = $keyId;
        $this->secretKey = $secretKey;

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

    /**
     * @param string $endpoint
     * @param array  $params
     * @param array  $headers
     * @param string $method
     *
     * @return Response
     */
    protected function send(string $endpoint, array $params = [], array $headers = [], string $method = 'get'): Response
    {
        $method  = strtolower($method);
        $headers = array_merge(['Content-Type' => 'application/json'], $headers);

        // Basic - for getting apps & campaigns (service acc credentials).
        // Bearer - for stats (api key).
        $headers = array_merge(
            ['Authorization' => 'Basic ' . base64_encode("$this->keyId:$this->secretKey")],
            $headers
        );

        return Http::withHeaders($headers)
            ->{$method}(sprintf(
                '%s/%s',
                strpos($headers['Authorization'], 'Basic ') !== false
                    ? self::API_ADVERTISE_URL . '/advertise/' . $this->apiVersion
                    : self::API_STATS_URL,
                trim($endpoint, '/'),
            ), $params);
    }
}
