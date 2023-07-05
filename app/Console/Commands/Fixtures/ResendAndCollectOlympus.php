<?php

namespace App\Console\Commands\Fixtures;

use App\Jobs\DeliverAssignment;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use Illuminate\Console\Command;
use Throwable;

class ResendAndCollectOlympus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:resend-olympus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend Olympus leads, collect errors';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assignments = LeadOrderAssignment::with('lead')->whereIn(
            'route_id',
            LeadOrderRoute::whereIn(
                'order_id',
                LeadsOrder::where('office_id', 34)->pluck('id'),
            )->pluck('id')
        )->whereNull('confirmed_at')
            ->get();

        $this->info('Collected %s assignments', $assignments->count());

        $results = collect();

        /** @var LeadOrderAssignment $assignment */
        foreach ($assignments as $assignment) {
            try {
                DeliverAssignment::dispatchNow($assignment);
                $results->add([
                    'id'          => $assignment->id,
                    'lead_id'     => $assignment->lead->uuid,
                    'phone'       => $assignment->lead->formatted_phone,
                    'name_valid'  => $assignment->lead->valid ? 'yes' : 'no',
                    'phone_valid' => $assignment->lead->phone_valid ? 'yes' : 'no',
                    'error'       => null
                ]);
            } catch (Throwable $exception) {
                $results->add([
                    'id'          => $assignment->id,
                    'lead_id'     => $assignment->lead->uuid,
                    'phone'       => $assignment->lead->formatted_phone,
                    'name_valid'  => $assignment->lead->valid ? 'yes' : 'no',
                    'phone_valid' => $assignment->lead->phone_valid ? 'yes' : 'no',
                    'error'       => $exception->getMessage()
                ]);
            }
        }

        $this->table(['Assignment','Lead','Phone','Name Valid','Phone valid','Error'], $results->toArray());

        return 0;
    }
}
