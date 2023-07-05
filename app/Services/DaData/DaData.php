<?php

namespace App\Services\DaData;

use Illuminate\Support\Facades\Http;

class DaData
{
    /**
     * API key
     *
     * @var string
     */
    protected ?string $token;
    /**
     * Secret key
     *
     * @var string
     */
    protected ?string $secret;

    /**
     * Base service cleaner url
     *
     * @var string
     */
    protected string $cleanUrl = 'https://cleaner.dadata.ru/api/v1/clean';

    /**
     * DaData constructor.
     *
     * @param string $token
     */
    public function __construct(?string $token, ?string $secret)
    {
        $this->token  = $token ?: null;
        $this->secret = $secret ?: null;
    }

    /**
     * @param string|null $value
     * @param string      $type
     *
     * @return array|null
     */
    public function clean(?string $value, string $type = 'name')
    {
        if (empty($value)) {
            return null;
        }

        try {
            $response = Http::asJson()->acceptJson()->withHeaders([
                'Authorization' => sprintf("Token %s", $this->token),
                'X-Secret'      => $this->secret,
            ])
                ->post(sprintf('%s/%s', $this->cleanUrl, $type), [$value])
                ->throw()
                ->json();

            return $response;
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
