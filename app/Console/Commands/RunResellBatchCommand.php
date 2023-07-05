<?php

namespace App\Console\Commands;

use App\Deposit;
use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\ResellBatch;
use Illuminate\Console\Command;

class RunResellBatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resell-batch:run';

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
        \Log::debug('Resell command started.');
        $batches = ResellBatch::query()
            ->withCount(['leads' => fn ($query) => $query->whereNull('assigned_at')])
            ->inProcess()
            ->incomplete()
            ->get();

        /** @var ResellBatch $batch */
        foreach ($batches as $batch) {
            $hoursLeft = optional($batch->assign_until)->isAfter(now())
                ? max(1, optional($batch->assign_until)->diffInHours(now()))
                : 1;
            $currentCount = (int) round($batch->leads_count / $hoursLeft);
            \Log::debug(sprintf(
                'Batch %s (#%s) iteration started, assign until: %s, hrs left: %s, leads: %s, leads for current run: %s',
                $batch->name,
                $batch->id,
                $batch->assign_until->toDateTimeString(),
                $hoursLeft,
                $batch->leads_count,
                $currentCount
            ));

            /** @var Lead $lead */
            foreach (
                $batch->leads()
                    ->wherePivotNull('assigned_at')
                    ->take($currentCount)->get() as $lead
            ) {
                if ($batch->substitute_offer) {
                    $lead->offer_id = $batch->substitute_offer;
                }

                if (Deposit::where('phone', $lead->phone)->exists()) {
                    \Log::debug(sprintf('Lead with dep, %s (#%s). Detaching', $lead->phone, $lead->id));
                    $batch->leads()->detach($lead->id);

                    continue;
                }

                if (now()->startOfDay()->equalTo($batch->registered_at->startOfDay())) {
                    $registeredAt = now();
                } else {
                    $registeredAt = $batch->registered_at->copy()
                        ->setHours(optional(optional($lead->current_assignment)->registered_at)->hour)
                        ->setMinutes(optional(optional($lead->current_assignment)->registered_at)->minute)
                        ->setSeconds(optional(optional($lead->current_assignment)->registered_at)->second);
                }

                LeadAssigner::dispatch(
                    $lead,
                    null,
                    $batch->offices()->pluck('offices.id')->toArray(),
                    $registeredAt,
                    $batch->create_offer ? LeadAssigner::RESELL_OFFER : LeadAssigner::RESELL,
                    $batch->simulate_autologin,
                    null,
                    false,
                    $batch->ignore_paused_routes,
                )->delay(now()->addMinutes(rand(1, 50)))->onQueue('default');

                // todo: refactor
                $batch->leads()->updateExistingPivot($lead->id, ['assigned_at' => now()]);
            }

            \Log::debug(sprintf('Batch %s (#%s) iteration finished.', $batch->name, $batch->id));
            if ($batch->leads_count <= $currentCount) {
                \Log::debug(sprintf('Batch %s (#%s) is done.', $batch->name, $batch->id));
                $batch->update(['status' => ResellBatch::FINISHED]);
            }
        }

        \Log::debug('Resell command finished.');

        return 0;
    }
}
