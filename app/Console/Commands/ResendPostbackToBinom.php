<?php

namespace App\Console\Commands;

use App\Binom;
use App\Lead;
use Illuminate\Console\Command;

class ResendPostbackToBinom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'binom:postback:resend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend binom postback';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::query()
            ->whereHas('deposits', fn ($q) => $q->where('created_at', '>', now()->subDays(90)->startOfDay()->toDateTimeString()))
            ->get(['id', 'clickid']);

        $binoms = Binom::get();

        $progress = $this->output->createProgressBar($leads->count());
        /** @var Lead $lead */
        foreach ($leads as $lead) {
            if (!empty($lead->clickid)) {
                $binoms->each(function ($binom) use ($lead) {
                    try {
                        $binom->sendPayout($lead->clickid, 400);
                    } catch (\Throwable $th) {
                        // $this->info('Not able to push postback for click'.$lead->clickid);
                    }
                });
            }

            $progress->advance();
        }

        $progress->finish();

        return 0;
    }
}
