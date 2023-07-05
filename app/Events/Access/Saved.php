<?php

namespace App\Events\Access;

use App\Access;

class Saved
{
    /**
     * @var \App\Access
     */
    public $access;

    /**
     * AccessUpdated constructor.
     *
     * @param \App\Access $access
     *
     * @return void
     */
    public function __construct(Access $access)
    {
        $this->access = $access;
    }
}
