<?php

namespace App\Listeners;

use App\Events\Lead\Created;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNioToDocs implements ShouldQueue
{
    protected $spreadSheetId = '1Jgw4AIXHkw8KmMCasZuMGFfPrJQyB4kPn_vuONkhJAc';
    protected $sheetId       = '1644130556';
    protected $sheetName     = 'Leads';

    /**
     * Handle the event.
     *
     * @param \App\Events\Lead\Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        if ($event->lead->offer_id === 307) {
            app('sheets')->spreadsheets_values
                ->append(
                    $this->spreadSheetId,
                    sprintf('%s!A1:N1', $this->sheetName),
                    new \Google_Service_Sheets_ValueRange([
                        'values' => [
                            [
                                $event->lead->uuid,
                                $event->lead->fullname,
                                $event->lead->phone,
                                $event->lead->email,
                                $event->lead->created_at->toDateTimeString(),
                            ]
                        ],
                    ]),
                    ['valueInputOption' => 'RAW']
                );
        }
    }
}
