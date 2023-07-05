<?php

namespace App\Console\Commands\Reports;

use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Office;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class YesterdayPercentage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:yst-percentage';

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
        $period = CarbonPeriod::since('2020-11-01')->days()->until(now());

        $orders = LeadsOrder::whereIn('office_id', Office::whereFrxTenantId(1)->pluck('id'))
            ->where('date', '>', '2020-11-01')
            ->pluck('id');
        $this->info(sprintf("Orders %s", $orders->count()));
        $routes = LeadOrderRoute::whereIn('order_id', $orders)->pluck('id');
        $this->info(sprintf("Routes %s", $routes->count()));

        $result      = [];

        foreach ($period as $date) {
            $result[] = [
                $date->toDateString(),
                $all = LeadOrderAssignment::whereIn('route_id', $routes)
                    ->whereDate('created_at', $date->toDateString())
                    ->whereDate('registered_at', $date->toDateString())
                    ->count(),

                $yst =  LeadOrderAssignment::whereIn('route_id', $routes)
                    ->whereDate('created_at', $date->toDateString())
                    ->whereDate('registered_at', $date->toImmutable()->subDay()->toDateString())
                    ->count(),
                $all > 0 ? round(($yst / $all) * 100, 2) : 0
            ];
        }

        $this->table(['date', 'all', 'yst', 'all/yst, %'], $result);

        return 0;
    }
}
