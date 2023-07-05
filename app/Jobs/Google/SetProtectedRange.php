<?php

namespace App\Jobs\Google;

use App\Google\Sheet;
use App\GoogleSheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetProtectedRange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\GoogleSheet
     */
    protected GoogleSheet $googleSheet;

    /**
     * Create a new job instance.
     *
     * @param \App\GoogleSheet $googleSheet
     */
    public function __construct(GoogleSheet $googleSheet)
    {
        $this->googleSheet = $googleSheet;
    }

    /**
     * Execute the job.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return void
     *
     */
    public function handle()
    {
        Sheet::setProtectedRange($this->googleSheet->spreadsheet_id, $this->googleSheet->sheet_id);
    }
}
