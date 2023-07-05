<?php

namespace App\Events\Offices;

use App\Office;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Created
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Office
     */
    public Office $office;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Office $office)
    {
        $this->office = $office;
    }
}
