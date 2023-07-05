<?php

namespace App\Services\Pourer\Jobs;

use App\Facebook\Account;
use App\Facebook\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class HandleAccounts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $accounts;
    /**
     * @var array
     */
    protected array $profile;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 2;

    /**
     * CollectAccounts constructor.
     *
     * @param array $accounts
     * @param array $profile
     */
    public function __construct(array $profile, array $accounts)
    {
        $this->accounts = $accounts;
        $this->profile  = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->process($this->storeProfile());
    }

    /**
     * Cache fb profile
     *
     * @return Profile
     */
    protected function storeProfile(): Profile
    {
        $profile =  Profile::updateOrCreate([
            'fbId'  => $this->profile['fbId'],
        ], [
            'name'  => $this->profile['name'],
            'token' => 'NONE',
        ]);

        if (!is_null($this->profile['issue_registered_at'] ?? null)) {
            $profile->addIssue(new \Exception($this->profile['issue_message']));
        } else {
            $profile->clearIssue();
        }

        return $profile;
    }

    /**
     * Cache fb ads accounts
     *
     * @param Profile $profile
     */
    public function process(Profile $profile)
    {
        collect($this->accounts)->each(function (array $account) use ($profile) {
            Account::updateOrCreate([
                'id'         => $account['id'],
                'profile_id' => $profile->id,
            ], Arr::only($account, Account::FB_FIELDS));
        });
    }

    /**
     * @return \Carbon\Carbon
     */
    public function retryUntil()
    {
        return now()->addSeconds(60);
    }
}
