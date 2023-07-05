<?php

namespace App\Console\Commands\Fixtures;

use App\Facebook\FacebookApp;
use App\Facebook\Profile;
use Illuminate\Console\Command;

class SetAppId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:set-app-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create app and set up app_id to all profiles in system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            FacebookApp::create([
                'id'            => config('facebook.app_id'),
                'secret'        => config('facebook.app_secret'),
                'default_token' => config('facebook.default_access_token'),
                'domain'        => 'ads-board.app',
                'name'          => 'AdsBoard',
            ]);
            $this->info('first app created');
        } catch (\Throwable $exception) {
            $this->info(sprintf('already has %s app', config('facebook.app_id')));
        }

        Profile::whereNull('app_id')->update(['app_id' => config('facebook.app_id')]);

        $this->info(sprintf('profiles with nullable app have been updated to use %s AdsBoard', config('facebook.app_id')));
    }
}
