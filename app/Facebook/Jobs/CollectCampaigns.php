<?php

namespace App\Facebook\Jobs;

use App\Facebook\Account;
use App\Facebook\Campaign;
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

class CollectCampaigns implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;

    /**
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
        if ($this->profile->hasIssues() === false) {
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
        $this->retrieve()->each(fn (array $campaign) => $this->save($campaign));
    }

    /**
     * Retrieve account campaigns
     *
     * @return \Illuminate\Support\Collection
     */
    protected function retrieve()
    {
        try {
            return $this->getCampaigns();
        } catch (FacebookSDKException | \FacebookAds\Http\Exception\AuthorizationException $exception) {
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
     * Make batch query to Facebook for getting campaigns related to current profile
     *
     * @param array $fields
     *
     * @throws \Facebook\Exceptions\FacebookSDKException|BatchLimitException
     *
     * @return \Illuminate\Support\Collection
     *
     */
    protected function getCampaigns(array $fields = Campaign::FB_FIELDS)
    {
        $batch = $this->profile->accounts()->pluck('id')->map(fn ($id) => [
            'method'       => 'GET',
            'relative_url' => sprintf("%s/campaigns?fields=%s", $id, implode(',', $fields)),
        ])->toArray();

        return app('facebook-client')->forProfile($this->profile)->batch($batch, $this->profile->token);
    }

    /**
     * Save retrieved campaign into database
     *
     * @param array $campaign
     *
     * @return void
     */
    protected function save(array $campaign)
    {
        Campaign::updateOrCreate(['id' => $campaign['id']], $campaign);
    }
}
