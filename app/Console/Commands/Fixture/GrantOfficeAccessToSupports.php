<?php

namespace App\Console\Commands\Fixture;

use App\Branch;
use App\Office;
use Illuminate\Console\Command;

class GrantOfficeAccessToSupports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:grant-office-access-to-supports';

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
        Branch::query()->each(function (Branch $branch) {
            $branch->offices()->sync(Office::all());
        });

        return 0;
    }
}
