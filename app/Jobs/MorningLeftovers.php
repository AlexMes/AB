<?php

namespace App\Jobs;

use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\LeadsOrder;
use App\Offer;
use App\Office;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MorningLeftovers implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    protected $time;

    /**
     * @var array
     */
    protected array $exceptOffices;

    /**
     * @var array
     */
    protected array $onlyOffices;

    /**
     * @var array
     */
    protected array $onlyOffers;

    /**
     * Create a new job instance.
     *
     * @param string $time
     * @param array  $exceptOffices
     * @param array  $onlyOffices
     * @param array  $onlyOffers
     */
    public function __construct($time, $exceptOffices = [], $onlyOffices = [], $onlyOffers = [])
    {
        $this->time          = $time;
        $this->exceptOffices = $exceptOffices;
        $this->onlyOffices   = $onlyOffices;
        $this->onlyOffers    = $onlyOffers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $morningOffices = Office::whereDistributionEnabled(true)
            ->whereHas('morningBranches', fn ($q) => $q->where('time', $this->time))
            ->with(['morningBranches' => fn ($q) => $q->where('time', $this->time)])
            ->get(['id']);

        logger('Started. Time: ' . $this->time, ['morning-distribution']);

        if ($morningOffices->count() < 1) {
            logger('No office with appropriate branches found. Exit.', ['morning-distribution']);

            return;
        }

        $morningBranches = $morningOffices->flatMap->morningBranches;

        logger('Pivot: ' . json_encode($morningBranches->pluck('pivot')->toArray()), ['morning-distribution']);

        $query = LeadsOrder::current()
            ->with(['routes' => fn ($query) => $query->active()->incomplete()
            ->when($this->onlyOffers, fn ($q) => $q->whereIn('routes.offer_id', $this->onlyOffers))])
            ->whereHas('routes', fn ($query) => $query->active()->incomplete()
            ->when($this->onlyOffers, fn ($q) => $q->whereIn('routes.offer_id', $this->onlyOffers)))
            ->when($this->exceptOffices, fn ($query, $input) => $query->whereNotIn('office_id', $input))
            ->when($this->onlyOffices, fn ($query, $input) => $query->whereIn('office_id', $input))
            ->where(function ($query) use ($morningOffices) {
                foreach ($morningOffices as $morningOffice) {
                    $query->orWhere('office_id', $morningOffice->id)
                        ->whereIn('branch_id', $morningOffice->morningBranches->pluck('id'));
                }

                return $query;
            });
        logger('Order query: ' . $query->toSql(), ['morning-distribution']);
        logger('Order bindings: ' . json_encode($query->getBindings()), ['morning-distribution']);

        $orders = $query->get();
        logger(sprintf('Orders found: %s, %s', $orders->count(), json_encode($orders->pluck('id'))), ['morning-distribution']);

        /** @var LeadsOrder $order */
        foreach ($orders as $order) {
            $morningBranch = $morningBranches->where('pivot.office_id', $order->office_id)
                ->where('pivot.branch_id', $order->branch_id)
                ->first();
            if ($morningBranch === null) {
                logger(sprintf('Something weird happened. Skip. Order #%s', $order->id), ['morning-distribution']);
                continue;
            }

            $toReceive   = max(0, $order->leads_ordered - $order->leads_received);
            $deliverAt   = now()->floorSeconds(60)->addMinute();
            $step        = $toReceive > 0 ? $morningBranch->pivot->duration / $toReceive : 0;
            $addMinCnt   = 0;
            logger(sprintf(
                'Morning lo started, for order: %s, deliver until:%s, deliver_at:%s, minLeft:%s, toReceive:%s, step:%s',
                $order->id,
                now()->addMinutes($morningBranch->pivot->duration + 1)->toDateTimeString('minute'),
                $deliverAt->toDateTimeString(),
                $morningBranch->pivot->duration,
                $toReceive,
                $step
            ), ['morning-distribution']);
            foreach ($order->routes as $route) {
                $leads = Lead::leftovers()
                    ->where('created_at', '>=', now()->subDay()->startOfDay()->toDateTimeString())
                    ->whereNotIn('offer_id', $this->getOffersToAlwaysSkip())
                    ->where('offer_id', $route->offer_id)
                    ->latest()
                    ->limit(max(0, $route->leadsOrdered - $route->leadsReceived))
                    ->get();

                if ($leads->isEmpty()) {
                    logger(sprintf('No leads found for route: %s', $route->id), ['morning-distribution']);
                }

                foreach ($leads as $lead) {
                    LeadAssigner::dispatchNow(
                        $lead,
                        null,
                        [$order->office_id],
                        null,
                        null,
                        false,
                        $deliverAt,
                        false,
                        false,
                        null,
                        $route,
                    );
                    logger(sprintf('Dispatch lead %s to order %s, route %s', $lead->id, $order->id, $route->id), ['morning-distribution']);

                    $addMinCnt += $step;
                    if ($addMinCnt >= 1) {
                        $deliverAt->addMinutes((int)$addMinCnt);
                        $addMinCnt = $addMinCnt - (int)$addMinCnt;
                    }
                    logger(sprintf(
                        'Next iteration. Deliver at:%s, addMin:%s',
                        $deliverAt->toDateTimeString(),
                        $addMinCnt
                    ), ['morning-distribution']);
                }
            }
        }
    }

    /**
     * Always skip offers, prefixes with LO_ as there are only old leads
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getOffersToAlwaysSkip(): \Illuminate\Support\Collection
    {
        return Offer::where('name', 'like', 'LO\_%')->pluck('id');
    }
}
