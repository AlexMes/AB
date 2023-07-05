<?php

namespace App\Jobs;

use App\LeadOrderAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class AffiliatePostback implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected LeadOrderAssignment $assignment;

    /**
     * @var string
     */
    protected string $status;

    /**
     * Placeholders and values
     *
     * @var array
     */
    protected array $replaceMap = [];

    /**
     * Create a new job instance.
     *
     * @param LeadOrderAssignment $assignment
     * @param string|null         $status
     */
    public function __construct(LeadOrderAssignment $assignment, string $status = null)
    {
        $this->assignment = $assignment->fresh(['lead','lead.affiliate']);
        $this->status     = $status ?? $assignment->status;
        $this->replaceMap = [
            'uuid'    => $assignment->lead->uuid,
            'status'  => $status ?? $assignment->status,
            'clickid' => $assignment->lead->clickid
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->assignment->lead->affiliate_id !== null && $this->assignment->lead->affiliate->postback !== null) {
            $url = $this->assignment->lead->affiliate->postback;

            foreach ($this->replaceMap as $placeholder => $value) {
                $url = str_replace("{{$placeholder}}", $value, $url);
            }

            Http::get($url);
        }
    }
}
