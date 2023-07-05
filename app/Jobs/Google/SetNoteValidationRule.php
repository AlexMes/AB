<?php

namespace App\Jobs\Google;

use App\Google\ValidationRules\Note;
use App\GoogleSheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetNoteValidationRule implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\GoogleSheet
     */
    protected GoogleSheet $sheet;

    /**
     * Create a new job instance.
     *
     * @param \App\GoogleSheet $sheet
     */
    public function __construct(GoogleSheet $sheet)
    {
        $this->sheet = $sheet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rule = new Note($this->sheet->sheet_id);

        $rule->set($this->sheet->spreadsheet_id);
    }
}
