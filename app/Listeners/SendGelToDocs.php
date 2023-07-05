<?php

namespace App\Listeners;

use App\Events\Lead\Created;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendGelToDocs implements ShouldQueue
{
    protected $spreadSheetId = '1LLDKTVuZO9JC3T0GpGP8a40_f_1vqwRY0ikO8FQYx9w';
    protected $sheetId       = '1952912221';
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
        if ($event->lead->offer_id === 461) {
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
