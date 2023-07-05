<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use Illuminate\Console\Command;

class AssignLoDecEighteenToHaTwo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:assign-lo-dec-eighteen-to-ha-two';

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
        $los = Lead::leftovers(['2020-12-18','2020-12-18'])->get()->groupBy('offer_id');

        $los->each(function ($group, $offerId) {
            $route = LeadOrderRoute::firstOrCreate([
                'order_id'     => 3611,
                'offer_id'     => $offerId,
                'manager_id'   => 37,
                'leadsOrdered' => $group->count(),
            ]);

            $group->each(fn ($lead) => LeadOrderAssignment::create(['lead_id' => $lead->id,'route_id' => $route->id]));
        });


        return 0;
    }
}
