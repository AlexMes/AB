<?php

namespace App\Console\Commands;

use App\Jobs\PullLeadTrafficSource;
use App\Lead;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CollectLeadTrafficSources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:collect-traffic-sources
                            {--since= : created since}
                            {--until= : created until}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collects traffic sources from binom.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::query()
            ->whereNull('traffic_source')
            ->whereHas('click')
            ->when(
                $this->option('since'),
                fn ($q) => $q->where('created_at', '>=', Carbon::parse($this->option('since'))->toDateTimeString())
            )
            ->when(
                $this->option('until'),
                fn ($q) => $q->where('created_at', '<=', Carbon::parse($this->option('until'))->toDateTimeString())
            );

        $progress = $this->output->createProgressBar($leads->count());

        foreach ($leads->cursor() as $lead) {
            try {
                PullLeadTrafficSource::dispatchNow($lead);
            } catch (Exception $ex) {
                //
            }

            $progress->advance();
        }

        $progress->finish();

        return 0;
    }
}
