<?php

namespace App\Leads\Markers;

use App\Lead;

class Crontab implements MarksLead
{
    /**
     * Determine is lead had verified sms registration
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    public function applicableTo(Lead $lead): bool
    {
        if ($lead->requestData['crontab'] ?? false) {
            return filter_var($lead->requestData['crontab'], FILTER_VALIDATE_BOOLEAN);
        }

        return false;
    }

    /**
     * Get marker definition
     *
     * @return string
     */
    public function getName(): string
    {
        return 'crontab';
    }
}
