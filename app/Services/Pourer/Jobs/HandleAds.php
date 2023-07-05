<?php

namespace App\Services\Pourer\Jobs;

use App\Facebook\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class HandleAds implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $ads;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 2;

    /**
     * HandleAdSets constructor.
     *
     * @param array $ads
     */
    public function __construct(array $ads)
    {
        $this->ads = $ads;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->process();
    }

    /**
     * Cache campaigns
     *
     * @return void
     */
    public function process()
    {
        collect($this->ads)->each(fn (array $ad) => $this->save($ad));
    }

    /**
     * Save retrieved campaign into database
     *
     * @param array $ad
     *
     * @return void
     */
    protected function save(array $ad)
    {
        Ad::updateOrCreate(['id' => $ad['id']], Arr::only($ad, Ad::FB_FIELDS));
    }

    /**
     * @return \Carbon\Carbon
     */
    public function retryUntil()
    {
        return now()->addSeconds(60);
    }
}
