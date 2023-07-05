<?php

namespace App\VK;

use App\VK\Exceptions\CouldNotGetAccessToken;
use App\VK\Exceptions\InvalidState;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VKLoginHelper
{
    public const OAUTH_STATE_KEY = 'vk-oauth-state';

    /**
     * @var VKClient
     */
    protected VKClient $client;

    /**
     * @param VKClient $client
     */
    public function __construct(VKClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param array  $scopes
     * @param string $separator
     *
     * @return string
     */
    public function getLoginUrl(array $scopes = [], string $separator = '&')
    {
        $state = Str::random();
        session()->put(self::OAUTH_STATE_KEY, $state);

        $params = [
            'client_id'     => $this->client->getClientId(),
            'state'         => $state,
            'response_type' => 'code',
            'redirect_uri'  => route('vk.callback'),
            'scope'         => implode(',', $scopes),
            'display'       => 'page',
        ];

        return sprintf(
            '%s/authorize?%s',
            VKClient::OAUTH_URL,
            http_build_query($params, null, $separator)
        );
    }

    /**
     * @param string $code
     * @param string $state
     * @param bool   $onlyToken
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws InvalidState
     * @throws CouldNotGetAccessToken
     *
     * @return array|string
     */
    public function getAccessTokenByCode(string $code, string $state, bool $onlyToken = true)
    {
        throw_if(!$this->validateState($state), new InvalidState());

        session()->forget(self::OAUTH_STATE_KEY);

        $params = [
            'client_id'     => $this->client->getClientId(),
            'client_secret' => $this->client->getClientSecret(),
            'code'          => $code,
            'redirect_uri'  => route('vk.callback'),
        ];

        $response = Http::get(sprintf(
            '%s/access_token?',
            VKClient::OAUTH_URL,
        ), $params);

        throw_unless($response->offsetExists('access_token'), new CouldNotGetAccessToken());

        return $onlyToken ? $response->offsetGet('access_token') : $response->json();
    }

    /**
     * @param string|null $state
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return bool
     */
    public function validateState(?string $state): bool
    {
        $savedState = session()->get(self::OAUTH_STATE_KEY);

        return $state === $savedState;
    }
}
