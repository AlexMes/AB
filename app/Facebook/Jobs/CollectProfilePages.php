<?php

namespace App\Facebook\Jobs;

use App\Facebook\Profile;
use App\Facebook\ProfilePage;
use Facebook\Exceptions\FacebookSDKException;
use FacebookAds\Http\Exception\EmptyResponseException;
use FacebookAds\Http\Exception\ServerException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class CollectProfilePages
 *
 * @package App\Facebook\Jobs
 */
class CollectProfilePages implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;


    /**
     * Tries before failure
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Profile model instance
     *
     * @var \App\Facebook\Profile
     */
    protected $profile;

    /**
     * Ignore profile errors.
     *
     * @var bool
     */
    protected $force = false;

    /**
     * CollectProfilePages constructor.
     *
     * @param \App\Facebook\Profile $profile
     * @param bool                  $force
     */
    public function __construct(Profile $profile, $force = false)
    {
        $this->profile = $profile;
        $this->force   = $force;
    }

    /**
     * Process a job
     *
     * @return void
     */
    public function handle()
    {
        if ($this->profile->hasIssues() === false || $this->force) {
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
        collect($this->retrieve())->each(function ($page) {
            $this->save($page);
        });
    }

    /**
     * Load profile pages from the Facebook API
     *
     * @return array
     */
    protected function retrieve()
    {
        try {
            return $this->profile
                ->getFacebookClient()
                ->get(
                    "/me/accounts?access_token={$this->profile->token}",
                    $this->profile->token
                )
                ->getDecodedBody()['data'];
        } catch (FacebookSDKException $facebookSDKException) {
            return [];
        } catch (ServerException | EmptyResponseException $exception) {
            Log::warning(
                sprintf(
                    "Server error happened when syncing [%d] %s",
                    $this->profile->id,
                    $this->profile->name
                )
            );

            return [];
        }
    }

    /**
     * Save page information into database
     *
     * @param array $page
     *
     * @return void
     */
    protected function save($page)
    {
        ProfilePage::query()->updateOrCreate(
            ['id' => $page['id']],
            array_merge(Arr::only($page, ['name','access_token']), ['profile_id' => $this->profile->id])
        );
    }
}
