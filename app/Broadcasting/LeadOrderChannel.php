<?php

namespace App\Broadcasting;

use App\LeadsOrder;
use App\User;

class LeadOrderChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param \App\User $user
     *
     * @return array|bool
     */
    public function join(User $user, LeadsOrder $order)
    {
        return $user->can('view', $order);
    }
}
