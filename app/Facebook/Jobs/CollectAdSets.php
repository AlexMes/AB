<?php

namespace App\Facebook\Jobs;

use App\Facebook\Account;
use App\Facebook\AdSet;
use App\Facebook\Exceptions\BatchLimitException;
use App\Facebook\Profile;
use Facebook\Exceptions\FacebookSDKException;
use FacebookAds\Http\Exception\EmptyResponseException;
use FacebookAds\Http\Exception\ServerException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CollectAdSets implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /*
     * Number of attempts
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Facebook profile instance
     *
     * @var \App\Facebook\Profile
     */
    protected $profile;

    /**
     * Ignore profile errors.
     *
     * @var bool
     */
    protected $force;

    /**
     * CollectAccounts constructor.
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
        if ($this->profile->hasIssues() === false || ($this->force && !$this->profile->hasNotToken())) {
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
        $this->retrieve()->each(fn (array $adSet) => $this->save($adSet));
    }

    /**
     * Retrieve account adsets
     *
     * @param \FacebookAds\Object\AdAccount $account
     *
     * @return \Illuminate\Support\Collection
     */
    protected function retrieve()
    {
        try {
            return $this->getAdSets();
        } catch (FacebookSDKException | \FacebookAds\Http\Exception\AuthorizationException $exception) {
//            $this->profile->addIssue($exception);

            return collect();
        } catch (ServerException | EmptyResponseException | BatchLimitException $exception) {
            Log::warning(
                sprintf(
                    "Server error happened when syncing [%d] %s",
                    $this->profile->id,
                    $this->profile->name
                )
            );

            return collect();
        }
    }

    /**
     * Make batch query to Facebook for getting adSets related to current profile
     *
     * @param array $fields
     *
     * @throws \Facebook\Exceptions\FacebookSDKException|BatchLimitException
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAdSets(array $fields = AdSet::FB_FIELDS)
    {
        $batch = $this->profile->accounts()->pluck('id')->map(fn ($id) => [
            'method'       => 'GET',
            'relative_url' => sprintf("%s/adsets?fields=%s", $id, implode(',', $fields)),
        ])->toArray();

        return app('facebook-client')->forProfile($this->profile)->batch($batch, $this->profile->token);
    }

    /**
     * Save retrieved adset into database
     *
     * @param array $adset
     *
     * @return void
     */
    protected function save(array $adset)
    {
        AdSet::updateOrCreate(['id' => $adset['id']], $adset);
    }
}
