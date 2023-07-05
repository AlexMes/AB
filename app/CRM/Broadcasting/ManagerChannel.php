<?php

namespace App\CRM\Broadcasting;

class ManagerChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param \App\User $user
     * @param mixed     $manager
     *
     * @return array|bool
     */
    public function join($user = null, $manager = null)
    {
        return (int) $user->id === (int) $manager;
    }
}
