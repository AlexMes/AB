<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Offer;
use Illuminate\Console\Command;

class CeeDjayLeftovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:mark-leads-as-lefovers-for-cj';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $orders = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->orders = LeadsOrder::whereBetween('date', [
            '2021-01-01','2021-01-20'
        ])->where('office_id', 5)->whereHas('routes', function ($query) {
            return $query->whereIn('offer_id', [170,139]);
        })->pluck('id');

        $this->gazrussi();
        $this->gazrussiMrq();

        return 0;
    }

    protected function gazrussi()
    {
        $offer  = Offer::firstOrCreate(['name' => 'LO_GAZRUSSI']);
        $routes = LeadOrderRoute::whereIn('order_id', $this->orders)->where('offer_id', 139);

        $leads = Lead::whereIn('id', LeadOrderAssignment::whereIn('route_id', $routes->pluck('id'))->pluck('lead_id'));

        $leads->update([
            'offer_id' => $offer->id
        ]);

        $routes->update([
            'offer_id' => $offer->id,
        ]);
    }

    protected function gazrussiMrq()
    {
        $offer  = Offer::firstOrCreate(['name' => 'LO_GAZRUSSI_MARKIZ2']);
        $routes = LeadOrderRoute::whereIn('order_id', $this->orders)->where('offer_id', 170);

        $leads = Lead::whereIn('id', LeadOrderAssignment::whereIn('route_id', $routes->pluck('id'))->pluck('lead_id'));

        $leads->update([
            'offer_id' => $offer->id
        ]);

        $routes->update([
            'offer_id' => $offer->id,
        ]);
    }
}
