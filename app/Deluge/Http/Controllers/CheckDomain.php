<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Domain;
use App\Http\Controllers\Controller;

class CheckDomain extends Controller
{
    public function __invoke(Domain $domain)
    {
        $domain->check();

        return redirect()->back();
    }
}
