<?php

namespace App\VK\Jobs;

use App\VK\Models\Profile;
use App\VK\VKApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConnectVKProfile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * VK profile access token
     *
     * @var string|null
     */
    public ?string $token;

    /**
     * Set maximum number of attempts
     *
     * @var int
     */
    public $tries = 1;

    /**
     * StoreNewAccount constructor.
     *
     * @param string $token
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Process a job
     *
     * @param VKApp $vkApp
     *
     *@throws \Throwable
     * @throws \App\VK\Exceptions\Unauthorized
     *
     * @return void
     *
     */
    public function handle(VKApp $vkApp)
    {
        if ($this->token === null) {
            return null;
        }

        /** @var array $response */
        $response = $vkApp->useToken($this->token)->me();

        $profile = Profile::query()->updateOrCreate(
            ['vk_id' => $response['id']],
            [
                'name'  => $response['first_name'] . ' ' . $response['last_name'],
                'token' => $this->token,
            ]
        );

        $profile->refreshVKData(true);
    }
}
