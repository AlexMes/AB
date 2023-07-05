<?php

namespace App\Jobs\Leads;

use App\LeadOrderAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateLeadData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var LeadOrderAssignment
     */
    protected $assignment;

    /**
     * Create a new job instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
        $this->assignment = LeadOrderAssignment::whereExternalId($this->attributes['external_id'])->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_null($this->assignment)) {
            $this->assignment->update([
                'status'          => $this->attributes['status'] ?? $this->assignment->status,
                'responsible'     => $this->attributes['responsible'] ?? $this->assignment->responsible,
                /*'office_id'       => $this->attributes['office_id'] ?? $this->assignment->office_id,*/
                'department_id'   => $this->attributes['department_id'] ?? $this->assignment->department_id,
                'called_at'       => $this->attributes['called_at'] ?? $this->assignment->called_at,
            ]);
            $this->assignment->lead->comments()->updateOrCreate([
                'text' => $this->attributes['comment'],
            ], [
                'text' => $this->attributes['comment'],
            ]);
        }
    }

    /**
     * @return array
     */
    public function tags()
    {
        return [
            "external_id: {$this->attributes['external_id']}",
            'lead_id: ' . optional($this->assignment)->lead_id,
            'assignment_id: ' . optional($this->assignment)->id,
        ];
    }
}
