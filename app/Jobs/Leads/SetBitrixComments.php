<?php

namespace App\Jobs\Leads;

use App\DestinationDrivers\Contracts\GetsInfoFromDestination;
use App\LeadOrderAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetBitrixComments implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected LeadOrderAssignment $assignment;
    protected GetsInfoFromDestination $destination;

    /**
     * SetBitrixComments constructor.
     *
     * @param LeadOrderAssignment     $assignment
     * @param GetsInfoFromDestination $destination
     */
    public function __construct(LeadOrderAssignment $assignment, GetsInfoFromDestination $destination)
    {
        $this->assignment  = $assignment;
        $this->destination = $destination;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $comments = $this->getComments();

        $comments->each(fn ($comment) => $this->process($comment));
    }

    /**
     * Resolve comment
     *
     * @param $comment
     */
    protected function process($comment)
    {
        $this->assignment->lead->comments()->updateOrCreate([
            'created_at' => $comment['created_at'],
            'user_id'    => null,
        ], [
            'text' => $comment['text'],
        ]);
    }

    /**
     * Get all comments from bitrix24
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    protected function getComments()
    {
        try {
            return collect($this->destination
                ->getLeadComments($this->assignment))
                ->map(fn ($comment) => [
                    'created_at' => $comment['CREATED'],
                    'text'       => $comment['COMMENT'],
                ]);
        } catch (\Throwable $exception) {
            return collect();
        }
    }
}
