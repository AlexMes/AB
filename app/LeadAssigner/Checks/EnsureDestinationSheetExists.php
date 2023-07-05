<?php

namespace App\LeadAssigner\Checks;

use App\Google\Sheet;
use App\LeadOrderAssignment;
use Closure;

class EnsureDestinationSheetExists
{
    /**
     * Check that destination sheet exists on spreadsheet
     *
     * @param \App\LeadOrderAssignment $assignment
     * @param \Closure                 $closure
     *
     * @return void
     */
    public function handle(LeadOrderAssignment $assignment, Closure $closure)
    {
        logger(sprintf('Ensuring destination sheet exists: %s', $assignment->id), ['gsheets']);
        try {
            if (!$assignment->getManager()->spreadsheet()->hasSheet($assignment->getDestinationSheetName())) {
                $assignment->getManager()->spreadsheet()->createSheet($assignment->getDestinationSheetName());
            }
        } catch (\Throwable $e) {
            logger(sprintf('Error ensuring destination sheet exists: %s. %s', $assignment->id, $e->getMessage()), ['gsheets']);

            throw $e;
        }

        $closure($assignment);
    }
}
