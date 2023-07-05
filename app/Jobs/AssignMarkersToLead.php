<?php

namespace App\Jobs;

use App\Lead;
use App\Leads\Markers\CaucasianName;
use App\Leads\Markers\Crontab;
use App\Leads\Markers\EmailGenerated;
use App\Leads\Markers\MarksLead;
use App\Leads\Markers\SmsVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignMarkersToLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Lead in work
     *
     * @var App\Lead
     */
    protected $lead;

    /**
     * All available markers
     *
     * @var array|MarksLead[]
     */
    protected $markers = [
        CaucasianName::class,
        SmsVerification::class,
        Crontab::class,
        EmailGenerated::class,
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        collect($this->markers)
            ->filter(fn ($marker) => app($marker)->applicableTo($this->lead))
            ->each(fn ($marker)   => $this->lead->markers()->updateOrCreate(['name' => app($marker)->getName()]));
    }
}
