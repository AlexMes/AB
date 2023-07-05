<?php

namespace App\Broadcasting;

use App\User;

class GoogleApp
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param \App\User      $user
     * @param \App\GoogleApp $app
     *
     * @return void
     */
    public function join(User $user, \App\GoogleApp $app)
    {
        return $user->can('view', $app);
    }
}
