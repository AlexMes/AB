<?php

namespace App\Console\Commands;

use App\Manager;
use Illuminate\Console\Command;
use Str;

class UpdateManagaNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:mannames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Manager::whereDoesntHave('assignments', function ($query) {
            return $query->where('lead_order_assignments.created_at', '>', '2022-04-01 00:00:00');
        })->each(function (Manager $manager) {
            $nn = Str::random(15);

            $manager->update([
                'name'  => $nn,
                'email' => $nn.'@old.co',
            ]);
        });

        return 0;
    }
}
