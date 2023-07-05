<?php

namespace App\Integrations\Commands;

use App\Integrations\Form;
use App\Lead;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SenToForms extends \Illuminate\Console\Command
{

    /**
     * The name of the console command
     *
     * @var string
     */
    protected $signature = 'integrations:leads:send
                            {--range : Send valid leads to from for a period of time}
                            {date? : Send valid leads to from for a specific date}';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Send valid leads to from';


    /**
     * Execute command
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->hasFilters()) {
            $this->sendForDate(now());
        }

        if ($this->argument('date')) {
            $this->sendForDate(Carbon::parse($this->argument('date')));
        }

        if ($this->option('range')) {
            $this->runForPeriod();
        }
    }

    /**
     * Send from range
     *
     * @param \Carbon\Carbon $date
     *
     * @return void
     */
    public function sendForDate($date)
    {
        $leads = Lead::query()
            ->valid()
            ->whereDate('created_at', $date)
            ->whereNotNull('landing_id')
            ->whereNull('external_id')
            ->cursor();

        foreach ($leads as $lead) {
            $lead->landing->forms()->active()
                ->each(function (Form $form) use ($lead) {
                    $form->dispatch($lead);
                });
        }
    }

    /**
     * Run
     *
     * @return void
     */
    protected function runForPeriod()
    {
        $period = (new CarbonPeriod())
            ->days(1)
            ->since($this->ask('Since date'))
            ->until($this->ask('Until date') ?? now());

        foreach ($period as $date) {
            $this->sendForDate($date);
        }
    }

    /**
     * Determine when user provides additional filters
     *
     * @return bool
     */
    protected function hasFilters()
    {
        return $this->option('range') === true || $this->argument('date') !== null;
    }
}
