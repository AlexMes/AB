<?php

namespace App\Broadcasting;

use App\User;

class UserChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User $user
     * @param $id
     *
     * @return array|bool
     */
    public function join(User $user, $id)
    {
        return (int) $user->id === (int) $id || $user->isAdmin();
    }
}
