<?php

namespace App\Console\Commands;

use App\Jobs\SetAssignmentsStatuses;
use App\StatusConfig;
use Illuminate\Console\Command;

class SetAssignmentsStatusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:set-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (
            StatusConfig::whereEnabled(true)
                ->orderBy('id', 'desc')
                ->cursor() as $config
        ) {
            SetAssignmentsStatuses::dispatch($config);
        }

        return 0;
    }
}
