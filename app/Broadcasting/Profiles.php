<?php

namespace App\Broadcasting;

use App\Facebook\Profile;
use App\User;

class Profiles
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User    $user
     * @param Profile $profile
     *
     * @return array|bool
     */
    public function join(User $user, Profile $profile)
    {
        return $user->can('view', $profile);
    }
}
