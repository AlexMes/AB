<?php

namespace App\Console\Commands;

use App\GoogleSheet;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Illuminate\Console\Command;

class RemoveProtectedRanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sheets:remove-protected-ranges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sheets = GoogleSheet::cursor();

        /** @var GoogleSheet $sheet */
        foreach ($sheets as $sheet) {
            $ranges = $sheet->getProtectedRanges();

            foreach ($ranges as $range) {
                app('sheets')->spreadsheets->batchUpdate(
                    $sheet->spreadsheet_id,
                    new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                        'requests' => [
                            'deleteProtectedRange' => ['protectedRangeId' => $range->protectedRangeId],
                        ]
                    ])
                );
            }
            sleep(10);
        }
    }
}
