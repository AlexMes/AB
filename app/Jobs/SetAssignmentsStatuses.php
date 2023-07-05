<?php

namespace App\Jobs;

use App\LeadOrderAssignment;
use App\StatusConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetAssignmentsStatuses implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var StatusConfig
     */
    protected StatusConfig $statusConfig;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(StatusConfig $statusConfig)
    {
        $this->statusConfig = $statusConfig;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $assigned = now()->subDays($this->statusConfig->assigned_days_ago);

        foreach (
            LeadOrderAssignment::query()
                ->whereHas('route.order', fn ($query) => $query->where('office_id', $this->statusConfig->office_id))
                ->whereBetween(
                    'created_at',
                    [
                        $assigned->startOfDay()->toDateTimeString(),
                        $assigned->endOfDay()->toDateTimeString(),
                    ]
                )
                ->when(
                    $this->statusConfig->statuses_type === StatusConfig::IN,
                    fn ($q) => $q->whereIn('status', $this->statusConfig->statuses)
                )
                ->when(
                    $this->statusConfig->statuses_type === StatusConfig::OUT,
                    fn ($q) => $q->whereNotIn('status', $this->statusConfig->statuses)
                )->cursor() as $assignment
        ) {
            $assignment->update(['status' => $this->statusConfig->new_status]);
        }
    }
}
