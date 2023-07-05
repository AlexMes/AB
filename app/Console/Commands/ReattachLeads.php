<?php

namespace App\Console\Commands;

use App\CRM\LeadOrderAssignment;
use App\Lead;
use App\LeadOrderRoute;
use Illuminate\Console\Command;

class ReattachLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:reattach-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reattach leads for j';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads =  Lead::query()
            ->whereIn('id', [787889, 787880, 787986, 786010, 786430, 786589, 786836, 787563, 786270, 788084, 786581, 786803, 788096, 786954, 787965, 786370, 786387, 786363, 786310, 787975, 786427, 788089, 787551, 786353, 786538, 786603, 786037, 786358, 786134, 788120, 787568, 787558])
            ->get()
            ->each(function (Lead $lead) {
                if ($lead->assignments()->doesntExist()) {
                    $setup = $lead->events()->where('events.type', 'assigned')->latest()->first();

                    $route = LeadOrderRoute::query()
                        ->where('offer_id', $setup['custom_data']['offer_id'])
                        ->where('manager_id', $setup['custom_data']['manager_id'])
                        ->where('order_id', $setup['custom_data']['order_id']);

                    $assignment = LeadOrderAssignment::create([
                        'registered_at' => $lead->created_at,
                        'created_at'    => $setup->created_at,
                        'route_id'      => $route->id,
                        'confirmed_at'  => now(),
                    ]);

                    $route->recalculate();

                    $this->info('Assignment '.$assignment->id.' created');
                }
            });




        return 0;
    }
}
