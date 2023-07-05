<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use Illuminate\Console\Command;

class InvalidateOldLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:invalidate-old-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set old leads as invalid';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $leads = Lead::leftovers()->whereDate('created_at', '<', now()->subDay());

        $this->info('Invalidating '.$leads->count().' leads');

        $leads->update(['valid' => false]);

        $this->info('Done');
    }
}
