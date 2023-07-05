<?php

namespace App\Events\Domains;

use App\Domain;

class Saved
{
    /**
     * @var \App\Domain
     */
    public $domain;

    /**
     * Saving constructor.
     *
     * @param \App\Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }
}
