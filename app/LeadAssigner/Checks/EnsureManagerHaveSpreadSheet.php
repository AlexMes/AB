<?php

namespace App\LeadAssigner\Checks;

use App\Jobs\CreateManagerSpreadSheet;
use App\LeadOrderAssignment;
use Closure;

class EnsureManagerHaveSpreadSheet
{
    /**
     * Perform check and rescue action
     *
     * @param \App\LeadOrderAssignment $assignment
     * @param \Closure                 $next
     *
     * @return void
     */
    public function handle(LeadOrderAssignment $assignment, Closure $next)
    {
        logger(sprintf('Ensuring manager has spreadsheet: %s', $assignment->id), ['gsheets']);
        try {
            if (!$assignment->route->manager->hasSpreadSheet()) {
                CreateManagerSpreadSheet::dispatchNow($assignment->route->manager);
            }
        } catch (\Throwable $e) {
            logger(sprintf('Error ensuring manager has spreadsheet: %s. %s', $assignment->id, $e->getMessage()), ['gsheets']);

            throw $e;
        }

        $next($assignment);
    }
}
