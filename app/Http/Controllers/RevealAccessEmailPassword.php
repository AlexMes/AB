<?php

namespace App\Http\Controllers;

use App\Access;
use Illuminate\Http\Request;

class RevealAccessEmailPassword extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Access $access
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return string
     */
    public function __invoke(Access $access)
    {
        $this->authorize('view', $access);

        return $access->getEmailPassword();
    }
}
