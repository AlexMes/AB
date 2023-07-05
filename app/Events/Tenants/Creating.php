<?php

namespace App\Events\Tenants;

use App\CRM\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Creating
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Tenant
     */
    public Tenant $tenant;

    /**
     * Create a new event instance.
     *
     * @param Tenant $tenant
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }
}
