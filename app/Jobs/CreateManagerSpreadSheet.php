<?php

namespace App\Jobs;

use App\AdsBoard;
use App\Google\SpreadSheet;
use App\Manager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateManagerSpreadSheet implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $manager;

    /**
     * CreateManagerSpreadSheet constructor.
     *
     * @param \App\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->manager->hasSpreadSheet()) {
            $spreadsheet = SpreadSheet::create($this->manager->getDefaultSpreadSheetName());

            $this->manager->update([
                'spreadsheet_id' => $spreadsheet->getSheetId(),
            ]);

            foreach ($this->grants() as $grant) {
                try {
                    $spreadsheet->grant($grant);
                } catch (\Throwable $exception) {
                    Log::error($exception->getMessage());
                }
            }
        }
    }

    /**
     * Collect grants for batch
     *
     * @return array
     */
    public function grants()
    {
        return collect([$this->manager->email])
            ->push(AdsBoard::teamEmails())
            ->push($this->manager->office->users->pluck('email')->values())
            ->flatten(1)
            ->toArray();
    }
}
