<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Offer;
use Illuminate\Console\Command;

class MarkLeadsAsLeftoversForJulia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:mark-leads-as-leftovers-for-julia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var \App\LeadsOrder|\App\LeadsOrder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    protected $order;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->order = LeadsOrder::find(2902);

        $this->bitcoin();
        $this->traderBook();
        $this->markiz2();

        return 0;
    }

    protected function bitcoin()
    {
        $offer  = Offer::firstOrCreate(['name' => 'LO_BITCOIN']);
        $routes = $this->order->routes()->where('offer_id', 168);

        $leads = Lead::whereIn('id', LeadOrderAssignment::whereIn('route_id', $routes->pluck('id'))->pluck('lead_id'));

        $leads->update([
            'offer_id' => $offer->id
        ]);

        $routes->update([
            'offer_id' => $offer->id,
        ]);
    }

    protected function traderBook()
    {
        $offer  = Offer::firstOrCreate(['name' => 'LO_TradersBook']);
        $routes = $this->order->routes()->where('offer_id', 185);

        $leads = Lead::whereIn('id', LeadOrderAssignment::whereIn('route_id', $routes->pluck('id'))->pluck('lead_id'));

        $leads->update([
            'offer_id' => $offer->id
        ]);

        $routes->update([
            'offer_id' => $offer->id,
        ]);
    }

    protected function markiz2()
    {
        $offer = Offer::firstOrCreate(['name' => 'LO_GAZRUSSI_MARKIZ2']);
        $lead  = Lead::find(296015);

        /** @var \App\LeadOrderRoute $route */
        $route = $lead->assignment->route;
        $route->update([
            'leadsReceived' => $route->leadsReceived - 1,
            'leadsOrdered'  => $route->leadsOrdered - 1,
        ]);

        $newRoute = LeadOrderRoute::firstOrCreate([
            'order_id'   => $this->order->id,
            'offer_id'   => $offer->id,
            'manager_id' => $route->manager_id
        ], ['leadsOrdered' => 1,'leadsReceived' => 1,'last_received_at' => $lead->assignment->created_at]);

        $lead->assignment->update([
            'route_id' => $newRoute->id,
        ]);
    }
}
