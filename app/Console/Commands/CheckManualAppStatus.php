<?php

namespace App\Console\Commands;

use App\ManualApp;
use Illuminate\Console\Command;

class CheckManualAppStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apps:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks google apps statuses.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ManualApp::query()
            ->whereIn('status', [ManualApp::NEW, ManualApp::PUBLISHED])
            ->each(fn (ManualApp $app) => \App\Jobs\CheckManualAppStatus::dispatch($app));

        return 0;
    }
}
