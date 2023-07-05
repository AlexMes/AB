<?php

namespace App\Unity\Commands;

use App\UnityOrganization;
use Illuminate\Console\Command;

class Refresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unity:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh unity organizations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        UnityOrganization::withoutIssues()
            ->each(function (UnityOrganization $organization) {
                $organization->refreshUnityData();
            });
    }
}
