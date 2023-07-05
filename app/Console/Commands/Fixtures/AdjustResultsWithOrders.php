<?php

namespace App\Console\Commands\Fixtures;

use App\Http\Controllers\Users\Leads;
use App\LeadsOrder;
use App\Result;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdjustResultsWithOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:adjust-results-with-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Walk thorough orders in current month, and recalculate all of them';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = LeadsOrder::whereBetween('date', [now()->startOfMonth()->toDateString(),now()->toDateString()]);

        $orders->each(fn (LeadsOrder $leadsOrder) => $this->processOrder($leadsOrder));

        $this->info('Done');
    }

    /**
     * Process and recalculate leads order and corresponding results
     *
     * @param LeadsOrder $leadsOrder
     *
     * @return void
     */
    public function processOrder(LeadsOrder $leadsOrder)
    {
        $groups = $leadsOrder->routes()->select([
            DB::raw('sum("leadsReceived")'),
            'offer_id'
        ])->groupBy('offer_id')->get();

        $groups->each(fn ($group) => $this->recalculate($group, $leadsOrder));
    }

    /**
     * Dumb recalculations on results
     *
     * @param $group
     * @param mixed $order
     *
     * @return bool|int
     */
    public function recalculate($group, $order)
    {
        $result = Result::whereDate('date', $order->date)
            ->whereOfferId($group->offer_id)
            ->whereOfficeId($order->office_id)
            ->first();
        if ($result !== null) {
            if ($result->leads_count !== $group->sum) {
                return $result->update([
                    'leads_count' => $group->sum,
                ]);
            }
        } else {
            Result::create([
                'date'        => $order->date,
                'office_id'   => $order->office_id,
                'leads_count' => $group->sum,
                'offer_id'    => $group->offer_id,
            ]);
        }
    }
}
