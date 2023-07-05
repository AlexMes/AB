<?php

namespace App\Jobs;

use App\ResellBatch;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateBatchWithLeads implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $leads;

    /**
     * @var array
     */
    protected array $offices;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $leads, array $offices)
    {
        $this->leads    = $leads;
        $this->offices  = $offices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $batch = ResellBatch::create([
            'name'                 => 'Branch19 ' . Carbon::now()->toDateString(),
            'branch_id'            => 19,
            'ignore_paused_routes' => true,
        ]);
        $batch->leads()->sync($this->leads);
        $batch->offices()->sync($this->offices);
    }
}
