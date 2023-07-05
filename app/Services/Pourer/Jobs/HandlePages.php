<?php

namespace App\Services\Pourer\Jobs;

use App\Facebook\Profile;
use App\Facebook\ProfilePage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class HandlePages implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $pages;
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
     * @param array $pages
     * @param array $profile
     */
    public function __construct(array $profile, array $pages)
    {
        $this->pages   = $pages;
        $this->profile = $profile;
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
        return Profile::updateOrCreate([
            'fbId'  => $this->profile['fbId'],
        ], [
            'name'  => $this->profile['name'],
            'token' => 'NONE',
        ]);
    }

    /**
     * Cache fb ads accounts
     *
     * @param Profile $profile
     */
    public function process(Profile $profile)
    {
        collect($this->pages)->each(function (array $page) use ($profile) {
            ProfilePage::updateOrCreate([
                'id'         => $page['id'],
                'profile_id' => $profile->id,
            ], Arr::only($page, ['name','access_token']));
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
