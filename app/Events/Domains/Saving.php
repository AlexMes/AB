<?php

namespace App\Events\Domains;

use App\Domain;

class Saving
{
    /**
     * @var Domain
     */
    public Domain $domain;

    /**
     * Create a new event instance.
     *
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }
}
