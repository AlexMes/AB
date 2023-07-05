<?php

namespace App\Leads\Markers;

use App\Lead;

interface MarksLead
{
    /**
     * Determine is mark applicable to the lead
     *
     * @return void
     */
    public function applicableTo(Lead $lead):bool;

    /**
     * Get marker definition
     *
     * @return void
     */
    public function getName():string;
}
