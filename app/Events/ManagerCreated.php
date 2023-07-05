<?php

namespace App\Events;

use App\Manager;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ManagerCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Manager instance
     *
     * @var [type]
     */
    public $manager;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }
}
