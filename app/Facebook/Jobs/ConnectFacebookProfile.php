<?php

namespace App\Facebook\Jobs;

use App\Facebook\FacebookApp;
use App\Facebook\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConnectFacebookProfile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * FB account access token
     *
     * @var string
     */
    protected $token;

    protected FacebookApp $app;

    /**
     * @var integer
     */
    public $user_id;


    /**
     * Set maximum number of attempts
     *
     * @var int
     */
    public $tries = 1;

    /**
     * StoreNewAccount constructor.
     *
     * @param string      $token
     * @param FacebookApp $app
     * @param integer     $user_id
     *
     * @return void
     */
    public function __construct($token, FacebookApp $app, $user_id)
    {
        $this->token   = $token;
        $this->app     = $app;
        $this->user_id = $user_id;
    }

    /**
     * Process a job
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return void
     */
    public function handle()
    {
        if ($this->token === null) {
            return null;
        }

        /** @var array $response */
        $response = $this->app
            ->facebook()
            ->get('/me', $this->token)
            ->getDecodedBody();

        $profile = Profile::query()->updateOrCreate(
            ['fbId'  => $response['id']],
            [
                'name'   => $response['name'],
                'token'  => $this->token,
                'app_id' => $this->app->id,
                'user_id'=> $this->user_id,
            ]
        );

        $profile->refreshFacebookData(true);
    }
}
