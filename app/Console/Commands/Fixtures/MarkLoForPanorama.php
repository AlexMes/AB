<?php

namespace App\Console\Commands\Fixtures;

use App\CRM\Queries\ManagerAssignments;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Offer;
use Illuminate\Console\Command;

class MarkLoForPanorama extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:mark-lo-for-panorama';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark leads as leftovers for panorama';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ids = ManagerAssignments::query()
            ->forOffice(10)
            ->forPeriod('2021-02-03')
            ->forRegistrationPeriod('2021-02-02')
            ->forOffer(215)
            ->get();

        $assignments =  LeadOrderAssignment::with('route', 'route.offer')->whereIn('id', $ids->pluck('id'))->get();


        foreach ($assignments as $assignment) {
            $this->markAsLeftovers($assignment);
        }

        return 0;
    }

    protected function markAsLeftovers(LeadOrderAssignment $assignment)
    {
        $offer = Offer::firstOrCreate(['name' => 'LO_' . $assignment->route->offer->name]);

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
