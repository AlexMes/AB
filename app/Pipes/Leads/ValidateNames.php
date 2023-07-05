<?php

namespace App\Pipes\Leads;

use App\Lead;
use App\Rules\ObsceneCensorRus;
use Closure;
use Illuminate\Support\Str;

class ValidateNames
{
    /**
     * Determines is lead duplicate
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return void
     */
    public function handle(Lead $lead, Closure $next)
    {
        $lead->valid = $this->withoutAbuse($lead) && $this->withoutTestInName($lead);

        return $next($lead);
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
