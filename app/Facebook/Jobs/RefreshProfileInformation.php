<?php

namespace App\Facebook\Jobs;

use App\Facebook\Profile;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshProfileInformation implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /**
     * Facebook profile, that must be refreshed
     *
     * @var \App\Facebook\Profile
     */
    protected $profile;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Forced synchronization
     *
     * @var bool
     */
    protected $force;

    /**
     * RefreshProfileInformation constructor.
     *
     * @param \App\Facebook\Profile $profile
     * @param bool                  $force
     *
     * @return void
     */
    public function __construct(Profile $profile, $force = false)
    {
        $this->profile = $profile;
        $this->force   = $force;
    }

    /**
     * Handle refreshing job
     *
     * @return void
     */
    public function handle()
    {
        // If no issues registered for account, sync it
        // If we are in forced mode, sync it
        if ($this->profile->hasIssues() === false || $this->force === true) {
            $this->synchronize();
        }
    }

    /**
     * Run synchronization
     *
     * @return void
     */
    protected function synchronize()
    {
        try {
            $response = $this->profile
                ->getFacebookClient()
                ->get('/me', $this->profile->token)
                ->getDecodedBody();

            $this->profile->update([
                'name' => $response['name']
            ]);

            // At this point does not failed, and we can purge all related issues
            if ($this->profile->hasIssues()) {
                \App\Facebook\Events\Profiles\Restored::dispatch($this->profile);
                $this->profile->clearIssue();
            }
        } catch (FacebookSDKException $exception) {
            if (! $this->profile->hasIssues()) {
                \App\Facebook\Events\Profiles\Banned::dispatch($this->profile, $this->profile->addIssue($exception));
            }
        }
    }
}
