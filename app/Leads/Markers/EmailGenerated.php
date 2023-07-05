<?php

namespace App\Leads\Markers;

use App\Lead;
use Illuminate\Support\Facades\Cache;

class EmailGenerated implements MarksLead
{
    /**
     * Determine when lead's email was generated
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    public function applicableTo(Lead $lead): bool
    {
        return Cache::pull(sprintf('ge-%s', $lead->email)) !== null;
    }

    /**
     * Get marker definition
     *
     * @return string
     */
    public function getName(): string
    {
        return 'email-generated';
    }
}
