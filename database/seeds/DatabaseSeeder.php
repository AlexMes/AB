<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment('local')) {
            $this->call(LocalUserSeeder::class);
        }

        $this->call(PlacementSeeder::class);

        $this->call(NotificationTypeSeeder::class);

        $this->call(DesignerSeeder::class);
    }
}
