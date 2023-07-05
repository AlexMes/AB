<?php

namespace App\Broadcasting;

use App\Facebook\AdSet;
use App\User;

class Adsets
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User  $user
     * @param AdSet $adset
     *
     * @return array|bool
     */
    public function join(User $user, AdSet $adset)
    {
        return $user->can('view', $adset);
    }
}
