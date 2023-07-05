<?php

namespace App\Console\Commands;

use App\AdsBoard;
use App\Jobs\MorningLeftovers;
use Illuminate\Console\Command;

class DispatchLeadsDistribution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:distribute
                            {--except=* : except office IDs}
                            {--only=* : only office IDs}
                            {--offers=* : only offer IDs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        MorningLeftovers::dispatch(
            now()->toTimeString('minute'),
            $this->option('except'),
            $this->option('only'),
            $this->option('offers')
        )->onQueue(AdsBoard::QUEUE_DEFAULT);

        return 0;
    }
}
