<?php

namespace App\Console\Commands;

use App\LeadDestination;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class AutomaticDestinationDeactivate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destination:deactivate-unused';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks once a day if there were assignments, e.g. last 40 days. If not, the destination becomes inactive';

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
        LeadDestination::active()
            ->whereHas('assignments')
            ->whereDoesntHave('assignments', fn (Builder $q) => $q->whereDate('created_at', '>=', now()->subDays(40)))
            ->get()
            ->each(fn ($destination) => $destination->update(['is_active' => false]));

        return 0;
    }
}
