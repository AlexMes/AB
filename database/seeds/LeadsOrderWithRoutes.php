<?php

use Illuminate\Database\Seeder;

class LeadsOrderWithRoutes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $office = factory(\App\Office::class)->create();

        $offers = factory(\App\Offer::class, 2)->create();

        $order = \App\LeadsOrder::create([
            'office_id' => $office->id,
            'date'      => now(),
        ]);

        $managers = factory(\App\Manager::class, 2)->create([
            'office_id' => $office->id,
        ]);

        $managers->each(function ($manager) use ($order, $offers) {
            $order->routes()->createMany(
                $offers
                    ->map(fn ($offer) => [
                        'offer_id'     => $offer->id,
                        'manager_id'   => $manager->id,
                        'leadsOrdered' => rand(1, 5)
                    ])->toArray()
            );
        });

        \App\Domain::create([
            'url'             => 'https://rolla.com',
            'status'          => \App\Domain::READY,
            'linkType'        => \App\Domain::LANDING,
            'offer_id'        => $offers->first()->id,
            'splitterEnabled' => true,
        ]);
    }
}
