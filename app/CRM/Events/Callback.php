<?php

namespace App\CRM\Events;

use App\AdsBoard;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Callback implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Fresh callback model
     *
     * @var \App\CRM\Callback
     */
    public \App\CRM\Callback $callback;

    /**
     * @var string
     */
    public $broadcastQueue = AdsBoard::QUEUE_NOTIFICATIONS;

    /**
     * Create a new event instance.
     *
     * @param \App\CRM\Callback $callback
     */
    public function __construct(\App\CRM\Callback $callback)
    {
        $this->callback = $callback->load('assignment.lead', 'assignment.route');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(sprintf(
            'CRM.Manager.%d',
            $this->callback->assignment->route->manager_id
        ));
    }

    /**
     * Define data that will be broadcasted
     *
     * @return array[]
     */
    public function broadcastWith()
    {
        return [
            'fullname'         => $this->callback->assignment->lead->fullname,
            'url'              => route('crm.assignments.show', $this->callback->assignment),
            'callback_at_date' => $this->callback->call_at->format('d.m.Y'),
            'callback_at_time' => $this->callback->call_at->format('H:i'),
        ];
    }
}
