<?php

namespace App\Console\Commands\Fixtures;

use App\Events\LeadAssigned;
use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class PlaceApexLeadsOnApexOffice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:place-apex-leads-on-apex-office';

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
        // Disable events, so leads are not sent ot customer.
        Event::fake([LeadAssigned::class]);


        // GAZRUSSI
        Lead::query()
            ->select([
                'id',
                'landing_id',
                DB::raw('date(created_at) as registration'),
            ])
            ->whereDoesntHave('assignment')
            ->whereIn('domain', ['gazrussi-capital.com'])
            ->get()
            ->groupBy('registration')
            ->each(function ($group, $date) {
                $order = LeadsOrder::firstOrCreate(['office_id' => 41, 'date' => $date]);

                /** @var LeadOrderRoute $route */
                $route = $order->routes()->firstOrCreate([
                    'offer_id' => 139
                ]);


                $group->each(
                    fn ($lead) =>
                    LeadOrderAssignment::create(['route_id' => $route->id,'lead_id' => $lead->id])
                );

                $route->update([
                    'leadsOrdered'  => $route->assignments()->count(),
                    'leadsReceived' => $route->assignments()->count(),
                ]);
            });

        // TON assignments.
        Lead::query()
            ->select([
                'id',
                'landing_id',
                DB::raw('date(created_at) as registration'),
            ])
            ->whereDoesntHave('assignment')
            ->whereIn('domain', ['rusgrams.com'])
            ->get()
            ->groupBy('registration')
            ->each(function ($group, $date) {
                $order = LeadsOrder::firstOrCreate(['office_id' => 41, 'date' => $date]);

                /** @var LeadOrderRoute $route */
                $route = $order->routes()->firstOrCreate([
                    'offer_id' => 3
                ]);

                $group->each(
                    fn ($lead) =>
                    LeadOrderAssignment::create(['route_id' => $route->id, 'lead_id' => $lead->id])
                );

                $route->update([
                    'leadsOrdered'  => $route->assignments()->count(),
                    'leadsReceived' => $route->assignments()->count(),
                ]);
            });

        return 0;
    }
}
