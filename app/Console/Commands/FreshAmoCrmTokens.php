<?php

namespace App\Console\Commands;

use App\LeadDestination;
use App\LeadDestinationDriver;
use Illuminate\Console\Command;

class FreshAmoCrmTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destination:fresh-amocrm-tokens';

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
        /** @var LeadDestination $destination */
        foreach (LeadDestination::where('driver', LeadDestinationDriver::AMOCRM)->get() as $destination) {
            $driver = $destination->initialize();
            if ($driver->getRefreshToken() === null) {
                $driver->freshTokensByCode();
            }
        }

        return 0;
    }
}
