<?php

namespace App\Console\Commands;

use App\Binom\Exceptions\BinomReponseException;
use App\Jobs\PullLeadAppId;
use App\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CollectLeadAppIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:collect-app-ids
                            {--since= : created since}
                            {--until= : created until}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collects app ids from binom.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::query()
            ->whereNull('app_id')
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
                PullLeadAppId::dispatchNow($lead);
            } catch (BinomReponseException $ex) {
                //
            }

            $progress->advance();
        }

        $progress->finish();

        return 0;
    }
}
