<?php

namespace App\Console\Commands\Fixture;

use App\LeadOrderRoute;
use App\LeadsOrder;
use Illuminate\Console\Command;

class ReaarrangeRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:rearrange-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move routes to matching orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = LeadOrderRoute::with(['order','manager'])->whereIn('id', [288300, 289080, 287826, 288009, 283555, 283690])->get();

        foreach ($routes as $route) {
            $order = LeadsOrder::firstOrCreate([
                'office_id' => $route->manager->office_id,
                'date'      => $route->order->date,
            ]);

            /** @var \App\LeadOrderRoute */
            $possibleDuplicate = $order->routes()->withTrashed()->where([
                'offer_id'   => $route->offer_id,
                'manager_id' => $route->manager_id
            ])->first();

            if ($possibleDuplicate === null) {
                $route->update(['order_id' => $order->id]);
            }

            if ($possibleDuplicate !== null && $possibleDuplicate->trashed()) {
                $possibleDuplicate->restore();
            }

            if ($possibleDuplicate !== null) {
                $route->assignments()->update([
                    'route_id' => $possibleDuplicate->id,
                ]);
            }
        }

        return 0;
    }
}
