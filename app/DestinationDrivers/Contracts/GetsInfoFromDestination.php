<?php


namespace App\DestinationDrivers\Contracts;

use App\LeadOrderAssignment;
use Illuminate\Support\Collection;

interface GetsInfoFromDestination
{
    /**
     * @return Collection
     */
    public function getLeads();

    /**
     * @param LeadOrderAssignment $assignment
     *
     * @return array|null
     */
    public function getLead(LeadOrderAssignment $assignment);

    /**
     * @param LeadOrderAssignment $assignment
     *
     * @return array|null
     */
    public function getLeadComments(LeadOrderAssignment $assignment);
}
