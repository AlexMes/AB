<?php

namespace App\CRM\View\Components;

use App\CRM\LeadOrderAssignment;
use Illuminate\View\Component;

class LeadComponent extends Component
{
    /**
     * @var LeadOrderAssignment
     */
    public LeadOrderAssignment $assignment;
    /**
     * @var bool
     */
    public bool $edit;

    /**
     * LeadShowComponent constructor.
     *
     * @param string $assignment
     * @param bool   $edit
     */
    public function __construct(string $assignment, bool $edit = false)
    {
        $this->assignment = LeadOrderAssignment::find($assignment);
        $this->edit       = $edit;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        if ($this->edit) {
            return view('crm::components.leads.edit');
        }

        return view('crm::components.leads.show');
    }
}
