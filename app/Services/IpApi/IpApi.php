<?php

namespace App\Services\IpApi;

use App\AdsBoard;
use Illuminate\Support\Facades\Http;

class IpApi
{
    /**
     * API key
     *
     * @var string
     */
    protected string $token;

    /**
     * Base service url
     *
     * @var string
     */
    protected string $url = 'https://ipapi.co';

    /**
     * IpApi constructor.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Fetch data about ip address
     *
     * @param string $ipAddress
     *
     * @return mixed
     */
    public function get(string $ipAddress)
    {
        if (config('services.ipapi.token')) {
            $response = Http::get(sprintf('%s/%s/json', $this->url, $ipAddress), [
                'key' => config('services.ipapi.token')
            ]);
        } else {
            $response = Http::get(sprintf('%s/%s/json', $this->url, $ipAddress));
        }

        if (! $response->ok()) {
            AdsBoard::report($response->body());
        }

        if ($response->ok() && ! $response->offsetExists('reserved')) {
            return $response->json();
        }

        return null;
    }
}
