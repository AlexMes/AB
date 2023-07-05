<?php

namespace App\Console\Commands\Fixtures;

use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveDagaNightRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:move-daga-night-orders';

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
        $office = Office::find(55);

        $routes = LeadOrderRoute::query()
            ->with(['order:id,date'])
            ->whereHas('order', fn ($query) => $query->whereDate('date', '>=', '2020-12-01'))
            ->whereIn('manager_id', $office->managers()->pluck('id'))
            ->where('offer_id', 40)
            ->get();

        foreach ($routes as $route) {
            DB::beginTransaction();
            /** @var \App\LeadsOrder $order */
            $order = $office->orders()->firstOrCreate([
                'date' => $route->order->date,
            ]);

            $route->update(['order_id' => $order->id]);

            DB::commit();
        }

        return 0;
    }
}
