<?php

namespace App\Events;

use App\GoogleSheet;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoogleSheetCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\GoogleSheet
     */
    public GoogleSheet $sheet;

    /**
     * Create a new event instance.
     *
     * @param \App\GoogleSheet $sheet
     */
    public function __construct(GoogleSheet $sheet)
    {
        $this->sheet = $sheet;
    }
}
