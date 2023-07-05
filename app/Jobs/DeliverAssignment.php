<?php

namespace App\Jobs;

use App\CRM\Events\NewLeadAssigned;
use App\Lead;
use App\LeadOrderAssignment;
use App\Trail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class DeliverAssignment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Number of attempts before failure
     *
     * @var int
     */
    public $tries = 1;

    /**
     * @var \App\LeadOrderAssignment
     */
    public LeadOrderAssignment $assignment;

    /**
     * Create a new job instance.
     *
     * @param \App\LeadOrderAssignment $assignment
     */
    public function __construct(LeadOrderAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Execute the job.
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return \App\LeadOrderAssignment
     */
    public function handle()
    {
        if ($this->assignment->deliver_at !== null) {
            \Log::channel('smooth-lo')->debug(sprintf(
                'Delivery ran at: %s, assignment:%s',
                now()->toDateTimeString(),
                $this->assignment->id
            ));
        }
        $driver = $this->assignment->getTargetDestination();

        $this->assignment->lead->addEvent(
            Lead::DELIVERY_TRY,
            [
                'assignment_id'  => $this->assignment->id,
                'destination'    => $driver->name,
                'destination_id' => $driver->id,
            ]
        );

        app(Trail::class)->add(sprintf("Init %s for *%s*", $driver->name, $this->assignment->route->order->office->name));

        $destination = $driver->initialize();

        $destination->send($this->assignment->lead);

        app(Trail::class)->add(sprintf(
            "Sent lead %s to %s",
            $this->assignment->lead_id,
            $driver->name
        ));

        $this->assignment->update([
            'destination_id'  => optional($driver)->id,
            'redirect_url'    => $destination->getRedirectUrl(),
            'confirmed_at'    => $destination->isDelivered() ? now() : null,
            'delivery_failed' => $destination->isDelivered() ? null : $destination->getError(),
            'external_id'     => $destination->getExternalId(),
        ]);

        app(Trail::class)->add(
            $destination->isDelivered()
                ? 'Assignment delivered ✅'
                : 'Assignment is not delivered 🚫'
        );

        if ($this->assignment->isConfirmed()) {
            NewLeadAssigned::dispatch($this->assignment);

            if ($destination->getRedirectUrl()) {
                app(Trail::class)->add(sprintf("Redirect url %s", $destination->getRedirectUrl()));
            }
            if ($destination->getExternalId()) {
                app(Trail::class)->add(sprintf("External ID %s", $destination->getExternalId()));
            }

            $this->assignment->lead->addEvent(
                Lead::DELIVERED,
                [
                    'assignment_id'  => $this->assignment->id,
                    'destination'    => $driver->name,
                    'destination_id' => $driver->id,
                ]
            );
        }

        if (! $this->assignment->isDelivered() && $destination->getError() !== null) {
            app(Trail::class)->add(sprintf("Error: %s", Str::limit($destination->getError(), 250)));

            $this->assignment->lead->addEvent(
                Lead::DELIVERY_FAILED,
                [
                    'assignment_id'  => $this->assignment->id,
                    'destination'    => $driver->name,
                    'destination_id' => $driver->id,
                    'error'          => $destination->getError(),
                ]
            );
        }


        return $this->assignment;
    }
}
