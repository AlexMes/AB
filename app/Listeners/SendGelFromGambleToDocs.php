<?php

namespace App\Listeners;

use App\Events\Lead\Created;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendGelFromGambleToDocs implements ShouldQueue
{
    protected $spreadSheetId = '13pcS8EzJXAapyQTUpzDdqyTtO41zY5-QJkJFza5ELWw';
    protected $sheetId       = '1474254590';
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
        if ($event->lead->offer_id === 472) {
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
                                $event->lead->email ?? 'email@domain.com',
                                $event->lead->created_at->toDateTimeString(),
                                $event->lead->domain,
                            ]
                        ],
                    ]),
                    ['valueInputOption' => 'RAW']
                );
        }
    }
}
