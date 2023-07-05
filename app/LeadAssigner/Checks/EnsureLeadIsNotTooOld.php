<?php

namespace App\LeadAssigner\Checks;

use App\Lead;
use Closure;
use Illuminate\Support\Carbon;

class EnsureLeadIsNotTooOld
{
    public function handle(Lead $lead, Closure $next)
    {
        // if lead is created less than a day ago - pass it further
        if (Carbon::parse($lead->created_at)->gte(Carbon::parse('2020-11-27 00:00:00'))) {
            $next($lead);
        }
    }
}
