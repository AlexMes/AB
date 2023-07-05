<?php

namespace App\Console\Commands;

use App\AdsBoard;
use App\LeadOrderRoute;
use App\LeadsOrder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;

class StartLeadOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts lead orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $time = collect(now()->subMinutes(5)->toPeriod(now(), "1m")->toArray())
            ->map(fn ($time) => $time->format('H:i'));
        $currentTime = $time->last();

        LeadsOrder::current()
            ->where(function (Builder $builder) use ($time, $currentTime) {
                return $builder->where('branch_id', 19)
                    ->where(
                        fn (Builder $query) => $query->where('start_at', $currentTime)
                            ->orWhereHas('routes', fn ($q) => $q->where('start_at', $currentTime))
                    )
                    ->orWhere('branch_id', '!=', 19)
                    ->where(
                        fn (Builder $query) => $query->whereIn('start_at', $time)
                            ->orWhereHas('routes', fn ($q) => $q->whereIn('start_at', $time))
                    );
            })
            ->orderBy('leads_orders.created_at')
            ->each(function (LeadsOrder $order) use ($time, $currentTime) {
                $routes = $order->routes()
                    ->paused()
                    ->where(function (Builder $builder) use ($time, $currentTime, $order) {
                        return $builder->when(
                            $order->branch_id === 19,
                            fn (Builder $query) => $query->where('start_at', $currentTime)
                                ->when($order->start_at === $currentTime, fn (Builder $q) => $q->orWhereNull('start_at')),
                            fn (Builder $query) => $query->whereIn('start_at', $time)
                                ->when($time->contains($order->start_at), fn (Builder $q) => $q->orWhereNull('start_at'))
                        );
                    });

                $updatedCnt = $routes->update(['status' => LeadOrderRoute::STATUS_ACTIVE]);

                if ($updatedCnt > 0) {
                    AdsBoard::report(
                        sprintf(
                            "[Order #%s (%s)](%s) started on schedule",
                            $order->id,
                            $order->office->name,
                            URL::to(sprintf("/leads-orders/%s", $order->id), [], true)
                        )
                    );
                }
            });

        return 0;
    }
}
