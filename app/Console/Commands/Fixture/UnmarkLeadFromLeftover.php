<?php

namespace App\Console\Commands\Fixture;

use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UnmarkLeadFromLeftover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:unmark-lead-from-lo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $ids = [
        '293219'
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assignments = LeadOrderAssignment::whereIn('id', $this->ids)->get();

        foreach ($assignments as $assignment) {
            $this->markAsNormal($assignment);
        }

        return 0;
    }

    protected function markAsNormal(LeadOrderAssignment $assignment)
    {
        $offer = Offer::firstWhere('name', Str::after($assignment->route->offer->name, 'LO_'));

        $route = LeadOrderRoute::withTrashed()->firstOrCreate([
            'order_id'   => $assignment->route->order_id,
            'offer_id'   => $offer->id,
            'manager_id' => $assignment->route->manager_id,
        ]);

        $assignment->lead->update([
            'offer_id' => $offer->id,
        ]);

        $assignment->update([
            'route_id' => $route->id,
        ]);
    }
}
