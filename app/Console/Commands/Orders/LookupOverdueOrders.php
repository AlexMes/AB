<?php

namespace App\Console\Commands\Orders;

use App\AdsBoard;
use App\Notifications\Orders\Overdue;
use App\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Throwable;

class LookupOverdueOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:lookup-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lookup database for due domain orders';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Order::overdue()->whereRaw('links_count > links_done_count')->each(function (Order $order) {
            // Allow one notification per order per hour.
            Redis::throttle("order-{$order->id}-due")->allow(1)->every(3600)->then(function () use ($order) {
                Notification::send(AdsBoard::devsChannel(), new Overdue($order));
            }, function (Throwable $e) {
                $this->info(sprintf("Locked notification %s", $e->getMessage()));
            });
        });
    }
}
