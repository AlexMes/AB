<?php

use Illuminate\Database\Seeder;

class PresetLeadAssignments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $office  = \App\Office::create(['name' => 'Office','cpa' => 0,'cpl' => 0]);
        $offer   = \App\Offer::create(['name' => 'Offer']);
        $manager = \App\Manager::create([
            'name'      => 'Testing',
            'office_id' => $office->id,
            'email'     => 'manager@office.com'
        ]);
        \App\Domain::create([
            'linkType'        => \App\Domain::LANDING,
            'offer_id'        => $offer->id,
            'status'          => \App\Domain::READY,
            'url'             => 'https://rolla.com',
            'splitterEnabled' => true,
        ]);
        $order = \App\LeadsOrder::create([
            'date'      => now(),
            'office_id' => $office->id,
        ]);
        $route = $order->routes()->create([
            'manager_id'    => $manager->id,
            'offer_id'      => $offer->id,
            'leadsOrdered'  => 30,
            'leadsReceived' => 0
        ]);
    }
}
