<?php

namespace App\Console\Commands;

use App\AdsBoard;
use App\Jobs\LogOfferStats;
use App\Offer;
use Illuminate\Console\Command;

class CollectOfferStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:collect-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log each offer data to db';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Offer::each(fn ($offer) => LogOfferStats::dispatch($offer)->onQueue(AdsBoard::QUEUE_DEFAULT));
    }
}
