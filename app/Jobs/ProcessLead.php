<?php

namespace App\Jobs;

use App\Lead;
use App\Leads\Pipes\FormatEmailAddress;
use App\Leads\Pipes\FormatRussianNumbers;
use App\Leads\Pipes\ValidateName;
use App\Pipes\Leads\DetectAffiliate;
use App\Pipes\Leads\DetectDuplicate;
use App\Pipes\Leads\DetectLanding;
use App\Pipes\Leads\GenerateUuid;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;

class ProcessLead
{
    use Dispatchable;

    /**
     * Attributes from request
     *
     * @var array
     */
    protected $attributes;

    /**
     * Array of processing classes
     *
     * @var array
     */
    protected $pipes = [
        GenerateUuid::class,
        DetectLanding::class,
        DetectAffiliate::class,
        ValidateName::class,
        FormatRussianNumbers::class,
        FormatEmailAddress::class,
        DetectDuplicate::class,
    ];

    /**
     * Create a new job instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return \App\Lead
     */
    public function handle()
    {
        $formatted = app(Pipeline::class)
            ->send(new Lead(
                array_merge(Arr::except($this->attributes, ['geetest_challenge','geetest_validate','geetest_seccode','password']), ['requestData' => $this->attributes])
            ))
            ->through($this->pipes)
            ->thenReturn();

        return $this->storeLead($formatted);
    }

    /**
     * Store lead in database
     *
     * @param \App\Lead|null $lead
     *
     * @return \App\Lead
     */
    protected function storeLead(?Lead $lead)
    {
        if ($lead === null) {
            return null;
        }

        $lead->save();

        return $lead->refresh();
    }
}
