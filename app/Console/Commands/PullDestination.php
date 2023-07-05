<?php

namespace App\Console\Commands;

use App\DestinationDrivers\CallResult;
use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\Jobs\ProcessCallResults;
use App\LeadDestination;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PullDestination extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destination:pull {destination} {--process=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull results from single destination';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $destination = LeadDestination::find($this->argument('destination'));

        if ($destination === null) {
            $this->error('Destination not found');

            return;
        }

        $implementation = $destination->initialize();

        if (! $implementation instanceof CollectsCallResults) {
            $this->error('Cant pull results from destinations, no implementation.');

            return;
        }

        $page     = 1;
        $leads    = 0;
        $deposits = 0;

        do {
            $results = $implementation->pullResults(Carbon::parse($destination->assignments()->min('created_at'))->subWeek(), $page++);
            if ($results->count() > 0) {
                $this->table(['ID','Status','Deposit'], $results->map(fn (CallResult $result) => [$result->getId(),$result->getStatus(), (string) $result->isDeposit()]));
                $leads += count($results);
                $deposits += $results->reduce(fn ($carry, $result) => $carry + ($result->isDeposit() ? 1 : 0), 0);

                $this->warn(sprintf('Got %s leads, %s deposits', count($results), $results->reduce(fn ($carry, $result) => $carry + ($result->isDeposit() ? 1 : 0), 0)));

                if ($this->option('process')) {
                    $this->info('Processing call results');
                    ProcessCallResults::dispatch($destination, $results);
                }
            } else {
                $this->info('No data returned from the API.');
            }
        } while ($results->count() !== 0);

        $this->info('Got data since '.Carbon::parse($destination->assignments()->min('created_at'))->subWeek().'. Leads: '.$leads.' Deposits: '.$deposits);

        return 0;
    }
}
