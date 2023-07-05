<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\Rules\ObsceneCensorRus;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RevalidateLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:revalidate-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Go through invalid leads, and recheck their validation status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::where('valid', false);

        $progress = $this->output->createProgressBar($leads->count());

        foreach ($leads->cursor() as $lead) {
            $lead->valid = $this->isValid($lead);

            $lead->save(['timestamps' => false]);

            $progress->advance();
        }

        $progress->finish();

        return 0;
    }

    /**
     * Determines is lead has valid name
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    protected function isValid(Lead $lead): bool
    {
        return $this->withoutAbuse($lead) && $this->withoutTestInName($lead);
    }

    /**
     * Determine is lead name contains abusive words
     *
     * @param \App\Lead $lead
     *
     * @return mixed
     */
    protected function withoutAbuse(Lead $lead)
    {
        return ObsceneCensorRus::isAllowed($lead->fullname);
    }

    /**
     * Determine is lead name contains `testing` markers
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    protected function withoutTestInName(Lead $lead)
    {
        return ! Str::contains(Str::lower($lead->fullname), ['test','ttes','demo','check','тест','проверка','демо']);
    }
}
