<?php

namespace App\Deluge\Events\Unity\Organizations;

use App\UnityOrganization;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Created
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var UnityOrganization
     */
    public UnityOrganization $organization;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UnityOrganization $organization)
    {
        $this->organization = $organization;
    }
}
