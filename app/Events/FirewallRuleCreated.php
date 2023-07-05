<?php

namespace App\Events;

use App\Firewall;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FirewallRuleCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Firewall
     */
    public Firewall $rule;

    /**
     * Create a new event instance.
     *
     * @param \App\Firewall $rule
     */
    public function __construct(Firewall $rule)
    {
        $this->rule = $rule;
    }
}
