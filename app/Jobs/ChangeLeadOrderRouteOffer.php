<?php

namespace App\Jobs;

use App\LeadOrderRoute;
use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ChangeLeadOrderRouteOffer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    /**
     * @var LeadOrderRoute
     */
    public LeadOrderRoute $route;

    /**
     * @var Offer
     */
    public Offer $offer;

    /**
     * @var LeadOrderRoute
     */
    protected $targetRoute;

    /**
     * @var int
     */
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @param LeadOrderRoute $route
     * @param Offer          $offer
     */
    public function __construct(LeadOrderRoute $route, Offer $offer)
    {
        $this->route = $route;
        $this->offer = $offer;
    }

    /**
     * Execute the job.
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
            if ($this->targetRoute->deleted_at !== null) {
                $this->targetRoute->restore();
            }

            $diff = $this->route->leadsOrdered - $this->route->leadsReceived;

            $this->targetRoute->update([
                'leadsOrdered'     => $this->targetRoute->leadsOrdered + $diff,
                'last_received_at' => $this->route->last_received_at,
                'priority'         => $this->route->priority,
                'start_at'         => $this->route->start_at,
                'stop_at'          => $this->route->stop_at,
            ]);

            $this->route->update([
                'leadsOrdered'  => $this->route->leadsOrdered - $diff,
            ]);
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
    protected function resolveTargetRoute()
    {
        return $this->route->manager->routes()->withTrashed()->firstOrCreate(
            [
                'order_id' => $this->route->order_id,
                'offer_id' => $this->offer->id
            ],
            ['leadsOrdered'  => 0, 'leadsReceived' => 0],
        );
    }
}
