<?php

namespace App\Jobs\Imports;

use App\Jobs\AssignMarkersToLead;
use App\Lead;
use App\Leads\Pipes\CheckForDuplicates;
use App\Leads\Pipes\DetermineAffiliate;
use App\Leads\Pipes\DetermineLanding;
use App\Leads\Pipes\DetermineOffer;
use App\Leads\Pipes\FormatEmailAddress;
use App\Leads\Pipes\FormatRussianNumbers;
use App\Leads\Pipes\GenerateEmail;
use App\Leads\Pipes\GenerateUuid;
use App\Leads\Pipes\SaveIntoDatabase;
use App\Leads\Pipes\ValidateName;
use App\Leads\SendLeadToCustomer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLead implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $attributes;

    /**
     * @var bool|null
     */
    protected ?bool $ignoreDoubles;

    /**
     * Array of processing classes
     *
     * @var array
     */
    protected $pipes = [
        GenerateUuid::class,
        DetermineOffer::class,
        DetermineAffiliate::class,
        DetermineLanding::class,
        ValidateName::class,
        FormatRussianNumbers::class,
        GenerateEmail::class,
        FormatEmailAddress::class,
        SaveIntoDatabase::class,
    ];

    /**
     * @param array $attributes
     * @param bool  $ignoreDoubles
     */
    public function __construct(array $attributes, bool $ignoreDoubles)
    {
        $this->attributes    = $attributes;
        $this->ignoreDoubles = $ignoreDoubles;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        if (!$this->ignoreDoubles) {
            array_splice($this->pipes, count($this->pipes) - 1, 0, [CheckForDuplicates::class]);
        }

        $lead = app(Pipeline::class)
            ->send(new Lead($this->attributes))
            ->through($this->pipes)
            ->thenReturn();

        AssignMarkersToLead::dispatchNow($lead);

        $start = microtime(true);
        /** @var \App\LeadOrderAssignment $assignment */
        $assignment = SendLeadToCustomer::dispatchNow($lead);
        $end        = microtime(true);

        $response = [
            'message'  => 'Stored',
            'redirect' => optional($assignment)->fresh()->redirect_url ?? null,
            'lead'     => [
                'id'    => $lead->uuid,
                'name'  => $lead->fullname,
                'phone' => $lead->formatted_phone,
                'valid' => $lead->valid,
            ],
        ];

        $lead->addEvent('upload', array_merge($response, [
            'start' => $start,
            'end'   => $end,
            'diff'  => $end - $start,
        ]));
    }
}
