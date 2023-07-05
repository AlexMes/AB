<?php

namespace App\Console\Commands;

use App\AdsBoard;
use App\GoogleSheet;
use App\Jobs\PullLeadsFromGoogleDocs;
use App\Manager;
use Illuminate\Console\Command;

class PullLeadsInfoFromSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull leads from the Google Sheets';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $sheets = GoogleSheet::latest()->cursor();

        foreach ($sheets as $sheet) {
            if (Manager::where('spreadsheet_id', $sheet->spreadsheet_id)->exists()) {
                PullLeadsFromGoogleDocs::dispatch($sheet)->onQueue(AdsBoard::QUEUE_GOOGLE);
            }
        }
    }
}
