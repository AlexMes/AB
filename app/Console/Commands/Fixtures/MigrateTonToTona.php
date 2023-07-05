<?php

namespace App\Console\Commands\Fixtures;

use App\Affiliate;
use App\Deposit;
use App\Lead;
use App\LeadOrderRoute;
use App\LeadsOrder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MigrateTonToTona extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:migrate-ton-to-tona';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate affiliate leads to offer TONA';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     *
     * @return int
     */
    public function handle()
    {
        // Migrate affiliates
        $this->task('Migrate affiliates', fn () => Affiliate::whereOfferId(3)->update(['offer_id' => 53]));
        // Migrate leads
        $this->task(
            'Migrate leads',
            fn () =>
            Lead::where('offer_id', 3)
                ->whereNotNull('affiliate_id')
                ->update(['offer_id' => 53])
        );
        // Migrate deposits
        $this->task(
            'Migrate deposits',
            fn () =>
            Deposit::where('offer_id', 3)
                ->whereHas('lead', function (Builder $query) {
                    return $query->whereNotNull('affiliate_id');
                })->update(['offer_id' => 53])
        );
        // Migrate routes, depending on leads
        $this->task('Migrate routes', function () {
            $routes = LeadOrderRoute::whereOfferId(3)
                ->whereHas('order', function (Builder $builder) {
                    $builder->whereDate('date', '>', '2020-06-30');
                })->cursor();

            foreach ($routes as $route) {
                $this->splitRoutesByAssignmentOffer($route);
            }
        });

        // Update results accordingly
        $this->task('Update results', function () {
            $hack = new AdjustResultsWithOrders();
            $orders = LeadsOrder::whereDate('date', '>', '2020-06-30')->cursor();

            foreach ($orders as $order) {
                $hack->processOrder($order);
            }
        });

        $this->info('Hopefully I don`t screwed whole fucking month, huh');

        return 0;
    }

    /**
     * @param LeadOrderRoute $route
     *
     * @throws \Exception|\Throwable
     */
    protected function splitRoutesByAssignmentOffer($route)
    {
        // If somehow we get another offer, break immediately
        if ($route->offer_id !== 3) {
            throw new \Exception('You fucking dumbass');
        }

        // Get assigned leads from affiliates
        $foreignAssignments = $route->assignments()->whereHas(
            'lead',
            fn ($query) => $query->where('offer_id', 53)
        )->get();

        // No assignments - fuck off
        if ($foreignAssignments->isEmpty()) {
            return;
        }

        // Some shit found - lets roll
        DB::transaction(function () use ($foreignAssignments, $route) {
            $targetRoute = LeadOrderRoute::firstOrCreate([
                'order_id'   => $route->order_id,
                'offer_id'   => 53,
                'manager_id' => $route->manager_id
            ], ['leadsOrdered' => 0, 'leadsReceived' => 0]);

            while ($this->locksAreNotObtained($route, $targetRoute)) {
                // Wait until we complete this little shit
                sleep(5);
            }

            // Here we have both locks obtained.
            // Update target route first
            $targetRoute->update([
                'leadsOrdered'  => $foreignAssignments->count(),
                'leadsReceived' => $foreignAssignments->count(),
            ]);
            // Update assignments to match new route
            $foreignAssignments->each->update(['route_id' => $targetRoute->id]);

            // Update old route last
            $route->update([
                'leadsOrdered'  => $route->leadsOrdered - $foreignAssignments->count(),
                'leadsReceived' => $route->leadsReceived - $foreignAssignments->count(),
            ]);
        });
    }

    /**
     * As we work in single thread, no way to release back on queue,
     * therefore we need opposite results of route locks state,
     * to run dumb while loop until server does what we need, or
     * dies trying.
     *
     * @param \App\LeadOrderRoute $route
     * @param \App\LeadOrderRoute $targetRoute
     *
     * @return bool
     */
    protected function locksAreNotObtained(LeadOrderRoute $route, LeadOrderRoute $targetRoute)
    {
        $locks = $route->lock() && $targetRoute->lock();

        return ! $locks;
    }
}
