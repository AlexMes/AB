<?php

namespace App\Console\Commands\Fixtures;

use App\GoogleSheet;
use App\Manager;
use App\Office;
use Illuminate\Console\Command;

class RemoveOldSpreadSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:remove-old-spreadsheets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove spreadsheets for customers, who receive leads in other ways';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $offices = Office::where('destination_id', 3)->pluck('id');

        $managers = Manager::withTrashed()->whereNotIn('office_id', $offices)->whereNotNull('spreadsheet_id')->get();

        foreach ($managers as $manager) {
            $this->cleanUpManager($manager);
        }

        $this->info('Managers cleaned up ');

        $workingSheets = Manager::whereIn('office_id', $offices)->pluck('spreadsheet_id');

        GoogleSheet::whereNotIn('spreadsheet_id', $workingSheets)->forceDelete();

        return 0;
    }

    /**
     * Cleanup shit
     *
     * @param $manager
     */
    protected function cleanUpManager(Manager $manager)
    {
        if (! $manager->hasSpreadSheet()) {
            return;
        }

        GoogleSheet::whereSpreadsheetId($manager->spreadsheet_id)->forceDelete();

        $manager->update(['spreadsheet_id' => null]);
    }
}
