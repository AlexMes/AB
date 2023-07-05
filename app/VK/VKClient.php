<?php

namespace App\VK;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class VKClient
{
    public const API_URL   = 'https://api.vk.com';
    public const OAUTH_URL = 'https://oauth.vk.com';

    /**
     * @var string
     */
    protected string $clientId;

    /**
     * @var string
     */
    protected string $clientSecret;

    /**
     * @var string
     */
    protected string $apiVersion = '5.131';

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $apiVersion
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $apiVersion = '5.131'
    ) {
        $this->apiVersion   = $apiVersion;
        $this->clientSecret = $clientSecret;
        $this->clientId     = $clientId;
    }

    /**
     * @param string $endpoint
     * @param array  $params
     * @param array  $headers
     * @param string $method
     *
     * @return Response
     */
    public function send(string $endpoint, array $params = [], array $headers = [], string $method = 'get'): Response
    {
        $method = strtolower($method);
        if ($method === 'get') {
            $headers = array_merge(['Content-Type' => 'application/x-www-form-urlencoded'], $headers);
        }

        $params = array_merge(['v' => $this->apiVersion], $params);

        return Http::withHeaders($headers)
            ->{$method}(sprintf(
                '%s/%s',
                self::API_URL,
                trim($endpoint, '/'),
            ), $params);
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }
}
