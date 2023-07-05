<?php

namespace App\Facebook\Jobs;

use App\Facebook\Account;
use App\Facebook\Profile;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CollectAdsAccounts
 *
 * @package App\Facebook\Jobs
 */
class CollectAccounts implements ShouldQueue
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
        collect($this->retrieve())->each(function ($account) {
            $this->save($account);
        });
    }

    /**
     * Load profile accounts from the Facebook API
     *
     * @return array
     */
    protected function retrieve()
    {
        try {
            return $this->profile
                ->getFacebookClient()
                ->get(
                    "/me/adaccounts?fields=id,account_id,account_status,age,amount_spent,balance,name,currency,disable_reason,funding_source_details",
                    $this->profile->token
                )
                ->getDecodedBody()['data'];
        } catch (FacebookSDKException $facebookSDKException) {
//            $this->profile->addIssue($facebookSDKException);

            return [];
        }
    }

    /**
     * Save account information into database
     *
     * @param array $account
     *
     * @return void
     */
    protected function save($account)
    {
        if ($account['id'] === 'act_467062327270428') {
            return;
        }
        Account::query()->updateOrCreate(
            ['id' => $account['id'], 'profile_id' => $this->profile->id],
            array_merge($account)
        );
    }
}
