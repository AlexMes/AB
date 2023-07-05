<?php

namespace App\Broadcasting;

use App\User;

class AllowEveryone
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @return array|bool
     */
    public function join()
    {
        return true;
    }
}
