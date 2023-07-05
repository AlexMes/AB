<?php

namespace App\Rules;

use App\LeadOrderRoute;
use Illuminate\Contracts\Validation\Rule;

class RouteAssignmentsDistribution implements Rule
{
    /**
     * @var LeadOrderRoute
     */
    protected LeadOrderRoute $route;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(LeadOrderRoute $route)
    {
        $this->route = $route;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value !== 'distribute'
            || $this->route->assignments->count() === 0
            || $this->route->order->getManagers($this->route->offer_id, $this->route->manager_id)->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No managers found to transfer.';
    }
}
