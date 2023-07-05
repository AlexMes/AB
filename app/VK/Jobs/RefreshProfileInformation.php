<?php

namespace App\VK\Jobs;

use App\VK\Exceptions\VKException;
use App\VK\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshProfileInformation implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 3;

    /**
     * @var Profile
     */
    protected Profile $profile;

    /**
     * @var bool
     */
    protected bool $force = false;

    /**
     * RefreshProfileInformation constructor.
     *
     * @param Profile $profile
     * @param bool    $force
     */
    public function __construct(
        Profile $profile,
        bool $force = false
    ) {
        $this->force   = $force;
        $this->profile = $profile;
    }

    /**
     * Handle refreshing job
     *
     * @return void
     */
    public function handle()
    {
        if ($this->profile->hasErrors() && $this->force === false) {
            return;
        }

        try {
            $response = $this->profile->initVKApp()->me();

            $this->profile->update([
                'name'        => $response['first_name'] . ' ' . $response['last_name'],
                'issues_info' => null,
            ]);
        } catch (VKException $exception) {
            $this->profile->addError($exception);

            Log::error(
                sprintf(
                    "Cannot refresh vk profile [%d]. Error: %s",
                    $this->profile->id,
                    $exception
                )
            );
        }
    }
}
