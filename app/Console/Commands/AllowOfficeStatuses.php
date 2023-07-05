<?php

namespace App\Console\Commands;

use App\CRM\Status;
use App\Office;
use App\OfficeStatus;
use Illuminate\Console\Command;

class AllowOfficeStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'office:allow-statuses
                            {--offices=* : office IDs}
                            {--statuses=* : status names}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $statuses  = Status::select('name')
            ->when($this->option('statuses'), fn ($query, $input) => $query->whereIn('name', $input))
            ->get();

        /** @var Office $office */
        foreach (
            Office::select('id')
                ->when($this->option('offices'), fn ($query, $input) => $query->whereIn('id', $input))
                ->get() as $office
        ) {
            /** @var Status $status */
            foreach ($statuses as $status) {
                OfficeStatus::updateOrCreate(
                    ['office_id' => $office->id, 'status' => $status->name],
                    ['selectable' => true],
                );
            }
        }

        return 0;
    }
}
