<?php

namespace App\Console\Commands\Fixtures;

use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Order;
use Illuminate\Console\Command;

class FixAssignmentsNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:recalculate-assignments-amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Walk thorough assignment routes, and fixes ordered and received leads counts depending on real count of assignments';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = LeadsOrder::whereBetween('date', [now()->startOfMonth()->subDay(), now()])->get();

        $orders->each(fn (LeadsOrder $leadsOrder) => $this->processOrder($leadsOrder));
    }

    /**
     * Work with leads order
     *
     * @param LeadsOrder $leadsOrder
     *
     * @return void
     */
    public function processOrder(LeadsOrder $leadsOrder)
    {
        $leadsOrder->routes()->withTrashed()->each(fn (LeadOrderRoute $route) => $this->recalculate($route));
    }

    /**
     * Check and update route if required
     *
     * @param LeadOrderRoute $route
     *
     * @return void
     */
    public function recalculate(LeadOrderRoute $route)
    {
        $this->task('Checking route ' . $route->id, function () use ($route) {
            if ($route->isCompleted()) {
                $assignments = $route->assignments()->count();

                if ($assignments !== $route->leadsReceived) {
                    return $route->update([
                        'leadsReceived' => $assignments,
                    ]);
                }
            }

            return true;
        });
    }
}
