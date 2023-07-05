<?php

namespace App\Console\Commands;

use App\AdsBoard;
use App\Jobs\DeliverAssignment;
use App\Jobs\SimulateAutologin;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DeliverDelayedAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:deliver-delayed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delivers delayed assignments.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date  = now()->toImmutable();
        $query = LeadOrderAssignment::query()
            ->withoutDeliveryTry()
            ->where('deliver_at', '=', $date->seconds(0)->toDateTimeString())
            ->where(function (Builder $builder) {
                return $builder->whereDoesntHave('batch')
                    ->orWhereHas('batch', fn ($query) => $query->inProcess());
            });

        \Log::channel('smooth-lo')->debug(sprintf(
            'Cron started: %s, assignments to deliver:%s',
            $date->toDateTimeString(),
            $query->count()
        ));

        $query->each(function (LeadOrderAssignment $assignment) {
            if (cache()->lock('delayed-' . $assignment->id, 45 * Carbon::SECONDS_PER_MINUTE)->get()) {
                if ($assignment->simulate_autologin && $assignment->deliver_at !== null) {
                    DeliverAssignment::withChain([
                        (new SimulateAutologin($assignment))->onQueue('autologin'),
                    ])->dispatch($assignment)->onQueue(AdsBoard::QUEUE_DEFAULT);
                } else {
                    DeliverAssignment::dispatch($assignment)->allOnQueue(AdsBoard::QUEUE_DEFAULT);
                }
                \Log::channel('smooth-lo')->debug(sprintf(
                    'Deliver dispatched:%s, assignment: %s',
                    now()->toDateTimeString(),
                    $assignment->id
                ));
            }
        });

        \Log::channel('smooth-lo')->debug(sprintf(
            'Cron finished: %s',
            $date->toDateTimeString()
        ));

        return 0;
    }
}
