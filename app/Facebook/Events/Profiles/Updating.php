<?php

namespace App\Facebook\Events\Profiles;

use App\Facebook\Profile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updating
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var Profile
     */
    public $profile;

    /**
     * Updating constructor.
     *
     * @param Profile $profile
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }
}
