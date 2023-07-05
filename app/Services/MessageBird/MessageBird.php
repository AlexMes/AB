<?php

namespace App\Services\MessageBird;

use Illuminate\Support\Facades\Http;

class MessageBird
{
    /**
     * Access token for service
     *
     * @var string
     */
    protected string $token;

    /**
     * MessageBird constructor.
     *
     * @param string $token
     *
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Determines is phone number valid
     *
     * @param string $phone
     *
     * @return bool
     */
    public function isValid(string $phone): bool
    {
        $response = Http::withHeaders([
            'Authorization' => sprintf("AccessKey %s", $this->token)
        ])->get(sprintf("https://rest.messagebird.com/lookup/%s", $phone));

        return $response->ok();
    }

    /**
     * Get phone code from the number
     *
     * @param string $phone
     *
     * @return array|mixed
     */
    public function lookup(string $phone)
    {
        $response = Http::withHeaders([
            'Authorization' => sprintf("AccessKey %s", $this->token)
        ])->get(sprintf("https://rest.messagebird.com/lookup/%s", $phone));

        return $response->json();
    }
}
