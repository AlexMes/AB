<?php

namespace App\VK\Events\Profiles;

use App\VK\Models\Profile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Creating
{
    use Dispatchable, SerializesModels;

    /**
     * @var Profile
     */
    public Profile $profile;

    /**
     * Create a new event instance.
     *
     * @param Profile $profile
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }
}
