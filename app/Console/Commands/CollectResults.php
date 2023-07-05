<?php

namespace App\Console\Commands;

use App\AdsBoard;
use App\Jobs\PullResultsFromDestination;
use App\LeadDestination;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CollectResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:results
                            {date? : Collect results since specified date}
                            {--driver= : Collect only results for specific driver }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect statuses and deposits from destinations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::parse($this->argument('date') ?? now()->subMonths(2));

        LeadDestination::whereHas('assignments')
            ->active()
            ->when($this->option('driver'), fn ($query, $input) => $query->where('driver', $input))
            ->orderByDesc('id')
            ->get()
            ->each(function (LeadDestination $destination) use ($date) {
                $this->info('Starting collection since '.$date->toDateString(). ' for '.$destination->name);
                PullResultsFromDestination::dispatch($destination, $date->toDateString())->onQueue(AdsBoard::QUEUE_IMPORTS);
            });

        return 0;
    }
}
