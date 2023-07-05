<?php

use Illuminate\Database\Seeder;

class PlacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            'Facebook News Feed',
            'Instagram Feed',
            'Facebook Marketplace',
            'Facebook Video Feeds',
            'Facebook Right Column',
            'Instagram Explore',
            'Messenger Inbox',
            'Facebook Stories',
            'Instagram Stories',
            'Messenger Stories',
        ])
            ->diff(\App\Placement::all(['name'])->pluck('name'))
            ->each(function ($placement) {
                \App\Placement::create([
                    'name'  => $placement,
                ]);
            });
    }
}
