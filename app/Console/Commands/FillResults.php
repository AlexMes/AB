<?php

namespace App\Console\Commands;

use App\LeadsOrder;
use App\Result;
use Illuminate\Console\Command;

class FillResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = LeadsOrder::where('id', '>=', 31)->cursor();

        foreach ($orders as $order) {
            $this->stubResultsForOrder($order);
        }
    }

    protected function stubResultsForOrder(LeadsOrder $order)
    {
        $offers = $order->routes()->withTrashed()->pluck('offer_id')->unique();

        foreach ($offers as $offer) {
            $result =   Result::whereDate('date', $order->date)
                ->where('offer_id', $offer)
                ->where('office_id', $order->office_id)
                ->first();

            if ($result === null) {
                $result  = Result::create([
                    'date'      => $order->date,
                    'offer_id'  => $offer,
                    'office_id' => $order->office_id
                ]);
            }
            $result->updateLeadsCount();
        }
    }
}
