<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;

interface LeadProcessingPipe
{
    /**
     * Do something with lead
     *
     * @param \App\Lead $lead
     * @param \Closure  $next
     *
     * @return mixed
     */
    public function handle(Lead $lead, Closure $next);
}
