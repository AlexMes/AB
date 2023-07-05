<?php

namespace App\Binom\Commands;

use App\Binom;
use Illuminate\Console\Command;

class CacheTrafficSources extends Command
{
    /**
     * Command signature
     *
     * @var string
     */
    protected $signature   = 'binom:cache:traffic-sources';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Pull in traffic sources from binom';

    /**
     * Execute command
     *
     * @return void
     */
    public function handle()
    {
        Binom::active()->each(
            fn (Binom $binom) =>
            collect($binom->getTrafficSources())
                ->each(fn ($source) => $binom->trafficSources()
                ->updateOrCreate(['ts_id' => $source['id']], ['name' => $source['name']]))
        );
    }
}
