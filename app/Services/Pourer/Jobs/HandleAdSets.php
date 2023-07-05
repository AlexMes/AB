<?php

namespace App\Services\Pourer\Jobs;

use App\Facebook\AdSet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class HandleAdSets implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $adSets;

    /**
     * HandleAdSets constructor.
     *
     * @param array $adSets
     */
    public function __construct(array $adSets)
    {
        $this->adSets = $adSets;
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
        collect($this->adSets)->each(fn (array $adSet) => $this->save($adSet));
    }

    /**
     * Save retrieved campaign into database
     *
     * @param array $adSet
     *
     * @return void
     */
    protected function save(array $adSet)
    {
        AdSet::updateOrCreate(['id' => $adSet['id']], Arr::only($adSet, AdSet::FB_FIELDS));
    }
}
