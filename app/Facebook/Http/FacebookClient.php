<?php

namespace App\Facebook\Http;

use App\Facebook\Exceptions\BatchLimitException;
use App\Facebook\Exceptions\FacebookClientNotDefinedException;
use App\Facebook\FacebookApp;
use App\Facebook\Profile;
use Facebook\Facebook;

class FacebookClient
{
    protected FacebookApp $facebookApp;
    protected Profile $profile;
    protected $client;

    /**
     * FacebookClient constructor.
     *
     * @param mixed $client
     */
    public function __construct($client = null)
    {
        $this->client = $client;
    }

    /**
     * @return Facebook
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set profile which tell us which fb client we should use
     *
     * @param Profile $profile
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return FacebookClient
     */
    public function forProfile(Profile $profile)
    {
        $this->facebookApp = FacebookApp::init($profile);
        $this->client      = $this->facebookApp->facebook();

        return $this;
    }

    /**
     * Set app which tell us which fb client we should use
     *
     * @param FacebookApp $app
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return FacebookClient
     */
    public function forApp(FacebookApp $app)
    {
        $this->facebookApp = $app;
        $this->client      = $app->facebook();

        return $this;
    }

    /**
     * Make batch query to Facebook
     *
     * @param array  $batch
     * @param string $token
     * @param bool   $pretty = true
     *
     * @throws BatchLimitException
     * @throws FacebookClientNotDefinedException
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function batch(array $batch, string $token, bool $pretty = true)
    {
        throw_if(count($batch) > 50, new BatchLimitException());
        throw_unless($this->client, new FacebookClientNotDefinedException());

        $response = $this->client->post('', [
            'batch' => json_encode($batch)
        ], $token)
            ->getDecodedBody();

        if ($pretty) {
            return collect($response)
                ->flatMap(function (array $piece) {
                    return json_decode($piece['body'], true)['data'] ?? []; // skip error
                });
        }

        return $response;
    }
}
