<?php

namespace App\Jobs;

use App\Lead;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransferLeadOrderRoute implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\LeadOrderRoute
     */
    public LeadOrderRoute $route;

    /**
     * @var \App\Manager
     */
    public Manager $manager;

    /**
     * @var \App\LeadOrderRoute
     */
    protected $targetRoute;

    /**
     * @var int
     */
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @param \App\LeadOrderRoute $route
     * @param \App\Manager        $manager
     *
     * @return void
     */
    public function __construct(LeadOrderRoute $route, Manager $manager)
    {
        $this->route   = $route;
        $this->manager = $manager;
    }

    /**
     * Execute the job.
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function handle()
    {
        $this->targetRoute = $this->resolveTargetRoute();

        if (! $this->locksObtained()) {
            // If we aren't able to lock both routes, retry in 5 seconds
            $this->release(5);

            return;
        }

        DB::transaction(function () {
            if ($this->targetRoute->trashed()) {
                $this->targetRoute->restore();
            }

            $this->targetRoute->update([
                'leadsOrdered'  => $this->route->leadsOrdered + $this->targetRoute->leadsOrdered,
                'leadsReceived' => $this->route->leadsReceived + $this->targetRoute->leadsReceived,
            ]);

            $this->route->update([
                'leadsReceived'  => 0,
                'leadsOrdered'   => 0,
            ]);

            $leads = $this->route->leads;

            $this->route->assignments()->update(['route_id' => $this->targetRoute->id]);

            $leads->each(function (Lead $lead) {
                $lead->addEvent(
                    Lead::ROUTE_TRANSFERRED,
                    [
                        'manager_id' => $this->manager->id,
                        'route_id'   => $this->targetRoute->id,
                    ],
                    [
                        'manager_id' => $this->route->manager_id,
                        'route_id'   => $this->route->id,
                    ],
                );
            });
        });
    }

    /**
     * Obtain lock for both routes
     *
     * @return bool
     */
    protected function locksObtained(): bool
    {
        return $this->route->lock() && $this->targetRoute->lock();
    }

    /**
     * @return \App\LeadOrderRoute|\Illuminate\Database\Eloquent\Model
     */
    protected function resolveTargetRoute(): LeadOrderRoute
    {
        if ($this->route->order->office_id !== $this->manager->office_id) {
            $orderId = $this->resolveOrder()->id;
        }

        return $this->manager->routes()->withTrashed()->firstOrCreate([
            'order_id' => $orderId ?? $this->route->order_id,
            'offer_id' => $this->route->offer_id
        ], ['leadsOrdered' => 0,'leadsReceived' => 0]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    protected function resolveOrder()
    {
        if (!$this->manager->routes()->first()) {
            return LeadsOrder::create([
                'office_id' => $this->manager->office_id,
                'date'      => $this->route->order->date,
                'branch_id' => $this->route->order->branch_id,
            ]);
        }

        return $this->manager->routes()->first()->order;
    }
}
