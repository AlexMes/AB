<?php

use Illuminate\Database\Seeder;

class LeadDestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect((new \App\LeadDestinationDriver())->getRows())
            ->whereNotIn('id', \App\LeadDestination::pluck('driver'))
            ->each(function ($destination) {
                \App\LeadDestination::create([
                    'name'   => $destination['name'],
                    'driver' => $destination['id'],
                ]);
            });
    }
}
