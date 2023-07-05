<?php

namespace App\Jobs;

use App\Deposit;
use App\LeadAssigner\LeadAssigner;
use App\ResellBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartResellBatch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var ResellBatch
     */
    public ResellBatch $batch;

    /**
     * Create a new job instance.
     *
     * @param ResellBatch $batch
     */
    public function __construct(ResellBatch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->batch->loadCount(['leads' => fn ($query) => $query->whereNull('assigned_at')]);
        $deliverAt   = now()->floorSeconds(60)->addMinute();
        $minutesLeft = $deliverAt->diffInMinutes($this->batch->assign_until);
        $step        = $this->batch->leads_count > 0 ? $minutesLeft / $this->batch->leads_count : 0;
        $addMinCnt   = 0;

        foreach ($this->batch->leads()->whereNull('assigned_at')->get() as $lead) {
            if ($this->batch->substitute_offer) {
                $lead->offer_id = $this->batch->substitute_offer;
            }
            if (Deposit::where('phone', $lead->phone)->exists()) {
                $this->batch->leads()->detach($lead->id);

                continue;
            }

            if (now()->startOfDay()->equalTo($this->batch->registered_at->startOfDay())) {
                $registeredAt = now();
            } else {
                $registeredAt = $this->batch->registered_at->copy()
                    ->setHours(optional(optional($lead->current_assignment)->registered_at)->hour)
                    ->setMinutes(optional(optional($lead->current_assignment)->registered_at)->minute)
                    ->setSeconds(optional(optional($lead->current_assignment)->registered_at)->second);
            }

            LeadAssigner::dispatchNow(
                $lead,
                null,
                $this->batch->offices->pluck('id')->toArray(),
                $registeredAt,
                $this->batch->create_offer ? LeadAssigner::RESELL_OFFER : LeadAssigner::RESELL,
                $this->batch->simulate_autologin,
                $deliverAt,
                false,
                $this->batch->ignore_paused_routes,
            );

            $addMinCnt += $step;
            if ($addMinCnt >= 1) {
                $deliverAt->addMinutes((int)$addMinCnt);
                $addMinCnt = $addMinCnt - (int)$addMinCnt;
            }
        }
    }
}
