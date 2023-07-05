<?php

namespace App\Console\Commands\Fixtures;

use App\OfferStatisticsLog;
use Illuminate\Console\Command;

class TruncateOfferStatisticsLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:truncate-offer-statistics-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        OfferStatisticsLog::truncate();

        return 0;
    }
}
