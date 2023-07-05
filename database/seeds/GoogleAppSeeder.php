<?php

use Illuminate\Database\Seeder;

class GoogleAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\GoogleApp::firstOrCreate([
            'market_id' => 'com.board.application',
        ], [
            'name'      => 'Testing application',
            'enabled'   => true,
            'url'       => 'https://ads-board.app'
        ]);
    }
}
