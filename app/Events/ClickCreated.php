<?php

namespace App\Events;

use App\Binom\Click;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClickCreated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Click
     */
    public Click $click;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Click $click)
    {
        $this->click = $click;
    }
}
