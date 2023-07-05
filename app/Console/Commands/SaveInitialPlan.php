<?php

namespace App\Console\Commands;

use App\InitialPlan;
use App\Offer;
use Illuminate\Console\Command;

class SaveInitialPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:save-initial-plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save initial plan for a date';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        InitialPlan::create([
            'date'         => now()->toDateString(),
            'leads_amount' => Offer::withOrderedCount(now())->get()->sum('ordered')
        ]);
    }
}
