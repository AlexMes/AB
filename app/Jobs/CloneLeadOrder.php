<?php

namespace App\Jobs;

use App\LeadOrderRoute;
use App\LeadsOrder;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CloneLeadOrder implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\LeadsOrder
     */
    public LeadsOrder $order;

    /**
     * @var \Carbon\CarbonInterface
     */
    public CarbonInterface $date;

    /**
     * Create a new job instance.
     *
     * @param \App\LeadsOrder         $order
     * @param \Carbon\CarbonInterface $date
     *
     * @return void
     */
    public function __construct(LeadsOrder $order, CarbonInterface $date)
    {
        $this->order = $order;
        $this->date  = $date;
    }

    /**
     * Execute the job.
     *
     * @throws \Throwable
     *
     * @return void
     *
     */
    public function handle()
    {
        DB::transaction(function () {
            $clone = LeadsOrder::firstOrCreate(
                [
                    'date'      => $this->date,
                    'office_id' => $this->order->office_id,
                    'branch_id' => app()->runningInConsole() || auth()->user()->isAdmin()
                        ? $this->order->branch_id
                        : auth()->user()->branch_id,
                ],
                [
                    'start_at'              => $this->order->start_at,
                    'stop_at'               => $this->order->stop_at,
                    'destination_id'        => $this->order->destination_id,
                    'autodelete_duplicates' => $this->order->autodelete_duplicates,
                ]
            );

            $this->order->routes()->visible()->get()->each(function (LeadOrderRoute $route) use ($clone) {
                $clone->routes()->firstOrCreate(
                    [
                        'offer_id'   => $route->offer_id,
                        'manager_id' => $route->manager_id,
                    ],
                    [
                        'leadsOrdered'   => $route->leadsOrdered,
                        'start_at'       => $route->start_at,
                        'stop_at'        => $route->stop_at,
                        'priority'       => $route->priority,
                        'destination_id' => $route->destination_id,
                    ],
                );
            });
        });
    }
}
